<?php

namespace App\Livewire\Sagervalglistetype;

use Livewire\Component;
use App\Models\Sagervalglistetype;
use App\Models\Sagervalgliste;

class ShowSagervalglistetype extends Component
{
    public ?Sagervalglistetype $sagervalglistetype;
    public $type_id;
    
    public function opretnysagervalglistetype()
    {
        return redirect()->route('createsagervalglistetype');
    }
    public function opretnysagervalgliste()
    {
        return redirect()->to("/sagervalglister/".$this->sagervalglistetype->id."/create");
    }
    public function deletesagervalglistetype($id)
    {
        $sagervalglistetype = Sagervalglistetype::find($id);
        
        $sagervalglistetype->delete();
    }
    public function render()
    {
        $findnavn = Sagervalglistetype::all()->where('id',$this->sagervalglistetype->id)->first();
        $navn = $findnavn->navn;
        $sagervalglistetype ="";
        $sagervalglister = Sagervalgliste::with('sagervalglistetype')->get();
        return view('liveWire.sagervalglistetyper.show-sagervalglistetype',[
            'navn' => $navn,
            'sagervalglistetyper' => Sagervalglistetype::all(),
            'sagervalglister' => Sagervalgliste::all(),
            'sagervalglistetype' => $sagervalglistetype,
        ]);
    }
}
