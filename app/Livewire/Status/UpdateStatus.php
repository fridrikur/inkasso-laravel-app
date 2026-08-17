<?php

namespace App\Livewire\Status;

use Livewire\Component;
use App\Models\Status;
use App\Livewire\Forms\statusForm;

class UpdateStatus extends Component
{
    public ?Status $status;
    public statusForm $form;
    
    public function mount(Status $status)
    {
        $this->form->status = $status;
        $this->form->Setstatus($status);
    }
    public function save(Status $status)
    {
        $this->form->update();
    }
    public function render()
    {
        return view('livewire.status.update-status');
    }
}