<?php

namespace App\Livewire\Debitorer;

use Livewire\Component;
use App\Models\Debitorer;
use App\Livewire\forms\DebitorForm;

class UpdateDebitor extends Component
{
    public ?Debitorer $debitor;
    public DebitorForm $form;
    
    public function mount(Debitorer $debitor)
    {
        $this->form->debitor = $debitor;
        $this->form->SetDebitor($debitor);
    }
    public function save(Debitorer $debitor)
    {
        $this->form->update();
    }
    public function render()
    {
        return view('livewire.debitorer.create-debitor');
    }
}
