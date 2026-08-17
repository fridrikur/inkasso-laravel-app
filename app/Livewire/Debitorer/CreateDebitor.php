<?php

namespace App\Livewire\Debitorer;

use App\Livewire\Forms\DebitorForm;
use Livewire\Component;
use App\Models\Debitorer;

class CreateDebitor extends Component
{
    public DebitorForm $form;
    
    public function save()
    {
        $this->validate();
        
        Debitorer::create(
            $this->form->all()
        );
 
        return redirect()->to('/debitorer');
    }    
    public function render()
    {
        $debitorer = Debitorer::all();

        return view('livewire.debitorer.create-debitor',['debitorer' => $debitorer]);
    }
}