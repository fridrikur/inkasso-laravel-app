<?php

namespace App\Livewire\Kreditorer;

use Livewire\Component;
use App\Models\Kreditorer;
use App\Models\Debitorer;
use App\Models\Sagsbehandler;
use App\Livewire\Forms\KreditorForm;
use App\Models\User;
use App\Models\Sager;

class UpdateKreditor extends Component
{
    public ?Kreditorer $kreditor;
    public KreditorForm $form;
    
    public function mount(Kreditorer $kreditor)
    {
        $this->form->kreditor = $kreditor; 
        $this->form->SetKreditor($kreditor);
    }
    public function save(Kreditorer $kreditor)
    {
        $this->form->update();
    }
    public function render()
    {
        $users = User::withCount('kreditorer')->get();
        $kreditorer = Kreditorer::all();
        $debitorer = Debitorer::all();
        $sagsbehandlere = Sagsbehandler::withCount('kreditor')->get();
        $sager = Sager::all();
        $hovedsagsbehandler = Sagsbehandler::all();
        
        return view('livewire.kreditorer.create-kreditor',['kreditorer' => $kreditorer, 'users' => $users, 'sagsbehandlere' => $sagsbehandlere, 'sager' => $sager, 'debitorer' => $debitorer, 'hovedsagsbehandler' => $hovedsagsbehandler]);
    }
}