<?php

namespace App\Livewire\forms;

use Livewire\Attributes\Validate;
use Livewire\form;
use App\Models\Autotekster;

class AutotekstForm extends Form
{
    #[Validate('required')]
    public $tekst = '';
    #[Validate('required|min:5')] 
    public $dato = '';
    
    public ?Autotekster $autotekst;
    
    public function SetAutotekst(Autotekster $autotekst) 
    {
        $this->autotekst = $autotekst;
        $this->tekst = $autotekst->tekst;
        $this->dato = $autotekst->dato;
    }
    public function store()
    {
        $this->validate();
        $this->autotekst->update(
            $this->all()
        );
    }
    public function create(){
        $autotekster = Autotekster::create(
            $this->all()
        );
        $autotekst_id = $autotekst->id;
        return $autotekst_id;
    }
    public function update()
    {
        $this->validate();
        $this->autotekst->update(
            $this->all()
        );
    }
}