<?php

namespace App\Livewire\Sagervalgliste;

use Livewire\Component;
use App\Models\Sagervalgliste;
use App\Models\Sagervalglistetype;

class ShowSagervalgliste extends Component
{
    public $sagervalglistetype = '';
    public function opretnysagervalgliste()
    {
        return redirect()->to('/sagervalglister/create');
    }
    public function deletesagervalgliste($id)
    {
        $sagervalgliste = Sagervalgliste::find($id);
 
        $sagervalgliste->delete();
    }
    public function render()
    {
        return view('liveWire.sagervalglister.show-sagervalgliste',[
            'sagervalglister' => Sagervalgliste::with('sagervalglistetype')->get(),
            'sagervalglistetype' => $this->sagervalglistetype,
        ]);
    }
}
