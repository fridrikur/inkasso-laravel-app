<?php

namespace App\Livewire\afslutning;

use Livewire\Component;
use App\Models\afslutning;

class Showafslutning extends Component
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