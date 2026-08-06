<?php

class Users extends Component
{
    public function render()
    {
        return view('livewire.admin.dashboard.users', [
            'userStats' => User::stats(),
            'roleStats' => Role::withCount('users')->pluck('users_count', 'name')
        ]);
    }
}