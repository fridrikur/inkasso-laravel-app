<?php 
// app/Http/liveWire/UpdateModel.php

namespace App\Livewire\Datatable;

use Livewire\Component;
use Illuminate\Support\Str;

class UpdateModel extends Component
{
    public $columns;
    public $model;
    public $form = [];

    public function mount($model, $columns)
    {
        $this->model = $model;
        $this->columns = $columns;
        foreach ($columns as $column) {
            $this->form[$column] = $model->{$column};
        }
    }

    // UpdateModel.php
    public function render()
    {
        $modelName = class_basename($this->model);
        $componentName = strtolower($modelName) . '.create-' . strtolower($modelName);

        return view('liveWire.datatable.update-model', compact('componentName'));
    }
}