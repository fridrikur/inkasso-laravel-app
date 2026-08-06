<?php

namespace App\Livewire\KTR;

use Livewire\Component;
use App\Models\KTR;

class ShowKTR extends Component
{
    public $ktr;

    public function mount(KTR $ktr)
    {
        $this->ktr = $ktr;
    }

    public function render()
    {
        return view('liveWire.ktr.show');
    }
}