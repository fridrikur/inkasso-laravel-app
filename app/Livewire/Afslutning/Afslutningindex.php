<?php

namespace App\Livewire\afslutning;

use Livewire\Component;
use App\Models\afslutning;

class afslutningIndex extends Component
{
    public $afslutnings;

    public function render()
    {
        $this->afslutnings = afslutning::all();
        return view('liveWire.afslutning.index');
    }

    public function delete($id)
    {
        afslutning::find($id)->delete();
    }
}