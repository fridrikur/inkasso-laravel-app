<?php

namespace App\Livewire\Kreditorer;

use App\Models\Kreditorer;
use App\Models\Sagsbehandler;
use App\Models\User;
use App\Models\SystemSetting;
use App\Services\KreditorManagementService;
use App\Services\KreditorTransferService;
use App\Traits\HasCrudModal;

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

use Livewire\Component;

class ManageKreditor extends Component
{
    use HasCrudModal;

    public ?Kreditorer $kreditor = null;
    
    // NY VARIABEL: Holder styr på om kreditoren lige er oprettet
    public bool $kreditor_lige_oprettet = false;

    protected KreditorManagementService $management;
    protected KreditorTransferService $transfer;

    /*
    |--------------------------------------------------------------------------
    | Kreditor form
    |--------------------------------------------------------------------------
    */

    public string $navn = '';
    public ?string $lotusID = null;

    /*
    |--------------------------------------------------------------------------
    | User modal
    |--------------------------------------------------------------------------
    */

    public bool $showUserModal = false;
    public ?User $activeUser = null;
    public string $userName = '';
    public string $userEmail = '';
    public ?string $userPassword = null;
    public int $sagerCount = 0;

    /*
    |--------------------------------------------------------------------------
    | Sagsbehandler modal
    |--------------------------------------------------------------------------
    */

    public bool $showSagsModal = false;
    public ?Sagsbehandler $activeSagsbehandler = null;
    public string $modalNavn = '';
    public ?string $modalEmail = null;
    public ?string $modalTlf = null;
    public ?string $modalMobil = null;

    /*
    |--------------------------------------------------------------------------
    | Delete / transfer
    |--------------------------------------------------------------------------
    */

    public string $securityCode = '';
    public ?int $transferToKreditorId = null;
    public $transferTargets = [];

    /*
    |--------------------------------------------------------------------------
    | Dependency injection
    |--------------------------------------------------------------------------
    */

    public function boot(
        KreditorManagementService $management,
        KreditorTransferService $transfer
    ): void {
        $this->management = $management;
        $this->transfer = $transfer;
    }

    /*
    |--------------------------------------------------------------------------
    | Mount
    |--------------------------------------------------------------------------
    */

    public function mount(Kreditorer $kreditor): void
    {
        $this->kreditor = $kreditor;
        $this->loadKreditorData();

        if (request()->query('oprettet') == 1) {
            $this->kreditor_lige_oprettet = true;
        }
    }

    public function resetForm(): void
    {
        $this->navn = '';
        $this->lotusID = null;
        $this->resetValidation();
    }

    public function loadItemData($id): void
    {
        $kreditor = Kreditorer::findOrFail($id);
        $this->navn = $kreditor->navn;
        $this->lotusID = $kreditor->lotusID;
    }

    public function loadKreditorData(): void
    {
        if (!$this->kreditor?->exists) {
            return;
        }

        $this->kreditor = Kreditorer::query()
            ->with([
                'users:id,name,email',
                'sagsbehandlere:id,navn,email,tlf,mobil',
                'sager' => fn ($query) =>
                    $query
                        ->with('sagerdebitor')
                        ->latest()
                        ->take(10),
            ])
            ->withCount('sager')
            ->findOrFail($this->kreditor->id);

        $this->sagerCount = $this->kreditor->sager_count ?? 0;
    }

    /*
    |--------------------------------------------------------------------------
    | Save creditor
    |--------------------------------------------------------------------------
    */

    public function save(): void
    {
        $this->validate([
            'navn' => ['required', 'string', 'max:255'],
            'lotusID' => ['nullable', 'string', 'max:255'],
        ]);

        if ($this->editingId) {
            $kreditor = Kreditorer::findOrFail($this->editingId);

            $this->management->update(
                $kreditor,
                [
                    'navn' => $this->navn,
                    'lotusID' => $this->lotusID,
                ]
            );

            $message = 'Kreditor blev opdateret.';
            $this->closeFormModal();
            $this->loadKreditorData();

            $this->dispatch('toast', message: $message, type: 'success');

        } else {
            // Opret ny via vores fælles KreditorManagementService
            $nyKreditor = $this->management->create([
                'navn' => $this->navn,
                'lotusID' => $this->lotusID,
            ]);

            $this->closeFormModal();

            // Omdiriger til detaljesiden med query-parameteren for notifikationen
            $this->redirect(route('kreditor.manage', $nyKreditor->id) . '?oprettet=1', navigate: true);
        }
    }
    
    public function requestDelete(): void
    {
        $this->resetValidation();
        $this->securityCode = '';
        $this->transferToKreditorId = null;
        $this->transferTargets = Kreditorer::query()->whereKeyNot($this->kreditor->id)->orderBy('navn')->get();
        $this->showDeleteModal = true;
    }

    public function confirmDelete(): void
    {
        $this->resetValidation();
        if (!$this->kreditor?->exists) { return; }

        if ($this->kreditor->sager()->exists()) {
            $expectedCode = SystemSetting::where('key', 'global_unlock_code')->value('value');
            if (!$expectedCode || !Hash::check($this->securityCode, $expectedCode)) {
                $this->addError('securityCode', 'Forkert sikkerhedskode.');
                return;
            }
            if (!$this->transferToKreditorId) {
                $this->addError('transferToKreditorId', 'Vælg en modtager-kreditor.');
                return;
            }
            if ((int) $this->transferToKreditorId === (int) $this->kreditor->id) {
                $this->addError('transferToKreditorId', 'Du kan ikke overføre sager til den samme kreditor.');
                return;
            }
            $target = Kreditorer::findOrFail($this->transferToKreditorId);
            $this->transfer->transferSager($this->kreditor, $target);
        }

        $this->management->delete($this->kreditor);

        session()->flash('toast', [
            'type' => 'success',
            'message' => 'Kreditor blev slettet.',
        ]);

        $this->redirect(route('kreditorer.index'), navigate: true);
    }

    public function openUserModal(?int $id = null): void
    {
        $this->resetValidation();
        if ($id) {
            $this->activeUser = User::findOrFail($id);
            $this->userName = $this->activeUser->name;
            $this->userEmail = $this->activeUser->email;
            $this->userPassword = null;
        } else {
            $this->activeUser = null;
            $this->userName = '';
            $this->userEmail = '';
            $this->userPassword = null;
        }
        $this->showUserModal = true;
    }

    public function saveUser(): void
    {
        $this->validate([
            'userName' => ['required', 'string', 'max:255'],
            'userEmail' => ['required', 'email', Rule::unique('users', 'email')->ignore($this->activeUser?->id)],
            'userPassword' => $this->activeUser ? ['nullable', 'min:8'] : ['required', 'min:8'],
        ]);

        if ($this->activeUser) {
            $this->activeUser->update([
                'name' => $this->userName,
                'email' => $this->userEmail,
                ...(filled($this->userPassword) ? ['password' => Hash::make($this->userPassword)] : []),
            ]);
            $user = $this->activeUser;
        } else {
            $user = User::create([
                'name' => $this->userName,
                'email' => $this->userEmail,
                'password' => Hash::make($this->userPassword),
            ]);
        }

        $user->kreditorer()->sync([$this->kreditor->id]);
        $this->showUserModal = false;
        $this->loadKreditorData();
        $this->dispatch('toast', message: 'Bruger blev gemt.', type: 'success');
    }

    public function detachUser(int $id): void
    {
        $user = User::findOrFail($id);
        $user->kreditorer()->detach($this->kreditor->id);
        $this->loadKreditorData();
        $this->dispatch('toast', message: 'Bruger blev fjernet fra kreditoren.', type: 'success');
    }

    public function openSagsbehandlerModal(?int $id = null): void
    {
        $this->resetValidation();
        if ($id) {
            $this->activeSagsbehandler = Sagsbehandler::findOrFail($id);
            $this->modalNavn = $this->activeSagsbehandler->navn;
            $this->modalEmail = $this->activeSagsbehandler->email;
            $this->modalTlf = $this->activeSagsbehandler->tlf;
            $this->modalMobil = $this->activeSagsbehandler->mobil;
        } else {
            $this->activeSagsbehandler = null;
            $this->modalNavn = '';
            $this->modalEmail = null;
            $this->modalTlf = null;
            $this->modalMobil = null;
        }
        $this->showSagsModal = true;
    }

    public function saveSagsbehandler(): void
    {
        $this->validate([
            'modalNavn' => ['required', 'string', 'max:255'],
            'modalEmail' => ['required', 'email', Rule::unique('sagsbehandlers', 'email')->ignore($this->activeSagsbehandler?->id)],
            'modalTlf' => ['nullable', 'string', 'max:50'],
            'modalMobil' => ['nullable', 'string', 'max:50'],
        ]);

        if ($this->activeSagsbehandler) {
            $sagsbehandler = $this->activeSagsbehandler;
            $sagsbehandler->update([
                'navn' => $this->modalNavn,
                'email' => $this->modalEmail,
                'tlf' => $this->modalTlf,
                'mobil' => $this->modalMobil,
            ]);
        } else {
            $sagsbehandler = Sagsbehandler::create([
                'navn' => $this->modalNavn,
                'email' => $this->modalEmail,
                'tlf' => $this->modalTlf,
                'mobil' => $this->modalMobil,
            ]);
        }

        $sagsbehandler->kreditorer()->sync([$this->kreditor->id]);
        $this->showSagsModal = false;
        $this->loadKreditorData();
        $this->dispatch('toast', message: 'Sagsbehandler blev gemt.', type: 'success');
    }

    public function detachSagsbehandler(int $id): void
    {
        $sagsbehandler = Sagsbehandler::findOrFail($id);
        $sagsbehandler->kreditorer()->detach($this->kreditor->id);
        $this->loadKreditorData();
        $this->dispatch('toast', message: 'Sagsbehandler blev fjernet fra kreditoren.', type: 'success');
    }

    public function closeModals(): void
    {
        $this->showUserManager = false;
        $this->showUserModal = false;
        $this->showSagsModal = false;
        $this->showDeleteModal = false;
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.kreditorer.manage-kreditor');
    }
}