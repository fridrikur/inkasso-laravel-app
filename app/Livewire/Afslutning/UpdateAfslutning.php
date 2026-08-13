<?php

namespace App\Livewire\afslutning;

use Livewire\Component;
use App\Models\afslutning;
use App\Livewire\forms\afslutningForm;

class Updateafslutning extends Component
{
    public ?afslutning $afslutning;
    public afslutningForm $form;

    public function mount(afslutning $afslutning)
    {
        $this->form->setafslutning($afslutning);
    }

    public function save()
    {
        $this->form->update();
        return redirect()->to('/afslutning');
    }

    public function render()
    {
        return view('livewire.afslutning.update-afslutning');
    }
}