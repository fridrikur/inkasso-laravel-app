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
        // Hent koden fra system_settings tabellen via modellen
        $this->hasCode = SystemSetting::where('key', 'global_unlock_code')->value('value') !== null;
    }

    public function save()
    {
        $this->validate([
            'unlock_code' => 'required|min:4|same:unlock_code_confirmation',
        ]);

        // Gem eller opdater koden som en nøgle i system_settings tabellen
        SystemSetting::updateOrCreate(
            ['key' => 'global_unlock_code'],
            ['value' => Hash::make($this->unlock_code)]
        );

        $this->reset([
            'unlock_code',
            'unlock_code_confirmation',
        ]);

        $this->hasCode = true;

        $this->dispatch(
            'toast',
            message: 'Global låsekode opdateret',
            type: 'success'
        );
    }

    public function render()
    {
        return view('livewire.admin.system-security');
    }
}