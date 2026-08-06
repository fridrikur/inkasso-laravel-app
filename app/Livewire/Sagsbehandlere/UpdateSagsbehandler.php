<?php

namespace App\Livewire\Sagsbehandlere;

use Livewire\Component;
use App\Models\Sagsbehandler;
use App\Livewire\forms\SagsbehandlerForm;
use App\Models\Kreditorer;

class UpdateSagsbehandler extends Component
{
    public ?Sagsbehandlere $sagsbehandler;
    public SagsbehandlerForm $form;
    public $kreditor;
    public $hsb;
    public $can_check = true;
    public $message = "Redigering af sagsbehandler";
    public $kreditornavn="";
    
    public function mount(Sagsbehandlere $sagsbehandler)
    {
        $this->form->sagsbehandler = $sagsbehandler;
        $this->form->setsagsbehandler($sagsbehandler);
        if($this->form->ErdetteHovedSagsbehandler($sagsbehandler)=='true'){
            $this->hsb =  ['hsb']; //checked
            $this->can_check = false;
        }  
        // $this->form->SetKreditor($sagsbehandler);
        $sagsbehandler_id = $sagsbehandler->id;
        $sagsbehandler= Sagsbehandlere::withCount('kreditor')->where('id',$sagsbehandler_id)->first();
        if($sagsbehandler->kreditor_count=='1'){
            $sagsbehandler= Sagsbehandlere::with('kreditor')->where('id',$sagsbehandler_id)->get();
            foreach ($sagsbehandler as $sagsbehandler){
                    foreach ($sagsbehandler->kreditor as $sagsbehandler){
                        $kreditorID = $sagsbehandler->pivot->kreditor_id;
                        $kreditor = Kreditorer::all()->where('id',$kreditorID)->first();
                        $this->kreditornavn = $kreditor->navn;
                }
            }
        }
    }
    public function save(Sagsbehandlere $sagsbehandler)
    {
        $this->form->update();
        
        $sagsbehandler = $this->form->sagsbehandler;
        
        $kreditor_id = $this->form->ReturnKreditorID($sagsbehandler);
        $sagsbehandlercheckbox = $this->hsb;
        if($sagsbehandlercheckbox=='selected'){
            $this->form->DetachHovedSagsbehandler($kreditor_id);//fjern først den eksisterende hsb om den findes
            $kreditor = Kreditorer::all()->where('id',$kreditor_id)->first();
            $sagsbehandler->hovedsagsbehandler()->attach($kreditor);
        }
    }
    public function render()
    {
        return view('liveWire.sagsbehandlere.create-sagsbehandler');
    }
}
