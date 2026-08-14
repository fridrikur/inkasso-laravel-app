<?php

namespace App\Livewire\Afslutning;

use Livewire\Component;
use App\Livewire\forms\afslutningForm;
use App\Models\afslutning;

class CreateAfslutning extends Component
{
    public afslutningForm $form;

    public function save()
    {
        $this->form->create();
        return redirect()->to('/afslutning');
    }

    public function render()
    {
        return view('livewire.afslutning.create-afslutning');
    }
}