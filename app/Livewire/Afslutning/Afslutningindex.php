<?php

namespace App\Livewire\Afslutning;

use Livewire\Component;
use App\Models\afslutning;

class AfslutningIndex extends Component
{
    public $afslutnings;

    public function render()
    {
        $this->afslutnings = afslutning::all();
        return view('livewire.afslutning.index');
    }

    public function delete($id)
    {
        afslutning::find($id)->delete();
    }
}