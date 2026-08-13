<?php

namespace App\Livewire\bemaerkning;

use Livewire\Component;
use App\Models\bemaerkning;

class BemaerkningIndex extends Component
{
    public $bemaerknings;

    public function render()
    {
        $this->bemaerknings = bemaerkning::all();
        return view('livewire.bemaerkning.index');
    }
    

    public function delete($id)
    {
        bemaerkning::find($id)->delete();
    }
}