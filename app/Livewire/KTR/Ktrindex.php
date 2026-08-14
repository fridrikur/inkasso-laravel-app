<?php

namespace App\Livewire\KTR;

use Livewire\Component;
use App\Models\KTR;

class KTRindex extends Component
{
    public $ktrs;

    public function render()
    {
        $this->ktrs = KTR::all();
        return view('livewire.ktr.index');
    }

    public function delete($id)
    {
        KTR::find($id)->delete();
    }
}