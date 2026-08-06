<?php
// liveWire component
namespace App\liveWire;

use Livewire\Component;
use Livewire\form;
use App\Models\Konsulenter;

class DynamicModal extends Component
{
    public $model;
    public $form;
    // In your DynamicModal component
    public $showModal = true; // Set to true by default

    public function mount($model)
    {
        $this->model = $model;
    }
    public function getFormProperty()
    {
        $model = $this->model;
        dd($model);
        $modelClass = 'App\Models\\' . ucfirst($this->model) . 'er'; // Assuming your model names are pluralized with 'er'
        $modelInstance = new $modelClass(); // Create a new instance of the model
        $formClass = 'App\Livewire\forms\\' . ucfirst($this->model) . 'Form';
        $this->form = new $formClass($this, []);
        return $this->form;
    }

    public function save()
    {
        $form = $this->getFormProperty();
        $form->validate();
        if ($form->{$this->model}) {
            $form->update();
        } else {
            $form->create();
        }
    }

    protected $listeners = ['open-modal' => 'openModal'];

    public function openModal($model)
    {
        $this->model = $model;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function render()
    {
        return view('liveWire.dynamic-modal');
    }
}