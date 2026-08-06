<?php

namespace App\Livewire\Meta;
use Livewire\Component;
use App\Models\Meta;
use App\Livewire\forms\MetaForm;

class UpdateMeta extends Component
{
    public ?Meta $meta;
    public MetaForm $form;
    
    public function mount(Meta $meta)
    {
        $this->form->meta = $meta;
        $this->form->SetMeta($meta);
    }
    public function save(Meta $meta)
    {
        $this->form->update();
        return redirect()->to('/meta');
    }
    public function render()
    {
        return view('liveWire.meta.create-meta');
    }
}