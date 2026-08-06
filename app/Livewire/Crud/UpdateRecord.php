<?php
// app/Http/liveWire/UpdateModel.php

namespace App\Livewire\Crud;

use Livewire\Component;
use Illuminate\Support\Str;

class UpdateRecord extends Component
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
    public function update()
    {
        $this->model->update($this->form);
        $this->dispatch('refresh-page');
    }
}