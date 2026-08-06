<?php

namespace App\Livewire\Sagervalglistetype;

use App\Livewire\forms\SagervalglistetypeForm;
use Livewire\Component;
use App\Models\Sagervalglistetype;

class Createsagervalglistetype extends Component
{
    public ?Sagervalglistetype $sagervalglistetype;
    public SagervalglistetypeForm $form;


    public function save()
    {
        $this->validate();
        
        $sagervalglistetype = $this->form->create();
        
        return redirect()->to('/sagervalglistetyper');
    }    
    public function render()
    {
        return view('liveWire.sagervalglistetyper.create-sagervalglistetype');
    }
}
