<?php

namespace App\Livewire\forms;

use Livewire\Attributes\Validate;
use Livewire\Form;
use App\Models\Debitorer;

class DebitorForm extends Form
{
    #[Validate('required|min:2')]
    public $navn = '';

    public $debitorid = '';
    public $co = '';
    public $adresse = '';
    public $postnr = '';
    public $email = '';
    public $tlf = '';
    public $mobil = '';
    public $adropl = '';

    #[Validate('required|min:5')]
    public $pnr = '';

    public $kontakt_bemaerkning = '';
    
    public ?Debitorer $debitor;

    public function SetDebitor(Debitorer $debitor) 
    {
        $this->debitor = $debitor;
        
        $this->debitorid           = $debitor->debitorid ?? '';
        $this->navn                = $debitor->navn;
        $this->co                  = $debitor->co;
        $this->adresse             = $debitor->adresse;
        $this->postnr              = $debitor->postnr;
        $this->email               = $debitor->email;
        $this->tlf                 = $debitor->tlf;
        $this->mobil               = $debitor->mobil;
        $this->adropl              = $debitor->adropl;
        $this->pnr                 = $debitor->pnr;
        $this->kontakt_bemaerkning = $debitor->kontakt_bemaerkning;
    }

    public function store()
    {
        $this->validate();
        
        // Fjern 'debitor' og 'debitorid' (hvis sidstnævnte ikke er en kolonne)
        Debitorer::create($this->except(['debitor', 'debitorid']));
    }
    
    public function update()
    {
        $this->validate();
        
        $data = $this->except(['debitor', 'debitorid']);

        // Sørg for at tomme datoer bliver sendt som null til databasen
        if (empty($data['adropl'])) {
            $data['adropl'] = null;
        }

        $this->debitor->update($data);
    }
}