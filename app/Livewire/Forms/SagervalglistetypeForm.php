<?php

namespace App\Livewire\forms;

use Livewire\Attributes\Validate;
use Livewire\form;
use App\Models\Sagervalglistetype;

class SagervalglistetypeForm extends Form
{
    #[Validate('required|unique:sagervalglistetyper|min:2')]
    public $navn = '';
    
    public ?Sagervalglistetype $sagervalglistetype;

    public function SetSagervalglistetype(Sagervalglistetype $sagervalglistetype) 
    {
        $this->sagervalglistetype = $sagervalglistetype;
        $this->navn = $sagervalglistetype->navn;
    }
    public function store()
    {
        $this->validate();
        $this->sagervalglistetype->update(
            $this->all()
        );
    }
    
    public function create(){
        $sagervalglistetype = Sagervalglistetype::create(
        $this->all()
    );
    $sagervalglistetype_id = $sagervalglistetype->id;
    return $sagervalglistetype_id;
    }
    public function update()
    {
        $this->sagervalglistetype->update(
            $this->all()
        );
    }
 } 