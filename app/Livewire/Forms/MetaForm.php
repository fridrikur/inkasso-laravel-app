<?php

namespace App\Livewire\forms;

use Livewire\Attributes\Validate;
use Livewire\form;
use app\models\Meta;

class MetaForm extends Form
{
    #[Validate('required|min:3')]
    public $navn = '';
    
    public ?Meta $meta;

    public function SetMeta(Meta $meta) 
    {
        $this->meta = $meta;
        $this->navn = $meta->navn;
    }
    public function store()
    {
        $this->validate();
        $this->meta->update(
            $this->all()
        );
    }
    public function create(){
        $meta = Meta::create(
            $this->all()
        );
        $meta_id = $meta->id;
        return $meta_id;
    }
    public function update()
    {
        $this->validate();
        $this->meta->update(
            $this->all()
        );
    }
}