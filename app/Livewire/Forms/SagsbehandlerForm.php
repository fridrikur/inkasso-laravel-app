<?php

namespace App\Livewire\forms;

use Livewire\form;
use Livewire\Attributes\Validate;
use App\Models\Sagsbehandler;
use Illuminate\Database\QueryException;
use Illuminate\Validation\Rule;

class SagsbehandlerForm extends Form
{
    public ?Sagsbehandler $sagsbehandler = null;
    public bool   $is_hoved = false;

    public $navn = '';

    public $email = '';

    public $tlf = '';

    public $mobil = '';

    public function mount(?Sagsbehandler $sagsbehandler = null)
    {
        $this->sagsbehandler = $sagsbehandler;

        if ($sagsbehandler) {
            $this->navn  = $sagsbehandler->navn;
            $this->email = $sagsbehandler->email;
            $this->tlf   = $sagsbehandler->tlf;
            $this->mobil = $sagsbehandler->mobil;

            // Ignore unique rules for the current record
            $this->email = $sagsbehandler->email;
        }
    }

    public function rules()
    {
        return [
            'navn' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('sagsbehandlers', 'email')->ignore($this->sagsbehandler?->id),
            ],

            'tlf' => [
                'nullable',
                'digits:8',
                Rule::unique('sagsbehandlers', 'tlf')->ignore($this->sagsbehandler?->id),
            ],

            'mobil' => [
                'nullable',
                'digits:8',
                Rule::unique('sagsbehandlers', 'mobil')->ignore($this->sagsbehandler?->id),
            ],

            'is_hoved' => ['boolean'],
        ];
    }

    public function set(Sagsbehandler $sagsbehandler)
    {
        $this->sagsbehandler = $sagsbehandler;

        $this->navn = $sagsbehandler->navn;
        $this->email = $sagsbehandler->email;
        $this->tlf = $sagsbehandler->tlf;
        $this->mobil = $sagsbehandler->mobil;
        $this->is_hoved = $sagsbehandler->is_hoved;
    }

    public function resetForm()
    {
        $this->sagsbehandler = null;

        $this->navn = '';
        $this->email = '';
        $this->tlf = '';
        $this->mobil = '';
        $this->is_hoved = false;
    }

    public function save(): Sagsbehandler
    {
        $this->validate();

        try {
            if ($this->sagsbehandler) {
                $this->sagsbehandler->update([
                    'navn'  => $this->navn,
                    'email' => $this->email,
                    'tlf'   => $this->tlf,
                    'mobil' => $this->mobil,
                ]);

                return $this->sagsbehandler;
            }

            return Sagsbehandler::create([
                'navn'  => $this->navn,
                'email' => $this->email,
                'tlf'   => $this->tlf,
                'mobil' => $this->mobil,
            ]);

        } catch (QueryException $e) {

            if ($e->getCode() === '23000') {
                $this->addError('mobil', 'Dette mobilnummer er allerede i brug.');

                // Required to satisfy return type
                return $this->sagsbehandler ?? new Sagsbehandler();
            }

            throw $e;
        }
    }
}