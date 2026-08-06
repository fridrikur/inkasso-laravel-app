<?php

namespace App\Livewire\forms;

use Livewire\Attributes\Validate;
use Livewire\form;
use App\Models\Sagervalgliste;

class SagervalglisteForm extends Form
{
    #[Validate('required|unique:sagervalglistes|min:2')]
    public $navn = '';
    public $forkortelse = '';
    
    public ?Sagervalgliste $sagervalgliste;
    
    public function SetSagervalgliste(Sagervalgliste $sagervalgliste) 
    {
        $this->sagervalgliste = $sagervalgliste;
        $this->navn = $sagervalgliste->navn;
        $this->forkortelse = $sagervalgliste->forkortelse;
    }
    public function store()
    {
        $this->validate();
        $this->sagervalgliste->update(
            $this->all()
        );
    }
    
    public function create(){
        $sagervalgliste = Sagervalgliste::create(
        $this->all()
    );
    $sagervalgliste_id = $sagervalgliste->id;
    return $sagervalgliste_id;
    }
    public function update()
    {
        $this->sagervalgliste->update(
            $this->all()
        );
    }
 } 