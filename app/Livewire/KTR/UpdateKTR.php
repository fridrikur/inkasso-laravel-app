<?php

namespace App\Livewire\KTR;

use Livewire\Component;
use App\Models\KTR;
use App\Livewire\forms\KTRForm;

class UpdateKTR extends Component
{
    public ?KTR $ktr;
    public KTRForm $form;

    public function mount(KTR $ktr)
    {
        $this->form->setKTR($ktr);
    }

    public function save()
    {
        $this->form->update();
        return redirect()->to('/ktr');
    }

    public function render()
    {
        return view('liveWire.ktr.update-ktr');
    }
}