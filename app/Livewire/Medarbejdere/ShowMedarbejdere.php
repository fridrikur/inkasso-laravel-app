<?php

namespace App\Livewire\Medarbejdere;

use Livewire\Component;
use App\Models\User;

class ShowMedarbejdere extends Component
{
    public ?User $activeUser = null;

    // 🔥 THIS is your new button action
    public function createMedarbejderUser()
    {
        $this->activeUser = null;
        $this->dispatch('open-user-modal', role: 'Medarbejder')
        ->to(\App\Livewire\Users::class);
    }

    public function render()
    {
        return view('livewire.medarbejdere.show-medarbejdere', [
            'users' => User::role('Medarbejder')->get(),
        ]);
    }
}