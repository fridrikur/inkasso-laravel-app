<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Hash;

class SystemSecurity extends Component
{
    public string $unlock_code = '';
    public string $unlock_code_confirmation = '';

    public bool $hasCode = false;

    public function mount()
    {
        $this->hasCode = SystemSetting::get('global_unlock_code') !== null;
    }

    public function save()
    {
        $this->validate([
            'unlock_code' => 'required|min:4|same:unlock_code_confirmation',
        ]);

        SystemSetting::set(
            'global_unlock_code',
            Hash::make($this->unlock_code)
        );

        $this->reset([
            'unlock_code',
            'unlock_code_confirmation',
        ]);

        $this->hasCode = true;

        $this->dispatch(
            'toast',
            message: 'Global unlock code updated',
            type: 'success'
        );
    }

    public function render()
    {
        return view('livewire.admin.system-security');
    }
}