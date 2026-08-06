<?php

namespace App\Livewire\Users;

use Livewire\Component;
use App\Models\User;
use App\Models\Medarbejdere;

class CreateMedarbejderUser extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public ?int $medarbejder = null;

    public function save()
    {
        $this->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'medarbejder' => 'required|exists:medarbejders,id',
        ]);

        // 1. Create user
        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => bcrypt($this->password),
        ]);

        // 2. Assign role
        $user->syncRoles(['Medarbejder']);

        // 3. 🔥 THIS is what was missing / broken before
        $user->medarbejdere()->sync([$this->medarbejder]);

        return redirect()->to('/users');
    }

    public function render()
    {
        return view('liveWire.users.create-user', [
            'medarbejdere' => Medarbejdere::all(),
        ]);
    }
}