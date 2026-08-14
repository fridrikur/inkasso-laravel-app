<?php

namespace App\Livewire\Debitorer;

use Livewire\Component;
use App\Models\Debitorer;
use App\Livewire\forms\DebitorForm;

class UpdateDebitor extends Component
{
    public DebitorForm $form;
    
    public function mount(Debitorer $debitor)
    {
        $this->form->SetDebitor($debitor);
    }

    public function save()
    {
        $this->form->update();
        
        // Sæt en besked i sessionen og send brugeren tilbage til oversigten
        session()->flash('message', 'Debitor blev opdateret succesfuldt!');
        
        return redirect()->route('debitorer.index');
    }

    public function render()
    {
        return view('livewire.debitorer.edit-debitor');
    }
}