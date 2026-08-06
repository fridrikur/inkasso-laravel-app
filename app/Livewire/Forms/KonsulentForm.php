<?php

namespace App\Livewire\forms;

use Livewire\Attributes\Validate;
use Livewire\form;
use App\Models\Konsulenter;
use Illuminate\Support\Facades\DB;

class KonsulentForm extends Form
{
    #[Validate('required|min:2|unique:konsulenters,navn,{{id}}')]
    public $navn = '';

    #[Validate('required|min:8|unique:konsulenters,tlf,{{id}}')]
    public $tlf = '';

    #[Validate('required|email|min:8|unique:konsulenters,email,{{id}}')]
    public $email = '';

    #[Validate('required|min:8|unique:konsulenters,mobil,{{id}}')]
    public $mobil = '';
    public $hsb = '';
    public $kreditor = '';
    public $ssb = '';
    public $nsb = '';
    public $hovedkonsulentvalgt = '';
    
    public ?Konsulenter $konsulent;

    public function SetKonsulent(Konsulenter $konsulent) 
    {
        $this->konsulenter = $konsulent;
        $this->navn = $konsulent->navn;
        $this->email = $konsulent->email;
        $this->tlf = $konsulent->tlf;
        $this->mobil = $konsulent->mobil;
    }
    public function store()
    {
        $this->validate();
        $this->konsulent->update(
            $this->all()
        );
    }
    public function DetachHovedKonsulent() 
    {
        DB::table('hoved_konsulent')->truncate();

    }
    public function DetachSkjultKonsulent($konsulent_id) 
    {
            DB::table('skjult_konsulent')->where('konsulent_id',$konsulent_id)->delete();

    }
    public function ErdetteHovedKonsulent($konsulent) 
    {
        $konsulent = Konsulenter::withCount('hovedkonsulent')->find($konsulent->id);
        if ($konsulent && $konsulent->hovedkonsulent_count == 1) {
            return "true";
        }
    }
    public function ErdenneKonsulentSkjult($konsulent) 
    {
        $konsulent = Konsulenter::withCount('skjultkonsulent')->find($konsulent->id);
        if ($konsulent && $konsulent->skjultkonsulent_count == 1) {
            return "true";
        }
    }
    public function ModtagerdenneKonsulentNotifikationer($konsulent) 
    {
        $konsulent = Konsulenter::withCount('notifikationskonsulent')->find($konsulent->id);
        if($konsulent && $konsulent->notifikationskonsulent_count=='1'){
            return "true";
        }
    }
    public function DetachNotifikationskonsulent($konsulent_id) 
    {
        $konsulent = Konsulenter::all()->first();
        $konsulent = $konsulent::withCount('notifikationskonsulent')->first();
        if($konsulent->notifikationskonsulent_count !=="0"){
            DB::table('notifikations_konsulent')->where('konsulent_id',$konsulent_id)->delete();
        }
    }
    public function create(){
        $konsulent = Konsulenter::create(
        $this->all()
    );
    $konsulent_id = $konsulent->id;
    return $konsulent_id;
    }
    public function update()
    {
        $this->konsulent->update(
            $this->all()
        );
        return true; // indicate success
    }
 }