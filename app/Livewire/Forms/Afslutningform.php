<?php

namespace App\Livewire\forms;

use Livewire\Attributes\Validate;
use Livewire\form;
use App\Models\Afslutning;

class afslutningForm extends Form
{
    #[Validate('required')]
    public $tekst = '';
    #[Validate('required')] 
    public $forkortelse = '';
    
    public ?afslutning $afslutning;
    
    public function setafslutning(afslutning $afslutning) 
    {
        $this->afslutning = $afslutning;
        $this->tekst = $afslutning->tekst;
        $this->forkortelse = $afslutning->forkortelse;
     }

    public function create(){
        $afslutning = afslutning::create(
            $this->all()
        );
        return $afslutning->id;
    }

    public function update()
    {
        $this->afslutning->update(
            $this->all()
        );
    }
}