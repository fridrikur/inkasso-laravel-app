<?php

namespace App\Livewire\Sager;

use Livewire\Component;
use App\Models\Kreditorer;
use App\Models\KreditorSagerField;

class AdminSagerFilter extends Component
{
    public $kreditor;
    public $fields = [];

    public $allFields = [
        'sagsnr','afsluttet','faktureret','betalt','fakturadato','modtaget',
        'senesterapport','opgivet','hovedstol','renter','gebyr','ialt',
        'startgebyr','restgaeld','restgaeld_dkg','afdragsordning','indbetalt',
        'mdlydelse','n_mdlydelse','stelnr','aktiv'
    ];

    public function mount(Kreditorer $kreditor)
    {
        $this->kreditor = $kreditor;

        foreach($this->allFields as $field) {
            $pivot = $kreditor->sagerFields()->where('field_name', $field)->first();
            if ($pivot) {
                $this->fields[$field] = [
                    'visible'  => (bool) $pivot->visible,
                    'required' => (bool) $pivot->required,
                    'editable' => (bool) $pivot->editable,
                ];
            } else {
                $this->fields[$field] = [
                    'visible'  => false,
                    'required' => false,
                    'editable' => true,
                ];
            }
        }

    }

    public function save()
    {
        foreach($this->fields as $fieldName => $data){
            KreditorSagerField::updateOrCreate(
                ['kreditor_id'=>$this->kreditor->id,'field_name'=>$fieldName],
                $data
            );
        }

        // Re-hydrate from DB
        $this->mount($this->kreditor);

        session()->flash('success','Sager fields saved!');
    }




    public function render()
    {
        return view('liveWire.sager.admin-sager-filter');
    }
}
