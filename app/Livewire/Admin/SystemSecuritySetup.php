<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;

class SystemSecuritySetup extends Component
{
    public string $digit1 = '';
    public string $digit2 = '';
    public string $digit3 = '';
    public string $digit4 = '';

    public function mount()
    {
        // Hvis tabellen ikke findes endnu, eller koden allerede er sat, gå videre
        if (!Schema::hasTable('system_settings')) {
            return;
        }

        $hasCode = SystemSetting::where('key', 'global_unlock_code')->value('value') !== null;
        if ($hasCode) {
            $this->redirect(route('dashboard'), navigate: true);
        }
    }

    public function saveUnlockCode()
    {
        $code = $this->digit1 . $this->digit2 . $this->digit3 . $this->digit4;

        if (strlen($code) !== 4 || !is_numeric($code)) {
            $this->dispatch('toast', message: 'Indtast venligst en gyldig 4-cifret pinkode.', type: 'error');
            return;
        }

        if (Schema::hasTable('system_settings')) {
            SystemSetting::updateOrCreate(
                ['key' => 'global_unlock_code'],
                ['value' => Hash::make($code)]
            );
        }

        $this->dispatch('toast', message: 'Låsekode gemt med succes!', type: 'success');
        $this->redirect(route('dashboard'), navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.system-security-setup')
            ->layout('layouts.guest'); // Eller jeres standard fuldskærms-layout
    }
}