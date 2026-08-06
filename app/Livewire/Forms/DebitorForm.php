<?php

namespace App\Livewire\forms;

use Livewire\Attributes\Validate;
use Livewire\form;
use App\models\Debitorer;

class DebitorForm extends Form
{
    #[Validate('required|min:5')]
    public $navn = '';
    #[Validate('required|min:8')]
    public $pnr = '';
    
    public ?Debitorer $debitor;

    public function SetDebitor(Debitorer $debitor) 
    {
        $this->debitorer = $debitor;
        $this->navn = $debitor->navn;
        $this->pnr = $debitor->pnr;
    }
    public function store()
    {
        $this->validate();
        $this->debitor->update(
            $this->all()
        );
    }
    public function create(){
        $debitorer = Debitorer::create(
            $this->all()
        );
        $debitor_id = $debitor->id;
        return $debitor_id;
    }
    public function update()
    {
        $this->validate();
        $this->debitor->update(
            $this->all()
        );
    }
}