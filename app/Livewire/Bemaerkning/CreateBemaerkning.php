<?php

namespace App\Livewire\bemaerkning;

use Livewire\Component;
use App\Livewire\forms\bemaerkningForm;
use App\Models\bemaerkning;

class Createbemaerkning extends Component
{
    public bemaerkningForm $form;

    public function save()
    {
        $this->form->create();
        return redirect()->to('/bemaerkning');
    }

    public function render()
    {
        return view('livewire.bemaerkning.create-bemaerkning');
    }
}