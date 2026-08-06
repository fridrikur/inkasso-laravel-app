<?php

namespace App\Livewire\Sagervalglistetype;

use Livewire\Component;
use App\Models\Sagervalglistetype;

class ShowSagervalglistetyper extends Component
{
    public function opretnysagervalglistetype()
    {
        return redirect()->to('/sagervalglistetyper/create');
    }
    public function deletesagervalglistetype($id)
    {
        $sagervalglistetype = Sagervalglistetype::find($id);
        
        $sagervalglistetype->delete();
    }
    public function render()
    {
        return view('liveWire.sagervalglistetyper.show-sagervalglistetyper',[
            'sagervalglistetyper' => Sagervalglistetype::all(),
        ]);
    }
}
