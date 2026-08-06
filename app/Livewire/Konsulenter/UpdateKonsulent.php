<?php

namespace App\Livewire\Konsulenter;

use Livewire\Component;
use App\Models\Konsulenter;
use App\Livewire\forms\KonsulentForm;
use App\Models\Meta;
use Illuminate\Database\QueryException;

class UpdateKonsulent extends Component
{
    // public ?Konsulenter $konsulent;
    public KonsulentForm $form;
    public $hsb;
    public $can_check = true;
    public $ssb;
    public $nsb;
    public $prompt='Rediger konsulent';
    public $hovedkonsulentvalgt=false;
    public string $mode = 'update';

    public function mount(Konsulenter $konsulent)
    {
        // Correctly pass the component first
        $this->form = new KonsulentForm($this, $konsulent);
        $this->form->konsulent = $konsulent;
        $this->form->SetKonsulent($konsulent);
        if($this->form->ErdetteHovedKonsulent($konsulent)=='true'){
            $this->hsb =  ['hsb'];
            $this->can_check = false;
            $this->hovedkonsulentvalgt='true';
        }
        if($this->form->ErdenneKonsulentSkjult($konsulent)=='true'){
            $this->ssb =  ['ssb'];
        }
        if($this->form->ModtagerdenneKonsulentNotifikationer($konsulent)=='true'){
            $this->nsb =  ['nsb'];
        }
    }
    
    
    public function save(Konsulenter $konsulent)
    {
        try {
            $this->form->update();
            $konsulent_id = $this->form->konsulent->id;
            $hovedkonsulentcheckbox = $this->hsb;
            $skjultkonsulentcheckbox = $this->ssb;
            $notifikationskonsulentcheckbox = $this->nsb;

            if(!$notifikationskonsulentcheckbox){
                $this->form->DetachNotifikationsKonsulent($konsulent_id);
            }
            if(!$skjultkonsulentcheckbox){
                $this->form->DetachSkjultKonsulent($konsulent_id);
            }
            if($hovedkonsulentcheckbox == 'true'){
                $this->form->DetachHovedKonsulent();
                $meta = Meta::find(1);
                $this->form->konsulent->hovedkonsulent()->attach($meta);
                $this->form->DetachSkjultKonsulent($konsulent_id);
            }
            if($skjultkonsulentcheckbox == 'true'){
                $meta = Meta::find(1);
                $this->form->konsulent->skjultkonsulent()->sync($meta);
            }
            if($notifikationskonsulentcheckbox == 'true'){
                $meta = Meta::find(1);
                $this->form->konsulent->notifikationskonsulent()->sync($meta);
            }

            \Log::info('✅ Konsulent updated successfully, firing toaster...');
            $this->dispatch('toast', message: 'Konsulent gemt succesfuldt!', type: 'success', icon: 'check');
            // 👇 fire redirect event (Alpine will handle it with delay)
            $this->dispatch('redirect', url: '/konsulenter');
        } catch (\Illuminate\Database\QueryException $e) {

        // Duplicate entry
        if ($e->getCode() === '23000') {
            $this->addError('duplicate', 'Et af felterne findes allerede!');
            $this->dispatch('toast', 
                message: 'Et af felterne findes allerede!', 
                type: 'error', 
                icon: 'x-circle'
            );
        }

        // Data truncated / invalid value
        elseif ($e->getCode() === '01000') {
            $this->addError('mobil', 'Mobilnummer må kun indeholde tal og være kort nok!');
            $this->dispatch('toast', [
                'detail' => [
                    'message' => 'Mobilnummer må kun indeholde tal og være kort nok!',
                    'type' => 'error'
                ]
            ]);
        }

        // Numeric value too large
        elseif ($e->getCode() === '22003') {
            $this->addError('mobil', 'Nummeret er for langt til dette felt!');
            $this->dispatch('toast', [
                'detail' => [
                    'message' => 'Mobilnummer eller tlf er for langt!',
                    'type' => 'error'
                ]
            ]);
        }

        // Other errors
        else {
            throw $e;
        }
        }
    }

    
    public function render()
    {
        return view('liveWire.konsulenter.create-konsulent');
    }
}