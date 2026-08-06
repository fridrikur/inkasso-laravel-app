<?php

namespace App\Livewire\Users;

use Livewire\Component;
use App\Models\User;
use App\Models\Kreditorer;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rule;

class UserFormModal extends Component
{
    public ?User $user = null;

    public string $name = '';
    public string $email = '';
    public string $password = '';
    public array $roles = [];
    public ?int $selectedKreditor = null;

    public function mount(?User $user = null)
    {
        $this->user = $user;

        $this->name = $user?->name ?? '';
        $this->email = $user?->email ?? '';
        $this->roles = $user?->roles->pluck('name')->toArray() ?? [];
        $this->selectedKreditor = $user?->kreditorer->first()?->id ?? null;
    }

    public function updatedRoles($value)
    {
        $rolesArray = is_array($value) ? $value : [$value];

        if (!in_array('Kreditor', $rolesArray)) {
            $this->selectedKreditor = null;
        }
    }

    public function rules()
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 'email', 'max:255',
                Rule::unique('users', 'email')->ignore($this->user?->id),
            ],
            'roles' => ['required', 'array', 'min:1'],
            'password' => $this->user ? ['nullable', 'string', 'min:6'] : ['required', 'string', 'min:6'],
        ];

        if (in_array('Kreditor', $this->roles)) {
            $rules['selectedKreditor'] = ['required', 'integer', 'exists:kreditors,id'];
        }

        return $rules;
    }

    public function save()
    {
        $this->validate();

        $user = $this->user ?? new User();
        $user->name = $this->name;
        $user->email = $this->email;

        if (!empty($this->password)) {
            $user->password = bcrypt($this->password);
        }

        $user->save();
        $user->syncRoles($this->roles);

        if (in_array('Kreditor', $this->roles) && $this->selectedKreditor) {
            $user->kreditorer()->sync([$this->selectedKreditor]);
        } else {
            $user->kreditorer()->detach();
        }

        $this->dispatch('userSaved'); // notify parent
        session()->flash('message', 'Bruger gemt!');
    }

    public function render()
    {
        return view('liveWire.users.user-form-modal', [
            'allRoles' => Role::pluck('name')->toArray(),
            'allKreditors' => Kreditorer::pluck('navn', 'id')->toArray(),
        ]);
    }
}
