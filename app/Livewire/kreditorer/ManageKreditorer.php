<?php

namespace App\Livewire\Kreditorer;

use App\Models\Kreditorer;
use App\Models\SystemSetting;
use App\Services\KreditorTransferService;
use App\Services\KreditorManagementService;
use App\Traits\HasCrudModal;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class ManageKreditorer extends Component
{
    use WithPagination;
    use HasCrudModal;

    protected KreditorTransferService $transfer;
    protected KreditorManagementService $management;

    public bool $readyToLoad = false;

    public string $navn = '';
    public ?string $lotusID = null;

    public function boot(
        KreditorTransferService $transfer,
        KreditorManagementService $management
    ): void {
        $this->transfer = $transfer;
        $this->management = $management;
    }

    /*
    |--------------------------------------------------------------------------
    | Search / filters
    |--------------------------------------------------------------------------
    */

    public string $search = '';
    public string $filter = 'all';

    /*
    |--------------------------------------------------------------------------
    | Delete / transfer modal properties (fra ManageKreditor)
    |--------------------------------------------------------------------------
    */

    public bool $showDeleteModal = false;
    public ?Kreditorer $kreditorToDelete = null;
    public int $sagerCount = 0;
    public string $securityCode = '';
    public ?int $transferToKreditorId = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'filter' => ['except' => 'all'],
    ];

    public function mount(): void
    {
        //
    }

    public function resetForm(): void
    {
        $this->editingId = null;
    }

    public function opretnykreditor(): void
    {
        $this->openCreateModal();
        $this->dispatch('open-kreditor-modal');
    }

    public function setFilter(string $filter): void
    {
        $allowed = ['all', 'active_cases', 'no_cases', 'with_users'];

        if (! in_array($filter, $allowed, true)) {
            $filter = 'all';
        }

        $this->filter = $filter;
        $this->resetPage();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    #[On('kreditor-saved')]
    #[On('kreditor-updated')]
    #[On('refresh-table')]
    public function refreshTable(): void
    {
        $this->resetPage();
    }

    /*
    |--------------------------------------------------------------------------
    | Delete & Transfer (Tilpasset præcis som i ManageKreditor)
    |--------------------------------------------------------------------------
    */

    public function closeModals(): void
    {
        $this->showDeleteModal = false;
        $this->kreditorToDelete = null;
        $this->resetValidation();
    }

    public function confirmDelete(): void
    {
        $this->resetValidation();

        if (! $this->kreditorToDelete?->exists) {
            return;
        }

        if ($this->kreditorToDelete->sager()->exists()) {
            $expectedCode = SystemSetting::where('key', 'global_unlock_code')->value('value');

            if (! $expectedCode || ! Hash::check($this->securityCode, $expectedCode)) {
                $this->addError('securityCode', 'Forkert sikkerhedskode.');
                return;
            }

            if (! $this->transferToKreditorId) {
                $this->addError('transferToKreditorId', 'Vælg en modtager-kreditor.');
                return;
            }

            if ((int) $this->transferToKreditorId === (int) $this->kreditorToDelete->id) {
                $this->addError('transferToKreditorId', 'Du kan ikke overføre sager til den samme kreditor.');
                return;
            }

            $target = Kreditorer::findOrFail($this->transferToKreditorId);

            $this->transfer->transferSager(
                $this->kreditorToDelete,
                $target
            );
        }

        $this->management->delete($this->kreditorToDelete);

        $this->closeModals();

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Kreditoren blev slettet succesfuldt.',
        ]);

        $this->resetPage();
    }

    /*
    |--------------------------------------------------------------------------
    | Render
    |--------------------------------------------------------------------------
    */

    public function render()
    {
        $query = Kreditorer::query();

        if ($this->search !== '') {
            $search = trim($this->search);

            $query->where(function ($q) use ($search) {
                $q->where('navn', 'like', "%{$search}%")
                    ->orWhere('lotusID', 'like', "%{$search}%");

                if (is_numeric($search)) {
                    $q->orWhere('id', (int) $search);
                }
            });
        }

        match ($this->filter) {
            'active_cases' => $query->has('sager'),
            'no_cases' => $query->doesntHave('sager'),
            'with_users' => $query->has('users'),
            default => null,
        };

        $kreditorer = $query
            ->orderBy('navn')
            ->paginate(15);

        $totalKreditorer = Kreditorer::count();
        $medSagerCount = Kreditorer::has('sager')->count();
        $udenSagerCount = Kreditorer::doesntHave('sager')->count();
        $medBrugereCount = Kreditorer::has('users')->count();

        $transferTargets = $this->kreditorToDelete
            ? Kreditorer::whereKeyNot($this->kreditorToDelete->id)->orderBy('navn')->get()
            : collect();

        return view(
            'livewire.kreditorer.manage-kreditorer',
            [
                'kreditorer' => $kreditorer,
                'totalKreditorer' => $totalKreditorer,
                'medSagerCount' => $medSagerCount,
                'udenSagerCount' => $udenSagerCount,
                'medBrugereCount' => $medBrugereCount,
                'transferTargets' => $transferTargets,
            ]
        );
    }

    public function requestDelete(int $id): void
    {
        $this->resetValidation();

        $this->securityCode = '';
        $this->transferToKreditorId = null;

        $this->kreditorToDelete = Kreditorer::withCount('sager')->findOrFail($id);
        $this->sagerCount = $this->kreditorToDelete->sager_count ?? 0;

        $this->showDeleteModal = true;
    }

    public function loadItemData($id): void
    {
        $kreditor = Kreditorer::findOrFail($id);

        $this->navn = $kreditor->navn;
        $this->lotusID = $kreditor->lotusID;
    }

    public function editKreditor(int $id): void
    {
        $this->dispatch('open-edit-modal', id: $id)->to('kreditor.kreditor-form-modal');
    }

    public function loadKreditorer()
    {
        $this->readyToLoad = true;
    }
}