<?php

namespace App\Livewire\forms;

use Livewire\Attributes\Validate;
use Livewire\form;
use App\Models\udlaeg;

class udlaegForm extends Form
{
    #[Validate('required')]
    public $tekst = '';
    #[Validate('required')] 
    public $forkortelse = '';
    
    public ?udlaeg $udlaeg;
    
    public function setudlaeg(udlaeg $udlaeg) 
    {
        $this->udlaeg = $udlaeg;
        $this->tekst = $udlaeg->tekst;
        $this->forkortelse = $udlaeg->forkortelse;
     }

    public function create(){
        $udlaeg = udlaeg::create(
            $this->all()
        );
        return $udlaeg->id;
    }

    public function update()
    {
        $this->udlaeg->update(
            $this->all()
        );
    }
}