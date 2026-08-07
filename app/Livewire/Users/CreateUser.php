<?php

namespace App\Livewire\Users;

use App\Models\User;
use App\Models\Kreditorer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\Attributes\Url;

class CreateUser extends Component
{
    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public ?int $kreditor_id = null;

    #[Url]
    public string $role = 'Medarbejder';

    protected function rules(): array
    {
        $rules = [
            'role' => [
                'required',
                Rule::in([
                    'Admin',
                    'Medarbejder',
                    'Kreditor',
                ]),
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'unique:users,email',
            ],

            'password' => [
                'required',
                'min:8',
                'same:password_confirmation',
            ],
        ];

        if ($this->role === 'Kreditor') {

            $rules['kreditor_id'] = [
                'required',
                'exists:kreditors,id',
            ];
        }

        return $rules;
    }

    public function save()
    {
        $validated = $this->validate();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make(
                $validated['password']
            ),
        ]);

        /*
        |--------------------------------------------------------------------------
        | Assign role
        |--------------------------------------------------------------------------
        */

        $user->assignRole($validated['role']);

        /*
        |--------------------------------------------------------------------------
        | Kreditor users
        |--------------------------------------------------------------------------
        */

        if (
            $validated['role'] === 'Kreditor'
            && !empty($validated['kreditor_id'])
        ) {

            $user->kreditorer()->sync([
                $validated['kreditor_id']
            ]);
        }

        session()->flash(
            'success',
            'Brugeren blev oprettet.'
        );

        return redirect()->route('users.manage-users');
    }

    public function render()
    {
        return view('livewire.users.create-user', [
            'kreditorer' => Kreditorer::orderBy('navn')->get(),
        ]);
    }
}