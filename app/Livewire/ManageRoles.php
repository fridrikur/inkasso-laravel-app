<?php

namespace App\Livewire;

use Livewire\Component;
use Spatie\Permission\Models\Role;
use App\Models\User;

class ManageRoles extends Component
{
    public $roles;
    public $selectedRoleId = null;
    public $users = []; // array of users for display

    public string $tab = 'roles';

    public $roleId;
    public $roleName;
    public $showEditRoleModal = false;

    public $selectedUserId;
    public $showAddUserModal = false;

    public function mount()
    {
        $this->roles = Role::all();
    }

    // Select a role and load its users
    public function selectRole($roleId)
    {
        $this->selectedRoleId = $roleId;
        $role = Role::findOrFail($roleId);

        if ($role->name === 'Kreditor') {
            $this->users = User::role(['Kreditor'])->get()
                ->groupBy(function($user) {
                    // Group by first company, or "Uden kreditor" if none
                    return $user->kreditorer->first() ? $user->kreditorer->first()->navn : 'Uden kreditor';
                })
                ->toArray();
        }
        else {
                    $this->users = $role->users()->with('kreditorer')->get()->toArray();
                }
            }

    // Edit role
    public function editRole($id)
    {
        $role = Role::findOrFail($id);
        $this->roleId = $role->id;
        $this->roleName = $role->name;
        $this->showEditRoleModal = true;
    }

    public function updateRole()
    {
        $this->validate([
            'roleName' => 'required|string|max:255',
        ]);

        $role = Role::findOrFail($this->roleId);
        $role->name = $this->roleName;
        $role->save();

        $this->showEditRoleModal = false;
        session()->flash('message', 'Role updated successfully!');

        $this->selectRole($this->roleId); // reload users
    }

    // Add user to role
    public function addUserToRole($roleId)
    {
        $this->roleId = $roleId;
        $this->selectedUserId = null;
        $this->showAddUserModal = true;
    }

    public function saveUserToRole()
    {
        $user = User::findOrFail($this->selectedUserId);
        $role = Role::findOrFail($this->roleId);

        // 🔥 HARD RULE: remove ALL roles first
        $user->roles()->detach();

        // assign only one
        $user->assignRole($role->name);

        $this->showAddUserModal = false;

        session()->flash('message', "Assigned {$role->name} to {$user->name}");

        $this->selectRole($this->roleId);
    }

    // Remove user from role
    public function removeUserFromRole($roleId, $userId)
    {
        $user = User::findOrFail($userId);
        $role = Role::findOrFail($roleId);

        $user->removeRole($role->name);

        session()->flash('message', "Removed {$user->name} from {$role->name}");
        $this->selectRole($roleId); // reload users
    }

    public function toggleTwoFactor($roleId)
    {
        $role = Role::findOrFail($roleId);

        $role->requires_two_factor = ! $role->requires_two_factor;
        $role->save();

        session()->flash(
            'message',
            $role->requires_two_factor
                ? "{$role->name} now requires 2FA"
                : "{$role->name} no longer requires 2FA"
        );

        $this->roles = Role::all();
    }

    public function render()
    {
        $allUsers = User::all();
        return view('liveWire.roles.index', [
            'usersList' => $allUsers,
        ]);
    }
}
