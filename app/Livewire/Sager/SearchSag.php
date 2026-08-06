<?php

namespace App\Livewire\Sager;
use Livewire\Component;
use App\Models\Sager;

class SearchSag extends Component
{
    public $search ='';

    public function render()
    {
        return view('liveWire.sager.search-sag', [
            'sager' => Sager::where('sagsnr', 'like', '%' . $this->search . '%')
                           ->orWhere('aktiv', 'like', '%' . $this->search . '%')
                           ->get(),
        ]);
    }
}