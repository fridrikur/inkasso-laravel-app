<?php

namespace App\Livewire\Sagervalgliste;

use App\Livewire\forms\SagervalglisteForm;
use Livewire\Component;
use App\Models\Sagervalgliste;
use App\Models\Sagervalglistetype;

class Createsagervalgliste extends Component
{
    public ?Sagervalglistetype $sagervalglistetype;
    public ?Sagervalgliste $sagervalgliste;
    public SagervalglisteForm $form;
    public $id = '';
    
    public function save()
    {
        $this->validate();

        $sagliste = sagervalgliste::create(
            $this->form->all()
        );
        $sagliste_id = $this->id;
        $type_id = $this->sagervalglistetype;
        $sagliste->sagervalglistetype()->attach($type_id);
        
        return redirect()->to('/sagervalglistetyper');
    }    
    public function render()
    {
        return view('liveWire.sagervalglister.create-sagervalgliste');
    }
}
