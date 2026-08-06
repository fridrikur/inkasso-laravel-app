<?php

namespace App\Livewire\Sager;

use Livewire\Component;
use App\Models\Sager;
use App\Traits\HasSagDialog;

class Klientinformation extends Component
{
    use HasSagDialog;

    public Sager $sag;

    protected function getDialogType(): string
    {
        return 'klientinformation';
    }

    public function mount(Sager $sag)
    {
        $this->sag = $sag;
    }

    public function save()
    {
        $this->validate([
            'tekst' => 'required|string',
        ]);

        // Sendes af den aktuelt indloggede bruger (admin/konsulent)
        $this->sendMessage();
    }

    public function render()
    {
        return view('livewire.sager.klientinformation', [
            'dialogMessages' => $this->getDialogMessages(),
        ]);
    }
}