<?php

namespace App\Livewire\Meta;

use Livewire\Component;
use App\Models\Meta;

class ShowMeta extends Component
{
    public function render()
    {
        return view('liveWire.meta.show-meta',[
            'meta' => Meta::all(),
        ]);
    }
}
