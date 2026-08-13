<?php

namespace App\Livewire\Users;

use Livewire\Component;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AssignMedarbejder extends Component
{
    public $users;

    public function mount()
    {
        $this->loadUsersWithoutRole();
    }

    // Load all users who don't have any roles yet
    public function loadUsersWithoutRole()
    {
        $this->users = User::doesntHave('roles')->get();
    }

    // Assign Medarbejder role to a single user
    public function assignMedarbejder($userId)
    {
        $user = User::findOrFail($userId);
        $medarbejderRole = Role::firstOrCreate(['name' => 'Medarbejder']);
        $user->assignRole($medarbejderRole);

        session()->flash('message', "$user->name blev tildelt rollen Medarbejder.");
        $this->loadUsersWithoutRole();
    }

    // Assign Medarbejder role to all users without roles
    public function assignMedarbejderToAll()
    {
        $medarbejderRole = Role::firstOrCreate(['name' => 'Medarbejder']);

        foreach ($this->users as $user) {
            $user->assignRole($medarbejderRole);
        }

        session()->flash('message', "Alle brugere uden rolle fik tildelt rollen Medarbejder.");
        $this->loadUsersWithoutRole();
    }

    // Delete a user
    public function deleteUser($userId)
    {
        $user = User::findOrFail($userId);
        $userName = $user->name;
        $user->delete();

        session()->flash('message', "$userName blev slettet.");
        $this->loadUsersWithoutRole();
    }

    public function render()
    {
        return view('livewire.users.assign-medarbejder');
    }
}
