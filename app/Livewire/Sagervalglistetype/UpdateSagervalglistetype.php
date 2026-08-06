<?php

namespace App\Livewire\sagervalglistetype;

use Livewire\Component;
use App\Models\Sagervalglistetype;
use App\Models\Sagervalgliste;
use App\Livewire\forms\SagervalglistetypeForm;

class UpdateSagervalglistetype extends Component
{
    public ?Sagervalglistetype $sagervalglistetype;
    public sagervalglistetypeForm $form;
    public $type_id;
    
    public function mount(sagervalglistetype $sagervalglistetype)
    {
        $this->form->sagervalglistetype = $sagervalglistetype;
        $this->form->Setsagervalglistetype($sagervalglistetype);
    }
    public function save(sagervalglistetype $sagervalglistetype)
    {
        $this->form->update();
        $sagervalglistetype_id = $this->form->sagervalglistetype->id;
    }
    public function render()
    {
        $sagervalglistetype ="";
        $sagervalglister = Sagervalgliste::with('sagervalglistetype')->get();
        return view('liveWire.sagervalglistetyper.create-sagervalglistetype',['sagervalglistetype' => $sagervalglistetype, 'sagervalglister' => $sagervalglister]);
    }
    public function opretnysagervalgliste()
    {
        return redirect()->to("/sagervalglister/".$this->form->sagervalglistetype->id."/create");
    }
}