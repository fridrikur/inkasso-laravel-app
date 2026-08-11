<?php

namespace App\Livewire\Konsulenter;

use Livewire\Component;
use Livewire\WithPagination;

use App\Models\Konsulenter;
use App\Models\HovedKonsulent;
use App\Models\SkjultKonsulent;
use App\Models\NotifikationsKonsulent;

use App\Services\KonsulentService;
use App\Services\ToastService;

use Illuminate\Support\Facades\DB;
use App\Traits\HasCrudModal; // 🟢 Vores genbrugelige Trait

class ManageKonsulenter extends Component
{
    use WithPagination;
    use HasCrudModal; // 🟢 Implementerer modal-tilstande

    private function service(): KonsulentService
    {
        return app(KonsulentService::class);
    }

    public string $search = '';
    public int $perPage = 10;
    public string $activeRoleTab = 'alle';

    // Modal felter
    public ?Konsulenter $activeKonsulent = null;
    public string $modalNavn = '';
    public string $modalEmail = '';
    public ?string $modalTlf = null;
    public ?string $modalMobil = null;

    public bool $modalIsHoved = false;
    public bool $modalIsSkjult = false;
    public bool $modalIsNotifikation = false;

    // 🟢 Påkrævede metoder fra HasCrudModal Traiten
    public function resetForm(): void
    {
        $this->reset([
            'activeKonsulent',
            'modalNavn',
            'modalEmail',
            'modalTlf',
            'modalMobil',
            'modalIsHoved',
            'modalIsSkjult',
            'modalIsNotifikation',
        ]);

        $this->resetValidation();
    }

    public function loadItemData($id): void
    {
        $k = Konsulenter::findOrFail($id);

        $this->activeKonsulent = $k;
        $this->modalNavn = $k->navn;
        $this->modalEmail = $k->email;
        $this->modalTlf = $k->tlf;
        $this->modalMobil = $k->mobil;

        $this->modalIsHoved = HovedKonsulent::current()?->id === $k->id;
        $this->modalIsSkjult = SkjultKonsulent::has($k);
        $this->modalIsNotifikation = NotifikationsKonsulent::has($k);
    }

    /*
    |--------------------------------------------------------------------------
    | Handlinger
    |--------------------------------------------------------------------------
    */

    public function updatedSearch() { $this->resetPage(); }
    public function updatedActiveRoleTab() { $this->resetPage(); }

    public function setRoleTab(string $role)
    {
        $this->activeRoleTab = $role;
        $this->resetPage();
    }

    public function save()
    {
        $this->validate([
            'modalNavn' => 'required|string|max:255',
            'modalEmail' => 'required|email|max:255',
        ]);

        DB::transaction(function () {
            $konsulent = $this->service()->save(
                $this->activeKonsulent,
                [
                    'navn'  => $this->modalNavn,
                    'email' => $this->modalEmail,
                    'tlf'   => $this->modalTlf,
                    'mobil' => $this->modalMobil,
                ]
            );

            $this->service()->syncRoles(
                $konsulent,
                [
                    'hoved' => $this->modalIsHoved,
                    'skjult' => $this->modalIsSkjult,
                    'notifikation' => $this->modalIsNotifikation,
                ]
            );
        });

        $this->closeFormModal();

        $this->dispatch(
            'notify',
            ...app(ToastService::class)->success('Konsulent gemt')
        );
    }

    // 🟢 Sletning tilpasset HasCrudModal ($deletingId)
    /**
     * Håndterer både åbning af modal (med $id) og selve sletningen (uden $id)
     */
    public function confirmDelete($id = null): void
    {
        // 1. Åbn modal ved tryk på tabellen
        if ($id) {
            $this->deletingId = $id; // $deletingId kommer direkte fra HasCrudModal traiten
            $this->showDeleteModal = true;
            return;
        }

        // 2. Udfør sletning ved bekræftelse i modal
        if (!$this->deletingId) {
            $this->cancelDelete();
            return;
        }

        $k = Konsulenter::find($this->deletingId);

        if (!$k) {
            $this->cancelDelete();
            return;
        }

        if ($k->sager()->exists()) {
            $this->cancelDelete();
            
            $this->dispatch('toast', [
                'message' => 'Kan ikke slette konsulent med aktive sager.',
                'type'    => 'error'
            ]);
            return;
        }

        $this->service()->delete($k);
        $this->cancelDelete();

        $this->dispatch('toast', [
            'message' => 'Konsulenten blev slettet.',
            'type'    => 'success'
        ]);
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->deletingId = null;
    }

    public function render()
    {
        $query = Konsulenter::query()
            ->withExists(['skjultRole', 'notifikationRole']);

        if ($this->activeRoleTab === 'hoved') {
            $query->where('id', HovedKonsulent::current()?->id);
        }

        if ($this->activeRoleTab === 'notif') {
            $query->whereIn('id', NotifikationsKonsulent::pluck('notifikations_konsulent_id'));
        }

        if ($this->activeRoleTab === 'skjult') {
            $query->whereIn('id', SkjultKonsulent::pluck('skjult_konsulent_id'));
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('navn', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%");
            });
        }

        return view('livewire.konsulenter.manage-konsulenter', [
            'konsulenter' => $query->orderBy('navn')->paginate($this->perPage),
            'hovedKonsulent' => HovedKonsulent::current(),
            'notifikationCount' => NotifikationsKonsulent::count(),
            'skjultCount' => SkjultKonsulent::count(),
        ]);
    }
}