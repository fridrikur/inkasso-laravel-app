<?php

namespace App\Livewire\udlaeg;

use Livewire\Component;
use App\Models\udlaeg;

class Showudlaeg extends Component
{
    public $udlaeg;

    public function mount(udlaeg $udlaeg)
    {
        $this->udlaeg = $udlaeg;
    }

    public function render()
    {
        return view('livewire.udlaeg.show');
    }
}