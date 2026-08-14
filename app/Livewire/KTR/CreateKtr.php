<?php

namespace App\Livewire\KTR;

use Livewire\Component;
use App\Livewire\Forms\KTRForm;
use App\Models\KTR;

class CreateKTR extends Component
{
    public KTRForm $form;

    public function save()
    {
        $this->form->create();
        return redirect()->to('/ktr');
    }

    public function render()
    {
        return view('livewire.ktr.create-ktr');
    }
}