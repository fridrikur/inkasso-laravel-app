<?php

namespace App\Livewire\Autotekster;

use Livewire\Component;
use App\models\Autotekster;

class ShowAutotekster extends Component
{
    public function deleteautotekst($id)
    {
        $autotekst = Autotekster::find($id);
 
        $autotekst->delete();
    }
    public function opretnyautotekst()
    {
        return redirect()->route('autotekster.create');
    }
    public function render()
    {
        return view('livewire.autotekster.show-autotekster',[
            'autotekster' => Autotekster::all(),
        ]);
    }
}