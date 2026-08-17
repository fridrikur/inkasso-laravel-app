<?php

namespace App\Livewire\Autotekster;

use Livewire\Component;
use App\Livewire\Forms\AutotekstForm;
use App\Models\Autotekster;
use App\Models\Sager;
use App\Models\Tokens;

class CreateAutotekst extends Component
{
    public AutotekstForm $form;

    public $autotekst ='';
    
    public function save()
    {
        $this->validate();
        $autotekst = Autotekster::create(
            $this->form->all()
        );
        return redirect()->to('/autotekster');
    }
    public function render()
    { 
        // $dialoger = Dialoger::with('dialogtype')->get();
        return view('livewire.autotekster.create-autotekst');
    }
}    