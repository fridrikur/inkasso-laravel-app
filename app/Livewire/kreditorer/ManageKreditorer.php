<?php

namespace App\Livewire\Kreditorer;

use App\Models\Kreditorer;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class ManageKreditorer extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filter = 'all'; // all, active_cases, no_cases, with_users

    public bool $showDeleteModal = false;
    public bool $showStandaloneTransferModal = false;

    public ?Kreditorer $kreditorToDelete = null;
    public ?Kreditorer $kreditorToTransferFrom = null;

    public ?int $transferToKreditorId = null;
    public string $securityCode = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'filter' => ['except' => 'all'],
    ];

    #[On('kreditor-saved')]
    public function refreshTable(): void
    {
        // Genberegner automatisk render()
    }

    public function setFilter(string $filter): void
    {
        $this->filter = $filter;
        $this->resetPage();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Kreditorer::query()
            ->withCount(['sager', 'users', 'sagsbehandlere']);

        // Søgefiltrering
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('navn', 'like', '%' . $this->search . '%')
                  ->orWhere('lotusID', 'like', '%' . $this->search . '%')
                  ->orWhere('id', $this->search);
            });
        }

        // Dynamiske status-faner
        match ($this->filter) {
            'active_cases' => $query->has('sager'),
            'no_cases'     => $query->doesntHave('sager'),
            'with_users'   => $query->has('users'),
            default        => null,
        };

        $kreditorer = $query->orderBy('navn')->paginate(15);

        // Statistik sammentælling
        $totalKreditorer = Kreditorer::count();
        $medSagerCount   = Kreditorer::has('sager')->count();
        $udenSagerCount  = Kreditorer::doesntHave('sager')->count();
        $medBrugereCount = Kreditorer::has('users')->count();

        return view('livewire.kreditorer.manage-kreditorer', [
            'kreditorer'       => $kreditorer,
            'totalKreditorer'  => $totalKreditorer,
            'medSagerCount'    => $medSagerCount,
            'udenSagerCount'   => $udenSagerCount,
            'medBrugereCount'  => $medBrugereCount,
            'transferTargets'  => $this->kreditorToDelete || $this->kreditorToTransferFrom
                ? Kreditorer::where('id', '!=', $this->kreditorToDelete?->id ?? $this->kreditorToTransferFrom?->id)->orderBy('navn')->get()
                : collect(),
        ]);
    }

    public function opretnykreditor(): void
    {
        $this->dispatch('open-kreditor-modal');
    }

    // -------------------------------------------------
    // Slette- & Overførselslogik
    // -------------------------------------------------
    public function requestDelete(int $id): void
    {
        $this->kreditorToDelete = Kreditorer::with(['sager'])->withCount('sager')->findOrFail($id);
        $this->transferToKreditorId = null;
        $this->securityCode = '';
        $this->showDeleteModal = true;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->kreditorToDelete = null;
        $this->transferToKreditorId = null;
    }

    // 🟢 BEKRÆFT SLETTELSE MED EVENTUEL SAGSOVERFØRSEL
    public function confirmDelete(): void
    {
        if (! $this->kreditorToDelete) {
            return;
        }

        // Tjek om sager skal overføres før sletning
        if ($this->kreditorToDelete->sager_count > 0) {
            if (! $this->transferToKreditorId) {
                $this->dispatch('toast', [
                    'message' => 'Vælg venligst en modtager-kreditor, som sagerne skal overføres til.',
                    'type'    => 'error'
                ]);
                return;
            }

            $targetKreditor = Kreditorer::findOrFail($this->transferToKreditorId);

            // Overfør alle sager til modtager-kreditoren
            foreach ($this->kreditorToDelete->sager as $sag) {
                $sag->kreditorer()->detach($this->kreditorToDelete->id);
                $sag->kreditorer()->syncWithoutDetaching([$targetKreditor->id]);
            }
        }

        // SoftDelete kreditor
        $this->kreditorToDelete->delete();

        $this->dispatch('toast', [
            'message' => 'Kreditor blev slettet.',
            'type'    => 'success'
        ]);

        $this->cancelDelete();
        $this->resetPage();
    }

    // -------------------------------------------------
    // Standalone overførsel af sager
    // -------------------------------------------------
    public function openTransferModal(int $id): void
    {
        $this->kreditorToTransferFrom = Kreditorer::with(['sager'])->withCount('sager')->findOrFail($id);
        $this->transferToKreditorId = null;
        $this->showStandaloneTransferModal = true;
    }

    public function closeTransferModal(): void
    {
        $this->showStandaloneTransferModal = false;
        $this->kreditorToTransferFrom = null;
        $this->transferToKreditorId = null;
    }

    // 🟢 UDFØR KUN OVERFØRSEL AF SAGER
    public function executeStandaloneTransfer(): void
    {
        if (! $this->kreditorToTransferFrom || ! $this->transferToKreditorId) {
            $this->dispatch('toast', [
                'message' => 'Vælg venligst en modtager-kreditor.',
                'type'    => 'error'
            ]);
            return;
        }

        $targetKreditor = Kreditorer::findOrFail($this->transferToKreditorId);

        foreach ($this->kreditorToTransferFrom->sager as $sag) {
            $sag->kreditorer()->detach($this->kreditorToTransferFrom->id);
            $sag->kreditorer()->syncWithoutDetaching([$targetKreditor->id]);
        }

        $this->dispatch('toast', [
            'message' => 'Sagerne blev overført til ' . $targetKreditor->navn,
            'type'    => 'success'
        ]);

        $this->closeTransferModal();
        $this->resetPage();
    }
}