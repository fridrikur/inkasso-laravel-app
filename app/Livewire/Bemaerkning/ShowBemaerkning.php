<?php

namespace App\Livewire\bemaerkning;

use Livewire\Component;
use App\Models\bemaerkning;

class Showbemaerkning extends Component
{
    public $bemaerkning;

    public function mount(bemaerkning $bemaerkning)
    {
        $this->bemaerkning = $bemaerkning;
    }

    public function render()
    {
        return view('livewire.bemaerkning.show');
    }
}