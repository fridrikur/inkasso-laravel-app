<?php

namespace App\Livewire\sagervalgliste;

use Livewire\Component;
use App\Models\Sagervalgliste;
use App\Models\Sagervalglistetype;
use App\Livewire\forms\SagervalglisteForm;

class UpdateSagervalgliste extends Component
{
    public ?Sagervalgliste $sagervalgliste;
    public sagervalglisteForm $form;
    
    public function mount(sagervalgliste $sagervalgliste)
    {
        $this->form->sagervalgliste = $sagervalgliste;
        $this->form->Setsagervalgliste($sagervalgliste);
    }
    public function save(sagervalgliste $sagervalgliste)
    {
        $this->form->update();
        $sagervalgliste_id = $this->form->sagervalgliste->id;
    }
    public function render()
    {
        return view('liveWire.sagervalglister.create-sagervalgliste');
    }
}
