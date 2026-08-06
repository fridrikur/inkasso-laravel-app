<?php

namespace App\Livewire\forms;

use Livewire\Attributes\Validate;
use Livewire\form;
use App\Models\bemaerkning;

class bemaerkningForm extends Form
{
    #[Validate('required')]
    public $tekst = '';
    #[Validate('required')] 
    public $forkortelse = '';
    
    public ?bemaerkning $bemaerkning;
    
    public function setbemaerkning(bemaerkning $bemaerkning) 
    {
        $this->bemaerkning = $bemaerkning;
        $this->tekst = $bemaerkning->tekst;
        $this->forkortelse = $bemaerkning->forkortelse;
     }

    public function create(){
        $bemaerkning = bemaerkning::create(
            $this->all()
        );
        return $bemaerkning->id;
    }

    public function update()
    {
        $this->bemaerkning->update(
            $this->all()
        );
    }
}