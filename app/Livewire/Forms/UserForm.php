<?php

namespace App\Livewire\forms;

use Livewire\Component;
use App\Models\User;
use App\Models\Kreditorer;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rule;

class UserForm extends Component
{
    public ?int $userId = null;

    public string $name = '';
    public string $email = '';
    public string $password = '';
    public ?int $selectedKreditor = null;
    public ?int $selectedMedarbejder = null;
    public string $role = '';

    public function mount(?User $user = null)
    {
        $this->user = $user;

        if ($user) {
            $this->name = $user->name;
            $this->email = $user->email;
            $this->roles = $user->roles->pluck('name')->toArray();

            // Prefill the first (and only) Kreditor from pivot
            $this->selectedKreditor = $user->kreditorer->first()?->id ?? null;
            $this->selectedMedarbejder = $user->medarbejdere->first()?->id ?? null;
        }
    }

    public function setUser(User $user)
    {
        $this->userId = $user->id;

        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->getRoleNames()->first() ?? '';
    }


    public function updatedRoles($value)
    {
        $rolesArray = is_array($value) ? $value : [$value];

        if (!in_array('Kreditor', $rolesArray)) {
            $this->selectedKreditor = null;
        }

        if (!in_array('Medarbejder', $rolesArray)) {
            $this->selectedMedarbejder = null;
        }
    }


    public function rules()
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->user?->id),
            ],
            'roles' => ['required', 'array', 'min:1'],
            'password' => $this->user ? ['nullable', 'string', 'min:6'] : ['required', 'string', 'min:6'],
        ];

        if (in_array('Kreditor', $this->roles)) {
            $rules['selectedKreditor'] = ['required', 'integer', 'exists:kreditors,id'];
        }

        if (in_array('Medarbejder', $this->roles)) {
            $rules['selectedMedarbejder'] = ['required', 'integer', 'exists:medarbejders,id'];
        }

        return $rules;
    }

    public function update()
    {
        $this->validate();

        $this->user->update([
            'name' => $this->name,
            'email' => $this->email,
        ]);

        // ✅ THIS is where it belongs
        $this->user->syncRoles([$this->role]);
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

        // Sync roles
        $user->syncRoles($this->roles);

        // Handle Kreditor pivot (only one per user)
        if (in_array('Kreditor', $this->roles) && $this->selectedKreditor) {
            $user->kreditorer()->sync([$this->selectedKreditor]);
        } else {
            $user->kreditorer()->detach();
        }

        // Handle Medarbejder pivot (exact copy of kreditor logic)
        if (in_array('Medarbejder', $this->roles) && $this->selectedMedarbejder) {
            $user->medarbejdere()->sync([$this->selectedMedarbejder]);
        } else {
            $user->medarbejdere()->detach();
        }

        // Reset form fields
        $this->reset(['name', 'email', 'password', 'selectedKreditor', 'selectedMedarbejder', 'roles']);

        session()->flash('message', 'Bruger gemt!');
        return redirect()->to('/users');
    }
}
