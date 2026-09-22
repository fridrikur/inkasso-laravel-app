<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use App\Models\NotifyBruger;

class NotifyBrugereManager extends Component
{
    public function toggleNotification($userId)
    {
        // Tjek om brugeren allerede modtager notifikationer
        $exists = NotifyBruger::where('brugerID', $userId)->first();

        if ($exists) {
            $exists->delete();
            $message = 'Notifikationer slået fra for medarbejderen.';
        } else {
            NotifyBruger::create(['brugerID' => $userId]);
            $message = 'Notifikationer slået til for medarbejderen.';
        }

        $this->dispatch('toast', message: $message, type: 'success');
    }

    public function render()
    {
        // 🟢 Bruger direkte whereHas i stedet for User::role(), hvilket undgår navnekonflikten
        $users = User::whereHas('roles', function ($query) {
            $query->where('name', 'Medarbejder');
        })->get()->map(function ($user) {
            $user->is_notified = NotifyBruger::where('brugerID', $user->id)->exists();
            return $user;
        });

        return view('livewire.admin.notify-brugere-manager', [
            'users' => $users
        ]);
    }
}