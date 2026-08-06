<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Artisan;

class BackupManager extends Component
{
    public bool $running = false;

    public function runBackup()
    {
        $this->running = true;

        Artisan::call('backup:run');

        $this->running = false;

        session()->flash('success', 'Backup completed.');
    }

    public function render()
    {
        return view('livewire.backup-manager');
    }
}