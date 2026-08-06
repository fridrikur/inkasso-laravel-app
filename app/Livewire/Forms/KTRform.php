<?php

namespace App\Livewire\forms;

use Livewire\Attributes\Validate;
use Livewire\form;
use App\Models\KTR;

class KTRForm extends Form
{
    #[Validate('required')]
    public $tekst = '';
    #[Validate('required')] 
    public $forkortelse = '';
    
    public ?KTR $KTR;
    
    public function setKTR(KTR $KTR) 
    {
        $this->KTR = $KTR;
        $this->tekst = $KTR->tekst;
        $this->forkortelse = $KTR->forkortelse;
     }

    public function create(){
        $KTR = KTR::create(
            $this->all()
        );
        return $KTR->id;
    }

    public function update()
    {
        $this->KTR->update(
            $this->all()
        );
    }
}