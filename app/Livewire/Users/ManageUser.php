<?php

namespace App\Livewire\Users;

use App\Models\User;
use App\Models\Kreditorer;
use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class ManageUser extends Component
{
    public User $user;

    // Modaler
    public bool $showEditModal = false;
    public bool $showPasswordModal = false;
    public bool $showDeleteModal = false;

    // Form felter (Edit Stamdata)
    public string $name = '';
    public string $email = '';
    public string $selectedRole = 'Medarbejder';
    public ?int $assignedKreditorId = null;

    // Form felter (Password)
    public string $newPassword = '';
    public string $newPassword_confirmation = '';

    public function mount($user)
    {
        $userId = $user instanceof User ? $user->id : $user;
        $this->user = User::withTrashed()->findOrFail($userId);
        
        $this->loadRelations();
    }

    protected function loadRelations(): void
    {
        $this->user->load(['roles', 'kreditorer']);
    }

    public function render()
    {
        return view('livewire.users.manage-user', [
            'allRoles' => Role::orderBy('name')->get(),
            'allKreditorer' => Kreditorer::orderBy('navn')->get(),
        ]);
    }

    // =========================================================================
    // REDIGÉR STAMDATA & ROLLE
    // =========================================================================
    public function openEditModal(): void
    {
        $this->name = $this->user->name;
        $this->email = $this->user->email;
        $this->selectedRole = $this->user->roles->first()?->name ?? 'Medarbejder';
        $this->assignedKreditorId = $this->user->kreditorer->first()?->id;

        $this->showEditModal = true;
    }

    public function saveStamdata(): void
    {
        // Beskyttelse: Bruger #1 skal altid forblive Admin
        if ($this->user->id === 1 && $this->selectedRole !== 'Admin') {
            $this->dispatch('toast', [
                'message' => 'Rollen for systemets primære administrator (Bruger #1) kan ikke ændres.',
                'type'    => 'error'
            ]);
            $this->selectedRole = 'Admin';
            return;
        }

        $this->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|max:255|unique:users,email,' . $this->user->id,
            'selectedRole' => 'required|string',
        ]);

        $this->user->update([
            'name'  => $this->name,
            'email' => $this->email,
        ]);

        if (method_exists($this->user, 'syncRoles')) {
            $this->user->syncRoles([$this->selectedRole]);
        }

        if ($this->selectedRole === 'Kreditor' && $this->assignedKreditorId) {
            $this->user->kreditorer()->sync([$this->assignedKreditorId]);
        } else {
            $this->user->kreditorer()->detach();
        }

        $this->showEditModal = false;
        $this->loadRelations();

        $this->dispatch('toast', ['message' => 'Brugeroplysninger opdateret.', 'type' => 'success']);
    }

    // =========================================================================
    // NULSTIL / SKIFT ADGANGSKODE
    // =========================================================================
    public function openPasswordModal(): void
    {
        $this->reset(['newPassword', 'newPassword_confirmation']);
        $this->showPasswordModal = true;
    }

    public function updatePassword(): void
    {
        $this->validate([
            'newPassword' => 'required|string|min:8|confirmed',
        ], [
            'newPassword.confirmed' => 'Adgangskoderne er ikke ens.',
            'newPassword.min'       => 'Adgangskoden skal være på mindst 8 tegn.',
        ]);

        $this->user->update([
            'password' => Hash::make($this->newPassword),
        ]);

        $this->showPasswordModal = false;
        $this->dispatch('toast', ['message' => 'Adgangskoden er blevet ændret.', 'type' => 'success']);
    }

    // =========================================================================
    // SOFT-DELETE MODAL LOGIK & GENSKAB (RESTORE)
    // =========================================================================
    public function restoreUser(): void
    {
        if ($this->user->trashed()) {
            $this->user->restore();
            $this->loadRelations();

            $this->dispatch('toast', [
                'message' => 'Brugeren er blevet genaktiveret succesfuldt!',
                'type'    => 'success'
            ]);
        }
    }

    // 🟢 ÅBNER DEAKTIVERINGSMODALEN (med sikkerhedstjek)
    public function requestDeactivate(): void
    {
        if ($this->user->id === 1) {
            $this->dispatch('toast', [
                'message' => 'Systemets primære administrator (Bruger #1) kan ikke deaktiveres.',
                'type'    => 'error'
            ]);
            return;
        }

        if ($this->user->id === auth()->id()) {
            $this->dispatch('toast', [
                'message' => 'Du kan ikke deaktivere din egen konto.',
                'type'    => 'error'
            ]);
            return;
        }

        if ($this->user->hasRole('Admin')) {
            $this->dispatch('toast', [
                'message' => 'Brugere med Admin-rollen kan ikke deaktiveres. Skift først brugerens rolle til Medarbejder.',
                'type'    => 'error'
            ]);
            return;
        }

        $this->showDeleteModal = true;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
    }

    // 🟢 UDFØRER SLETTEN/DEAKTIVERINGEN
    public function confirmDelete()
    {
        if ($this->user->id === 1 || $this->user->id === auth()->id() || $this->user->hasRole('Admin')) {
            $this->cancelDelete();
            return;
        }

        $this->user->delete(); // SoftDelete

        $this->dispatch('toast', [
            'message' => 'Brugeren er blevet deaktiveret.',
            'type'    => 'success'
        ]);

        return redirect()->route('users.index');
    }
}