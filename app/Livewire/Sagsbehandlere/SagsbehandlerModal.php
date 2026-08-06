<?php

namespace App\Livewire\Sagsbehandlere;

use Livewire\Component;
use App\Models\Sagsbehandler;
use App\Models\Kreditorer;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;

class SagsbehandlerModal extends Component
{
    public bool $show = false;

    public ?Sagsbehandler $sags = null;
    public ?int $kreditorId = null;

    // 🔹 Form fields
    public string $modalNavn = '';
    public ?string $modalEmail = null;
    public ?string $modalTlf = null;
    public ?string $modalMobil = null;
    public bool $modalIsHoved = false;

    // 🔹 Open modal from ShowKreditor
    #[On('open-sagsbehandler-modal')]
    public function open(array $payload): void
    {
        $this->kreditorId = $payload['kreditorId'] ?? null;

        if (isset($payload['id'])) {
            $this->sags = Sagsbehandler::find($payload['id']);

            if ($this->sags) {
                $this->modalNavn  = $this->sags->navn;
                $this->modalEmail = $this->sags->email;
                $this->modalTlf   = $this->sags->tlf;
                $this->modalMobil = $this->sags->mobil;
                $this->modalIsHoved = $this->sags->hovedsagsbehandler()
                    ->where('kreditor_id', $this->kreditorId)
                    ->exists();
            }
        } else {
            $this->resetFields();
        }

        $this->show = true;
    }

    public function close(): void
    {
        $this->resetFields();
        $this->show = false;
    }

    private function resetFields(): void
    {
        $this->sags = null;
        $this->modalNavn = '';
        $this->modalEmail = null;
        $this->modalTlf = null;
        $this->modalMobil = null;
        $this->modalIsHoved = false;
        $this->resetValidation();
    }

    public function rules(): array
    {
        return [
            'modalNavn' => ['required', 'string', 'max:255'],
            'modalEmail' => [
                'nullable', 'email', 'max:255',
                Rule::unique('sagsbehandlers', 'email')->ignore($this->sags?->id),
            ],
            'modalTlf' => [
                'nullable', 'digits:8',
                Rule::unique('sagsbehandlers', 'tlf')->ignore($this->sags?->id),
            ],
            'modalMobil' => [
                'nullable', 'digits:8',
                Rule::unique('sagsbehandlers', 'mobil')->ignore($this->sags?->id),
            ],
            'modalIsHoved' => ['boolean'],
        ];
    }

    public function save(): void
    {
        $this->validate();

        if ($this->sags) {
            $this->sags->update([
                'navn' => $this->modalNavn,
                'email' => $this->modalEmail,
                'tlf' => $this->modalTlf,
                'mobil' => $this->modalMobil,
            ]);
            $sagsId = $this->sags->id;
        } else {
            $sags = Sagsbehandler::create([
                'navn' => $this->modalNavn,
                'email' => $this->modalEmail,
                'tlf' => $this->modalTlf,
                'mobil' => $this->modalMobil,
            ]);
            $sags->kreditor()->attach($this->kreditorId);
            $sagsId = $sags->id;
        }

        if ($this->modalIsHoved && $this->kreditorId) {
            Kreditorer::find($this->kreditorId)
                ->hovedsagsbehandler()
                ->sync([$sagsId]);
        }

        $this->close();
        $this->emitTo('kreditorer.show-kreditor', '$refresh');
    }

    public function render()
    {
        return view('liveWire.sagsbehandlere.sagsbehandler-modal');
    }
}
