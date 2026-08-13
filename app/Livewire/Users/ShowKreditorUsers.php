<?php

namespace App\Livewire\Users;

use Livewire\Component;
use App\Models\User;
use App\Models\Kreditorer;

class Showkreditorusers extends Component
{
    public function render()
    {
        return view('livewire.users.show-kreditor-users',[
            $users = User::role(['Kreditor'])->where('id', request()->kreditor)->get(),
        ]);
    }
}
