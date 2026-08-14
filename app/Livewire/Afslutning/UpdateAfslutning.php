<?php

namespace App\Livewire\Afslutning;

use Livewire\Component;
use App\Models\Afslutning;
use App\Livewire\forms\afslutningForm;

class UpdateAfslutning extends Component
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