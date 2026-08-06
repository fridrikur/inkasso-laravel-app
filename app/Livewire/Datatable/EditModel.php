<?php 
// app/Http/liveWire/Datatable/EditModel.php

namespace App\Livewire\Datatable;

use Livewire\Component;

class EditModel extends Component
{
    public $model;
    public $columns;

    public function mount($model, $columns)
    {
        $this->model = $model;
        $this->columns = $columns;
    }

    public function render()
    {
        return view('liveWire.datatable.edit-model');
    }

    public function update()
    {
        $this->model->save();
        $this->dispatch('close-modal');
    }
}