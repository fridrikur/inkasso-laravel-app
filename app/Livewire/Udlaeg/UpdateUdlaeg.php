<?php

namespace App\Livewire\udlaeg;

use Livewire\Component;
use App\Models\udlaeg;
use App\Livewire\forms\udlaegForm;

class Updateudlaeg extends Component
{
    public ?udlaeg $udlaeg;
    public udlaegForm $form;

    public function mount(udlaeg $udlaeg)
    {
        $this->form->setudlaeg($udlaeg);
    }

    public function save()
    {
        $this->form->update();
        return redirect()->to('/udlaeg');
    }

    public function render()
    {
        return view('liveWire.udlaeg.update-udlaeg');
    }
}