<?php

namespace App\Livewire\Sagsbehandlere;

use App\Livewire\forms\SagsbehandlerForm;
use Livewire\Component;
use App\Models\Sagsbehandler;
use App\Models\Kreditorer;

class CreateSagsbehandler extends Component
{
    public SagsbehandlerForm $form;
    public ?Kreditorer $kreditor = null;

    public $hsb; // hovedsagsbehandler checkbox
    public $message = "Oprettelse af sagsbehandler";
    public $kreditornavn;

    public function mount(Kreditorer $kreditor)
    {
        $this->kreditor = $kreditor;
        $this->kreditornavn = $kreditor->navn;
    }

    public function save()
    {
        $sagsbehandler = $this->form->save(); // now returns model

        // attach kreditor relation
        $sagsbehandler->kreditor()->attach($this->kreditor);

        // handle hovedsagsbehandler checkbox
        if ($this->hsb === 'true') {
            $this->form->DetachHovedSagsbehandler($this->kreditor->id); 
            $sagsbehandler->hovedsagsbehandler()->attach($this->kreditor);
        }

        session()->flash('message', 'Sagsbehandler gemt og tilknyttet kreditor!');
        // return redirect()->to("/sagsbehandlere/"+$this->kreditor->id);
    }

    public function render()
    {
        return view('liveWire.sagsbehandlere.create-sagsbehandler', [
            'sagsbehandlere' => Sagsbehandler::all(),
        ]);
    }
}
