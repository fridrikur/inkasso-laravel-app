<?php

namespace App\Livewire\bemaerkning;

use Livewire\Component;
use App\Models\bemaerkning;
use App\Livewire\forms\bemaerkningForm;

class Updatebemaerkning extends Component
{
    public ?bemaerkning $bemaerkning;
    public bemaerkningForm $form;

    public function mount(bemaerkning $bemaerkning)
    {
        $this->form->setbemaerkning($bemaerkning);
    }

    public function save()
    {
        $this->form->update();
        return redirect()->to('/bemaerkning');
    }

    public function render()
    {
        return view('liveWire.bemaerkning.update-bemaerkning');
    }
}