<?php

namespace App\Livewire\Sager;

use Livewire\Component;
use App\Models\Sager;
use App\Models\Dialog;
use App\Traits\HasSagDialog;

class Klientinformation extends Component
{
    use HasSagDialog;

    public Sager $sag;

    public string $meddebitorNavn = '';
    public string $ubetalteMaaneder = '';
    public bool $showMeddebitorModal = false;

    protected $listeners = [
        'klientinformationUpdated' => '$refresh',
        'dialogUpdated' => '$refresh',
    ];

    protected function getDialogType(): string
    {
        return 'klientinformation';
    }

    public function mount(Sager $sag): void
    {
        $this->sag = $sag;
    }

    /**
     * Henter navnet til visning sammen med sagsnummeret
     */
    public function getSagNameProperty()
    {
        // Prøver at finde navnet via debitor-relationen (eller tilpas hvis det findes direkte på $this->sag->navn)
        if ($this->sag->relationLoaded('sagerdebitor') && $this->sag->sagerdebitor->isNotEmpty()) {
            return $this->sag->sagerdebitor->first()->navn ?? null;
        }

        // Fallback hvis modellen har et direkte navn-felt
        return $this->sag->navn ?? optional($this->sag->sagerdebitor()->first())->navn ?? 'Ukendt Klient';
    }

    public function save(): void
    {
        $this->validate([
            'tekst' => 'required|string|min:1',
        ]);

        $this->sendMessage(senderId: auth()->id());
        $this->dispatch('dialogUpdated');
    }

    public function addMeddebitorBubble(): void
    {
        $this->validate([
            'meddebitorNavn' => 'required|string',
            'ubetalteMaaneder' => 'required|string',
        ]);

        $formattedText = "📌 OPLYSNING OM MEDDEBITOR & UBETALTE MÅNEDER:\n"
            . "• Meddebitor: {$this->meddebitorNavn}\n"
            . "• Ubetalte måneder: {$this->ubetalteMaaneder}";

        $dialog = $this->sag->dialogs()->firstOrCreate([
            'type' => 'klientinformation',
        ]);

        $dialog->messages()->create([
            'sender_id' => auth()->id(),
            'tekst'     => $formattedText,
            'dato'      => now(),
        ]);

        $this->reset(['meddebitorNavn', 'ubetalteMaaneder', 'showMeddebitorModal']);
        $this->dispatch('dialogUpdated');
        $this->dispatch('toast', message: 'Meddebitor boble oprettet i Klientinformation!', type: 'success');
    }

    public function render()
    {
        return view('livewire.sager.klientinformation', [
            'dialogMessages' => $this->getDialogMessages(),
            'sagName'        => $this->getSagNameProperty(),
        ]);
    }
}