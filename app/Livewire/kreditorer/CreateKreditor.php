<?php

namespace App\Livewire\Kreditorer;

use App\Livewire\forms\KreditorForm;
use Livewire\Component;
use App\Models\Kreditorer;
use App\Models\User;
use App\Models\Sagsbehandler;

class CreateKreditor extends Component
{
    public KreditorForm $form;

    public $usedLotusIds = [];

    /*
    |--------------------------------------------------------------------------
    | INIT
    |--------------------------------------------------------------------------
    */

    public function mount()
    {
        $this->usedLotusIds = Kreditorer::orderBy('lotusID')
            ->pluck('lotusID')
            ->toArray();
    }

    /*
    |--------------------------------------------------------------------------
    | LIVE STATUS
    |--------------------------------------------------------------------------
    */

    public function getLotusIdExistsProperty()
    {
        return in_array(
            $this->form->lotusID,
            $this->usedLotusIds
        );
    }

    public function getSuggestedLotusIdProperty()
    {
        return (Kreditorer::max('lotusID') ?? 0) + 1;
    }

    /*
    |--------------------------------------------------------------------------
    | SAVE
    |--------------------------------------------------------------------------
    */

    public function save()
    {
        $this->validate();

        Kreditorer::create(
            $this->form->all()
        );

        return redirect()->to('/kreditorer');
    }

    /*
    |--------------------------------------------------------------------------
    | VIEW
    |--------------------------------------------------------------------------
    */

    public function render()
    {
        $kreditorer = Kreditorer::withCount('sagsbehandlere')->first();
        $kreditor = null;

        return view('livewire.kreditorer.create-kreditor');
    }
}