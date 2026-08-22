<?php

namespace App\Livewire\Kreditorer;

use App\Models\Kreditorer;
use App\Models\User;
use App\Models\Sagsbehandler;
use Livewire\Component;
use Illuminate\Support\Facades\Hash;

class ManageKreditor extends Component
{
    public Kreditorer $kreditor;

    // Modal synlighed
// Modal synlighed - sikring af ensartede navne
    public bool $showDeleteModal = false;
    public bool $showUserModal = false;
    public bool $showSagsbehandlerModal = false; // Ændret fra showSagsModal
    // Slette/overførsel egenskaber
    public ?int $transferToKreditorId = null;
    public string $securityCode = '';

    // Cachede tællere for at undgå Livewire-loops og frysning
    public int $sagerCount = 0;
    public int $usersCount = 0;
    public int $sagsbehandlereCount = 0;

    // Bruger modal felter
    public ?User $activeUser = null;
    public string $userName = '';
    public string $userEmail = '';
    public string $userPassword = '';

    // Sagsbehandler modal felter
    public ?Sagsbehandler $activeSagsbehandler = null;
    public string $modalNavn = '';
    public string $modalEmail = '';
    public string $modalTlf = '';
    public string $modalMobil = '';
    
    public ?int $deletingId = null;
    
    protected $listeners = [
        'kreditor-updated' => 'loadRelations', // Kalder loadRelations i stedet for blot $refresh
    ];

    
    public function mount($kreditor)
    {
        $kreditorId = $kreditor instanceof Kreditorer ? $kreditor->id : $kreditor;
        $this->kreditor = Kreditorer::withTrashed()->findOrFail($kreditorId);
        $this->loadRelations();
    }

    public function render()
    {
        $transferTargets = $this->showDeleteModal
            ? Kreditorer::where('id', '!=', $this->kreditor->id)->orderBy('navn')->get()
            : collect();

        return view('livewire.kreditorer.manage-kreditor', [
            'transferTargets' => $transferTargets,
        ]);
    }

    // =========================================================================
    // MODAL LUKNING & NULSTILLING
    // =========================================================================
    public function closeModals(): void
    {
        $this->showDeleteModal = false;
        $this->showUserModal = false;
        $this->showSagsbehandlerModal = false; // Matcher navnet

        $this->reset([
            'activeUser', 'userName', 'userEmail', 'userPassword',
            'activeSagsbehandler', 'modalNavn', 'modalEmail', 'modalTlf', 'modalMobil',
            'transferToKreditorId', 'securityCode'
        ]);
    }

    public function openSagsbehandlerModal(?int $id = null): void
    {
        $this->reset(['modalNavn', 'modalEmail', 'modalTlf', 'modalMobil']);

        if ($id) {
            $this->activeSagsbehandler = Sagsbehandler::findOrFail($id);
            $this->modalNavn  = $this->activeSagsbehandler->navn;
            $this->modalEmail = $this->activeSagsbehandler->email ?? '';
            $this->modalTlf   = $this->activeSagsbehandler->tlf ?? '';
            $this->modalMobil = $this->activeSagsbehandler->mobil ?? '';
        } else {
            $this->activeSagsbehandler = null;
        }

        $this->showSagsbehandlerModal = true; // Sætter den korrekte variabel
    }

    public function openUserModal(?int $userId = NULL): void
    {
        $this->dispatch('open-user-modal', userId: $userId);
    }

    public function saveUser(): void
    {
        $userId = $this->activeUser?->id;

        $rules = [
            'userName'  => 'required|string|max:255',
            'userEmail' => 'required|email|max:255|unique:users,email,' . ($userId ?? 'NULL'),
        ];

        if (!$userId) {
            $rules['userPassword'] = 'required|string|min:8';
        } else {
            $rules['userPassword'] = 'nullable|string|min:8';
        }

        $this->validate($rules);

        if ($this->activeUser) {
            $this->activeUser->update([
                'name'  => $this->userName,
                'email' => $this->userEmail,
            ]);

            if (!empty($this->userPassword)) {
                $this->activeUser->update([
                    'password' => Hash::make($this->userPassword),
                ]);
            }

            $toastMsg = 'Brugeren er blevet opdateret.';
        } else {
            $user = User::create([
                'name'     => $this->userName,
                'email'    => $this->userEmail,
                'password' => Hash::make($this->userPassword),
            ]);

            if (method_exists($user, 'assignRole')) {
                $user->assignRole('Kreditor');
            }

            $this->kreditor->users()->attach($user->id);
            $toastMsg = 'Ny bruger oprettet og tilknyttet kreditoren.';
        }

        $this->dispatch('toast', ['message' => $toastMsg, 'type' => 'success']);
        $this->closeModals();
        $this->loadRelations();
    }

    public function detachUser(int $userId): void
    {
        $this->kreditor->users()->detach($userId);

        $this->dispatch('toast', ['message' => 'Brugeren er fjernet fra kreditoren.', 'type' => 'success']);
        $this->loadRelations();
    }

    // =========================================================================
    // SAGSBEHANDLER-HANDLINGER
    // =========================================================================
    
    public function saveSagsbehandler(): void
    {
        $this->validate([
            'modalNavn'  => 'required|string|max:255',
            'modalEmail' => 'nullable|email|max:255',
            'modalTlf'   => 'nullable|string|max:50',
            'modalMobil' => 'nullable|string|max:50',
        ]);

        if ($this->activeSagsbehandler) {
            $this->activeSagsbehandler->update([
                'navn'  => $this->modalNavn,
                'email' => $this->modalEmail,
                'tlf'   => $this->modalTlf,
                'mobil' => $this->modalMobil,
            ]);

            $toastMsg = 'Sagsbehandler opdateret.';
        } else {
            $sagsbehandler = Sagsbehandler::create([
                'navn'  => $this->modalNavn,
                'email' => $this->modalEmail,
                'tlf'   => $this->modalTlf,
                'mobil' => $this->modalMobil,
            ]);

            $this->kreditor->sagsbehandlere()->attach($sagsbehandler->id);
            $toastMsg = 'Sagsbehandler oprettet og tilknyttet.';
        }

        $this->dispatch('toast', ['message' => $toastMsg, 'type' => 'success']);
        $this->closeModals();
        $this->loadRelations();
    }

    // =========================================================================
    // SLETTEMODAL OG OVERFØRSEL AF SAGER
    // =========================================================================
    public function requestDelete(): void
    {
        $this->transferToKreditorId = null;
        $this->securityCode = '';
        $this->showDeleteModal = true;
    }

    public function confirmDelete(): void
    {
        if (! $this->kreditor) {
            return;
        }

        // Tjek om der er sager tilknyttet ved hjælp af vores sikre tæller
        if ($this->sagerCount > 0) {
            if (! $this->transferToKreditorId) {
                $this->dispatch('toast', [
                    'message' => 'Vælg venligst en modtager-kreditor, som sagerne skal overføres til.',
                    'type'    => 'error'
                ]);
                return;
            }

            $targetKreditor = Kreditorer::findOrFail($this->transferToKreditorId);

            foreach ($this->kreditor->sager as $sag) {
                if (method_exists($sag, 'kreditorer')) {
                    $sag->kreditorer()->detach($this->kreditor->id);
                    $sag->kreditorer()->syncWithoutDetaching([$targetKreditor->id]);
                } elseif (method_exists($sag, 'sagerkreditor')) {
                    $sag->sagerkreditor()->sync([$targetKreditor->id]);
                }
            }
        }

        $this->kreditor->delete();

        $this->dispatch('toast', [
            'message' => 'Kreditoren blev slettet succesfuldt.',
            'type'    => 'success'
        ]);

        $this->redirect(route('kreditorer.index'), navigate: true);
    }

    public function loadRelations(): void
    {
        $this->kreditor->load([
            'users',
            'sagsbehandlere',
            'sager' => fn($q) => $q->latest()->take(10)
        ]);

        $this->sagerCount = $this->kreditor->sager()->count();
        $this->usersCount = $this->kreditor->users()->count();
        $this->sagsbehandlereCount = $this->kreditor->sagsbehandlere()->count();
    }

}