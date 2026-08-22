<?php

namespace App\Livewire\Kreditor;

use App\Models\User;
use App\Models\Kreditorer;
use Livewire\Component;
use Illuminate\Support\Facades\Hash;

class UserFormModal extends Component
{
    public bool $showModal = false;
    public ?int $kreditorId = null;
    public ?int $editingId = null;

    public string $name = '';
    public string $email = '';
    public string $password = '';
    
    public bool $showDeleteModal = false;
    public ?int $deletingId = null;

    protected $listeners = [
        'open-user-create' => 'create',
        'open-user-edit'   => 'edit',
        'open-user-delete' => 'confirmDeleteModal',
    ];    
    
    public function create($kreditorId = null)
    {
        $this->reset(['name', 'email', 'password', 'editingId']);
        $this->kreditorId = $kreditorId;
        $this->editingId = null; // Tvinges til null ved oprettelse
        $this->showModal = true;
    }

    public function edit($id)
    {
        $this->reset(['name', 'email', 'password', 'editingId', 'kreditorId']);
        
        $this->editingId = (int) $id;
        $user = User::findOrFail($this->editingId);
        
        $this->name  = $user->name;
        $this->email = $user->email ?? '';
        $this->password = ''; // Adgangskode nulstilles af sikkerhedsmæssige årsager ved redigering

        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['name', 'email', 'password', 'editingId', 'kreditorId']);
    }

    public function save()
    {
        $rules = [
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . ($this->editingId ?? 'NULL'),
        ];

        // Kræv kun adgangskode ved oprettelse, ellers er den valgfri ved ændring
        if (!$this->editingId) {
            $rules['password'] = 'required|string|min:8';
        } else {
            $rules['password'] = 'nullable|string|min:8';
        }

        $this->validate($rules);

        try {
            if ($this->editingId) {
                $user = User::findOrFail($this->editingId);
                $userData = [
                    'name'  => $this->name,
                    'email' => $this->email,
                ];

                if (!empty($this->password)) {
                    $userData['password'] = Hash::make($this->password);
                }

                $user->update($userData);
                $msg = 'Bruger opdateret.';
            } else {
                // <--- HENT DATA OG OPRET BRUGER KORREKT HER --->
                $user = User::create([
                    'name'     => $this->name,
                    'email'    => $this->email,
                    'password' => Hash::make($this->password),
                ]);

                if (method_exists($user, 'assignRole')) {
                    $user->assignRole('Kreditor');
                }
                if ($this->kreditorId) {
                    $kreditor = Kreditorer::find($this->kreditorId);
                    $kreditor?->users()->syncWithoutDetaching([$user->id]);
                }

                $msg = 'Bruger oprettet og tilknyttet.';
            }

            $this->dispatch('toast', ['message' => $msg, 'type' => 'success']);
            $this->closeModal();
            $this->dispatch('kreditor-updated');

        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->errorInfo[1] === 1062) {
                $this->addError('email', 'Der findes allerede en bruger med denne e-mail.');
                $this->dispatch('toast', ['message' => 'Der findes allerede en bruger med denne e-mail.', 'type' => 'error']);
            } else {
                $this->dispatch('toast', ['message' => 'Der opstod en databasefejl. Prøv igen.', 'type' => 'error']);
            }
        }
    }

    public function confirmDeleteModal($id = null)
    {
        $this->deletingId = is_array($id) ? ($id['id'] ?? null) : $id;
        
        if (!empty($this->deletingId)) {
            if (is_array($id) && isset($id['kreditorId'])) {
                $this->kreditorId = $id['kreditorId'];
            }
        }

        $this->showDeleteModal = true;
    }

    public function deleteUser()
    {
        if ($this->deletingId) {
            if ($this->kreditorId) {
                $kreditor = Kreditorer::find($this->kreditorId);
                // Fjerner kun tilknytningen til kreditoren (brugeren slettes ikke fra systemet)
                $kreditor?->users()->detach($this->deletingId);
            }

            $this->dispatch('toast', ['message' => 'Brugeren er fjernet fra kreditoren.', 'type' => 'success']);
        }

        $this->showDeleteModal = false;
        $this->deletingId = null;
        
        $this->dispatch('kreditor-updated');
    }

    public function render()
    {
        return view('livewire.kreditorer.user-form-modal');
    }
}