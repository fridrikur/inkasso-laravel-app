<?php

namespace App\Livewire\Sagervalglistetype;

use Livewire\Component;
use App\Models\Sagervalglistetype;
use App\Models\Sagervalgliste;

class ListeSagervalglistetype extends Component
{
    public function placeholder()
    {
        return <<<'HTML'
        <div>
            Loading
            <!-- Loading spinner... -->
        </div>
        HTML;
    }
    public function render()
    {
        return view('liveWire.sagervalglistetyper.liste-sagervalglistetype',[
            'sagervalglistetyper' => Sagervalglistetype::all(),
            'sagervalglister' => Sagervalgliste::with('sagervalglistetype')->get(),
        ]);
    }
}