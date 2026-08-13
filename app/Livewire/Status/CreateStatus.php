<?php

namespace App\Livewire\Status;

use Livewire\Component;
use App\Livewire\Forms\StatusForm;
use App\Models\Status;

class CreateStatus extends Component
{
    public StatusForm $form;

    public function save()
    {
        $this->form->validate(); // ✅ use validation from the form
        $this->form->create();

        session()->flash('success', 'Status oprettet!');
        return redirect()->route('status.index');
    }

    public function render()
    {
        return view('livewire.status.create-status');
    }
}
