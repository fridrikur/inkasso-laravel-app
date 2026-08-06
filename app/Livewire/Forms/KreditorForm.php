<?php

namespace App\Livewire\forms;

use Livewire\Attributes\Validate;
use Livewire\form;
use App\models\Kreditorer;

class KreditorForm extends Form
{
    #[Validate('required|min:5')]
    public $navn = '';
    #[Validate('required')]
    public $lotusID = '';
    
    public ?Kreditorer $kreditor;

    public function SetKreditor(Kreditorer $kreditor) 
    {
        $this->kreditorer = $kreditor;
        $this->navn = $kreditor->navn;
        $this->lotusID = $kreditor->lotusID;
    }
    public function store()
    {
        $this->validate();
        $this->kreditor->update(
            $this->all()
        );
    }
    public function create(){
        $kreditor = Kreditorer::create(
            $this->all()
        );
        $kreditor_id = $kreditor->id;
        return $kreditor_id;
    }
    public function update()
    {
        $this->validate();
        $this->kreditor->update(
            $this->all()
        );
    }
}