<?php

namespace App\Livewire\udlaeg;

use Livewire\Component;
use App\Models\udlaeg;

class UdlaegIndex extends Component
{
    public $udlaegs;

    public function render()
    {
        $this->udlaegs = udlaeg::all();
        return view('livewire.udlaeg.index');
    }
    

    public function delete($id)
    {
        udlaeg::find($id)->delete();
    }
}