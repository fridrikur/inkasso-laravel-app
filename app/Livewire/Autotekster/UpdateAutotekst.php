<?php

namespace App\Livewire\Autotekster;

use Livewire\Component;
use App\Models\Autotekster;
use App\Livewire\forms\AutotekstForm;

class UpdateAutotekst extends Component
{
    public ?Autotekster $autotekst;
    public AutotekstForm $form;
    
    public function mount(Autotekster $autotekst)
    {
        $this->form->autotekst = $autotekst;
        $this->form->SetAutotekst($autotekst);
    }
    public function save(Autotekster $autotekst)
    {
        $this->form->update();
    }
    public function render()
    {
        return view('livewire.autotekster.update-autotekst');
    }
}