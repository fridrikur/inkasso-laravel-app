<?php

namespace App\Livewire\Users;

use App\Models\Kreditorer;
use App\Models\User;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class UpdateUser extends Component
{
    public int $userId;
    public User $user;

    public array $form = [
        'name' => '',
        'email' => '',
        'password' => '',
        'role' => '',
        'kreditor_id' => null,
    ];

    public bool $showRoleEditor = false;

    public $roles = [];
    public $kreditors = [];

    public function mount(int $userId): void
    {
        $this->userId = $userId;

        $this->user = User::query()
            ->with(['roles:id,name', 'kreditorer:id,navn'])
            ->findOrFail($userId);

        $this->roles = Role::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        $this->kreditors = Kreditorer::query()
            ->orderBy('navn')
            ->get(['id', 'navn']);

        $currentRole = $this->user->roles->first()?->name;

        $this->form = [
            'name' => $this->user->name,
            'email' => $this->user->email,
            'password' => '',
            'role' => $currentRole ?? '',
            'kreditor_id' => $this->user->kreditorer->first()?->id,
        ];
    }

    public function updatedFormRole($value): void
    {
        if ($value !== 'Kreditor') {
            $this->form['kreditor_id'] = null;
        }
    }

    public function save(): void
    {
        $rules = [
            'form.name' => ['required', 'string', 'max:255'],
            'form.email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->userId),
            ],
        ];

        if ($this->showRoleEditor) {
            $rules['form.role'] = ['required', 'string', Rule::exists('roles', 'name')];
        }

        if (!empty($this->form['password'])) {
            $rules['form.password'] = ['string', 'min:8'];
        }

        if (($this->showRoleEditor && $this->form['role'] === 'Kreditor')
            || (!$this->showRoleEditor && $this->user->hasRole('Kreditor'))) {
            $rules['form.kreditor_id'] = ['nullable', 'integer', Rule::exists('kreditors', 'id')];
        }

        $this->validate($rules);

        $this->user->update([
            'name' => $this->form['name'],
            'email' => $this->form['email'],
            ...(!empty($this->form['password']) ? ['password' => bcrypt($this->form['password'])] : []),
        ]);

        // Only sync role if admin explicitly opened role editing
        if ($this->showRoleEditor && !empty($this->form['role'])) {
            $this->user->syncRoles([$this->form['role']]);
            $this->user->load('roles');
        }

        $effectiveRole = $this->showRoleEditor
            ? $this->form['role']
            : ($this->user->roles->first()?->name);

        if ($effectiveRole === 'Kreditor') {
            $this->user->kreditorer()->sync(
                $this->form['kreditor_id'] ? [$this->form['kreditor_id']] : []
            );
        } else {
            // If not Kreditor anymore, detach kreditor relation
            $this->user->kreditorer()->sync([]);
        }

        $this->dispatch('user-updated');
    }

    public function close(): void
    {
        $this->dispatch('close-user-modal');
    }

    public function render()
    {
        return view('livewire.users.update-user');
    }
}