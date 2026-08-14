<?php

namespace App\Livewire\Afslutning;

use Livewire\Component;
use App\Models\Afslutning;

class ShowAfslutning extends Component
{
    public $afslutning;

    public function mount(afslutning $afslutning)
    {
        $this->afslutning = $afslutning;
    }

    public function render()
    {
        return view('livewire.afslutning.show');
    }
}