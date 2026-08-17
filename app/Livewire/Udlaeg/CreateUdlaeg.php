<?php

namespace App\Livewire\udlaeg;

use Livewire\Component;
use App\Livewire\Forms\udlaegForm;
use App\Models\udlaeg;

class Createudlaeg extends Component
{
    public udlaegForm $form;

    public function save()
    {
        $this->form->create();
        return redirect()->to('/udlaeg');
    }

    public function render()
    {
        return view('livewire.udlaeg.create-udlaeg');
    }
}