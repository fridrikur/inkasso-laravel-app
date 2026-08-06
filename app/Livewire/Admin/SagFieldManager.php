<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\SagFieldSetting;

class SagFieldManager extends Component
{
    public array $allowedFields = [];

    public array $availableFields = [

        // Sager model
        'sagsnr' => 'Sagsnummer',
        'hovedstol' => 'Hovedstol',
        'renter' => 'Renter',
        'gebyr' => 'Gebyr',
        'indbetalt' => 'Indbetalt',

        // Debitor model
        'navn' => 'Debitor navn',
        'adresse' => 'Adresse',
        'postnr' => 'Postnr',
        'by' => 'By',
    ];

    public function mount()
    {
        $settings = SagFieldSetting::first();

        if ($settings) {
            $this->allowedFields = $settings->allowed_fields ?? [];
        }
    }

    public function save()
    {
        SagFieldSetting::updateOrCreate(
            ['id' => 1],
            ['allowed_fields' => $this->allowedFields]
        );

        $this->dispatch('toast',
            message: 'Feltopsætning gemt',
            type: 'success'
        );
    }

    public function render()
    {
        return view('livewire.admin.sag-field-manager');
    }
}