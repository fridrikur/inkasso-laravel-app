<?php

namespace App\Livewire\udlaeg;

use Livewire\Component;
use App\Livewire\forms\udlaegForm;
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
        return view('liveWire.udlaeg.create-udlaeg');
    }
}