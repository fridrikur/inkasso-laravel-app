<?php

namespace App\Livewire\Kreditorer;

use Livewire\Component;
use App\Models\Kreditorer;
use App\Models\Sagsbehandler;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;


class ShowKreditor extends Component
{
    public Kreditorer $kreditor;
    public $usedLotusIds = [];
    
    /** ---------------- Sagsbehandler modal ---------------- */
    public bool $showSagsModal = false;
    public ?Sagsbehandler $activeSagsbehandler = null;

    public string $modalNavn = '';
    public ?string $modalEmail = null;
    public ?string $modalTlf = null;
    public ?string $modalMobil = null;
    
    /** ---------------- User modal ---------------- */
    public bool $showUserModal = false;
    public ?User $activeUser = null;

    public string $userName = '';
    public string $userEmail = '';
    public ?string $userPassword = null;

    public bool $emailTaken = false;

    public string $kreditorNavn = '';

    public function mount(Kreditorer $kreditor)
    {
        $this->kreditor = Kreditorer::query()
            ->with([
                'users:id,name,email',
                'sagsbehandlere:id,navn,email,tlf,mobil',
            ])
            ->withCount('sager')
            ->findOrFail($kreditor->id);

        $this->kreditornavn = $this->kreditor->navn;
        $this->usedLotusIds = \App\Models\Kreditorer::pluck('lotusID')->toArray();
    }

    /* =====================================================
     * USER MODAL
     * ===================================================== */

    public function openUserModal(?int $id = null)
    {
        $this->resetValidation();

        if ($id) {
            $this->activeUser = User::findOrFail($id);
            $this->userName = $this->activeUser->name;
            $this->userEmail = $this->activeUser->email;
        } else {
            $this->resetUserFields();
        }

        $this->showUserModal = true;
    }

    public function updatedUserEmail()
    {
        if (!filter_var($this->userEmail, FILTER_VALIDATE_EMAIL)) {
            $this->emailTaken = false;
            return;
        }

        $this->emailTaken = User::where('email', $this->userEmail)
            ->when($this->activeUser, fn ($q) =>
                $q->where('id', '!=', $this->activeUser->id)
            )
            ->exists();
    }

    public function getUserEmailChangedProperty(): bool
    {
        return $this->activeUser
            && $this->userEmail !== $this->activeUser->email;
    }

    public function saveUser()
    {
        if ($this->emailTaken) {
            $this->addError('userEmail', 'Denne e-mail er allerede i brug.');
            return;
        }

        $this->validate([
            'userName' => ['required', 'string', 'max:255'],
            'userEmail' => [
                'required',
                'email',
                Rule::unique('users', 'email')
                    ->ignore($this->activeUser?->id),
            ],
            'userPassword' => $this->activeUser
                ? ['nullable', 'min:8']
                : ['required', 'min:8'],
        ]);

        $user = $this->activeUser
            ? tap($this->activeUser)->update([
                'name' => $this->userName,
                'email' => $this->userEmail,
                'password' => $this->userPassword
                    ? Hash::make($this->userPassword)
                    : $this->activeUser->password,
            ])
            : User::create([
                'name' => $this->userName,
                'email' => $this->userEmail,
                'password' => Hash::make($this->userPassword),
            ]);

        $user->kreditorer()->syncWithoutDetaching([$this->kreditor->id]);

        $this->closeModal();
        $this->kreditor->refresh();
    }

    /* =====================================================
     * SAGSBHANDLER MODAL
     * ===================================================== */

    public function openSagsbehandlerModal(?int $id = null)
    {
        $this->resetValidation();

        if ($id) {
            $this->activeSagsbehandler = Sagsbehandler::findOrFail($id);
            $this->modalNavn = $this->activeSagsbehandler->navn;
            $this->modalEmail = $this->activeSagsbehandler->email;
            $this->modalTlf = $this->activeSagsbehandler->tlf;
            $this->modalMobil = $this->activeSagsbehandler->mobil;
        } else {
            $this->resetSagsFields();
        }

        $this->showSagsModal = true;
    }

    public function saveSagsbehandler()
    {
        $this->validate([
            'modalNavn' => ['required', 'string', 'max:255'],

            'modalEmail' => [
            'required',
            'email',
            Rule::unique('sagsbehandlers', 'email')
                ->ignore($this->activeSagsbehandler?->id),
        ],

            'modalTlf' => [
                'nullable',
                Rule::unique('sagsbehandlers', 'tlf')
                    ->ignore($this->activeSagsbehandler?->id),
            ],

            'modalMobil' => [
                'nullable',
                Rule::unique('sagsbehandlers', 'mobil')
                    ->ignore($this->activeSagsbehandler?->id),
            ],
        ], [
            'modalEmail.unique' => 'Denne e-mailadresse er allerede i brug.',
            'modalTlf.unique' => 'Telefonnummeret er allerede i brug.',
            'modalMobil.unique' => 'Mobilnummeret er allerede i brug.',
        ]);

        $sags = $this->activeSagsbehandler
            ? tap($this->activeSagsbehandler)->update([
                'navn' => $this->modalNavn,
                'email' => $this->modalEmail,
                'tlf' => $this->modalTlf,
                'mobil' => $this->modalMobil,
            ])
            : Sagsbehandler::create([
                'navn' => $this->modalNavn,
                'email' => $this->modalEmail,
                'tlf' => $this->modalTlf,
                'mobil' => $this->modalMobil,
            ]);

        $sags->kreditorer()->sync([$this->kreditor->id]);

        $this->closeModal();
        $this->kreditor->refresh();
    }

    /* =====================================================
     * SHARED MODAL HANDLING
     * ===================================================== */

    public function closeModal(): void
    {
        $this->showUserModal = false;
        $this->showSagsModal = false;

        $this->resetUserFields();
        $this->resetSagsFields();

        $this->resetValidation();
    }

    private function resetUserFields()
    {
        $this->activeUser = null;
        $this->userName = '';
        $this->userEmail = '';
        $this->userPassword = null;
        $this->emailTaken = false;
    }

    private function resetSagsFields()
    {
        $this->activeSagsbehandler = null;
        $this->modalNavn = '';
        $this->modalEmail = null;
        $this->modalTlf = null;
        $this->modalMobil = null;
    }

    public function getSuggestedLotusIdProperty()
    {
        return (\App\Models\Kreditorer::max('lotusID') ?? 0) + 1;
    }

    public function render()
    {
        return view('liveWire.kreditorer.show-kreditor', [
            'kreditornavn' => $this->kreditor->navn,
            'kreditor' => $this->kreditor,
        ]);
    }

}
