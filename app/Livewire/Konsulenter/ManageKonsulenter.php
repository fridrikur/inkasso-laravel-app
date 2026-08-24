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
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Hash;


// Overførsels-felter (tilsvarende kreditor-flowet)

class ManageKonsulenter extends Component
{
    use WithPagination;
    use HasCrudModal; // 🟢 Implementerer modal-tilstande

    // 🟢 Tilføj listeners her, så tabellen opdaterer sig automatisk ved slet
    protected $listeners = [
        'refresh-table' => '$refresh',
        'execute-deletion' => 'handleDeletion', // Lytter på det globale slette-signal
    ];

    public ?Konsulenter $konsulentToTransferFrom = null;
    public ?int $transferToKonsulentId = null;
    public bool $showStandaloneTransferModal = false;
    
    public int $userHasSagerCount = 0;

    public function handleDeletion($action, $id)
    {
        // Hvis eventet er ment til netop denne komponent (eller matcher vores action)
        if ($action === 'deleteKonsulent') {
            $this->deleteKonsulent($id);
        }
    }

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

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->deletingId = null;
    }

    public function openTransferModal(int $id): void
    {
        $this->konsulentToTransferFrom = Konsulenter::with(['sager'])->withCount('sager')->findOrFail($id);
        $this->transferToKonsulentId = null;
        $this->showStandaloneTransferModal = true;
    }

    public function transferAndClose(): void
    {
        $this->validate([
            'transferToKonsulentId' => 'required|exists:konsulenters,id',
        ]);

        if (!$this->konsulentToTransferFrom) {
            return;
        }

        DB::transaction(function () {
            // Flyt alle sager til den nye konsulent
            $this->konsulentToTransferFrom->sager()->update([
                'konsulent_id' => $this->transferToKonsulentId // Ret feltnavnet hvis det hedder noget andet i din database (f.eks. sagsbehandler_id el.lign.)
            ]);

            // Slet derefter den gamle konsulent
            $this->service()->delete($this->konsulentToTransferFrom);
        });

        $this->showStandaloneTransferModal = false;
        $this->konsulentToTransferFrom = null;
        $this->transferToKonsulentId = null;

        $this->dispatch('toast', [
            'message' => 'Sagerne blev overført, og konsulenten blev slettet.',
            'type'    => 'success'
        ]);
    }

    public function cancelTransfer(): void
    {
        $this->showStandaloneTransferModal = false;
        $this->konsulentToTransferFrom = null;
        $this->transferToKonsulentId = null;
    }

    public function render()
    {
        $query = Konsulenter::query()
            ->withExists(['skjultRole', 'notifikationRole'])->withCount('sager');

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

    // Slet-håndtering
    public function confirmDelete(int $id): void
    {
        $konsulent = \App\Models\Konsulenter::findOrFail($id);

        // Tjek om konsulenten har aktive sager
        $sagerCount = \App\Models\Sager::whereHas('sagerkonsulent', function ($q) use ($konsulent) {
            $q->where('konsulenters.id', $konsulent->id);
        })->count();

        if ($sagerCount > 0) {
            $this->konsulentToTransferFrom = $konsulent;
            $this->userHasSagerCount = $sagerCount;
            $this->showStandaloneTransferModal = true;

            $this->dispatch('toast', [
                'message' => "Konsulenten har {$sagerCount} aktive sager. Overfør venligst sagerne først.",
                'type'    => 'warning'
            ]);
            return;
        }

        // Ingen sager -> Åbn den lokale slette-modal
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }


    public function deleteKonsulent(): void
    {
        if (! $this->deletingId) return;

        $konsulent = \App\Models\Konsulenter::find($this->deletingId);
        if ($konsulent) {
            $konsulent->delete();

            $this->dispatch('toast', [
                'message' => 'Konsulenten er slettet.',
                'type'    => 'success'
            ]);
        }

        $this->cancelDelete();
    }

}