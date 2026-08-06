<?php

namespace App\Livewire\Meta;

use App\Livewire\forms\MetaForm;
use Livewire\Component;
use App\Models\Meta;

class CreateMeta extends Component
{
    
    public MetaForm $form;
 
    public function save()
    {
        $this->validate();
        
        $meta_tjek = Meta::all()->first();
        if($meta_tjek==null){
            $meta = Meta::create(
                $this->form->all()
            );
            $meta_id = $meta->id;
        }
        return redirect()->to('/meta');
    }    
    public function render()
    {
        $Meta = Meta::all();

        return view('liveWire.meta.create-meta',['meta' => $Meta]);
    }
}