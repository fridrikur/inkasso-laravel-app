<?php

namespace App\Livewire\Kreditorer;

use App\Models\Kreditorer;
use App\Models\User;
use App\Models\Sagsbehandler;
use App\Models\Sager;
use Livewire\Component;
use Illuminate\Support\Facades\Hash;

class ManageKreditor extends Component
{
    public Kreditorer $kreditor;

    // Modal synlighed
    public bool $showDeleteModal = false;
    public bool $showUserModal = false;
    public bool $showSagsModal = false;

    // Slette/overførsel egenskaber
    public ?int $transferToKreditorId = null;
    public string $securityCode = '';

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

    public function mount(Kreditorer $kreditor)
    {
        $this->kreditor = $kreditor;
        $this->loadRelations();
    }

    /**
     * Genindlæser relationer og tællere
     */
    protected function loadRelations()
    {
        $this->kreditor->loadCount(['sager', 'users', 'sagsbehandlere']);
        $this->kreditor->load([
            'users',
            'sagsbehandlere',
            'sager' => fn($q) => $q->latest()->take(10)
        ]);
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
        $this->showSagsModal = false;

        $this->reset([
            'activeUser', 'userName', 'userEmail', 'userPassword',
            'activeSagsbehandler', 'modalNavn', 'modalEmail', 'modalTlf', 'modalMobil',
            'transferToKreditorId', 'securityCode'
        ]);
    }

    // =========================================================================
    // BRUGER-HANDLINGER (PORTALBRUGERE)
    // =========================================================================
    public function openUserModal(?int $userId = null): void
    {
        $this->reset(['userName', 'userEmail', 'userPassword']);

        if ($userId) {
            $this->activeUser = User::findOrFail($userId);
            $this->userName = $this->activeUser->name;
            $this->userEmail = $this->activeUser->email;
        } else {
            $this->activeUser = null;
        }

        $this->showUserModal = true;
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
            // Opdater eksisterende bruger
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
            // Opret ny bruger og tilknyt kreditoren
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

        $this->showSagsModal = true;
    }

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

    public function detachSagsbehandler(int $id): void
    {
        $this->kreditor->sagsbehandlere()->detach($id);

        $this->dispatch('toast', ['message' => 'Sagsbehandleren er fjernet fra kreditoren.', 'type' => 'success']);
        $this->loadRelations();
    }

    // =========================================================================
    // SLETTEMODAL OG OVERFØRSEL AF SAGER
    // =========================================================================
    public function requestDelete(): void
    {
        $this->showDeleteModal = true;
    }

    public function confirmDelete()
    {
        // Hvis der er sager tilknyttet, skal modtager-kreditor vælges
        if ($this->kreditor->sager_count > 0) {
            $this->validate([
                'transferToKreditorId' => 'required|exists:kreditors,id',
            ], [
                'transferToKreditorId.required' => 'Vælg venligst en modtager-kreditor til sagerne.',
            ]);

            // Overfør alle sager i pivot/relation til den nye kreditor
            $targetKreditor = Kreditorer::findOrFail($this->transferToKreditorId);

            foreach ($this->kreditor->sager as $sag) {
                $sag->sagerkreditor()->sync([$targetKreditor->id]);
            }
        }

        // Slet kreditoren
        $this->kreditor->delete();

        $this->dispatch('toast', ['message' => 'Kreditoren er blevet slettet.', 'type' => 'success']);

        return redirect()->route('kreditorer.index');
    }
}