<?php

namespace App\Livewire\KTR;

use Livewire\Component;
use App\Models\KTR;

class KTRIndex extends Component
{
    public $ktrs;

    public function render()
    {
        $this->ktrs = KTR::all();
        return view('liveWire.ktr.index');
    }

    public function delete($id)
    {
        KTR::find($id)->delete();
    }
}