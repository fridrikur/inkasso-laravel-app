<?php

namespace App\Livewire\Konsulenter;

use App\Livewire\forms\KonsulentForm;
use Livewire\Component;
use App\Models\Konsulenter;
use App\Models\Meta;

class CreateKonsulent extends Component
{
    public KonsulentForm $form;
    public $hsb; //hovedkonsulent
    public $can_check = true;
    public $ssb; //skjultkonsulent
    public $nsb; //notifikationskonsulent
    public $prompt = "Opret konsulent";
    public $hovedkonsulentvalgt=false;
    public string $mode = 'create';

    public function save()
    {
        $this->validate();
        
        $konsulent = $this->form->create();
        $konsulent_id = $konsulent;
        $hovedkonsulentcheckbox = $this->hsb;
        $skjultkonsulentcheckbox = $this->ssb;
        $notifikationscheckbox = $this->nsb;
        if($hovedkonsulentcheckbox=='true'){
            $this->form->DetachHovedKonsulent();//fjern først den eksisterende hsb om den findes
            $konsulent = Konsulenter::all()->where('id',$konsulent_id)->first();
            $meta = Meta::all()->where('id','1')->first();
            $konsulent->hovedkonsulent()->attach($meta);
            $konsulent->notifikationskonsulent()->attach($meta);
        }
        if($skjultkonsulentcheckbox=='true'){
            $konsulent = Konsulenter::all()->where('id',$konsulent_id)->first();
            $meta = Meta::all()->where('id','1')->first();
            $konsulent->skjultkonsulent()->attach($meta);
        }
        if($notifikationscheckbox=='true'){
            $konsulent = Konsulenter::all()->where('id',$konsulent_id)->first();
            $meta = Meta::all()->where('id','1')->first();
            $konsulent->notifikationskonsulent()->attach($meta);
        }
        return redirect()->to('/konsulenter');
    }    
    public function render()
    {
        return view('liveWire.konsulenter.create-konsulent');
    }
}
