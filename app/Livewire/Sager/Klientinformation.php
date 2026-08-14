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

    // Felter til meddebitor / ubetalte måneder
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
     * Send almindelig besked skrevet af den indloggede bruger
     */
    public function save(): void
    {
        $this->validate([
            'tekst' => 'required|string|min:1',
        ]);

        // Sætter automatiske afsenderoplysninger fra den indloggede bruger
        $this->sendMessage(senderId: auth()->id());

        $this->dispatch('dialogUpdated');
    }

    /**
     * Opretter en ny boble med ubetalte måneder fra meddebitor
     */
    public function addMeddebitorBubble(): void
    {
        $this->validate([
            'meddebitorNavn' => 'required|string',
            'ubetalteMaaneder' => 'required|string',
        ]);

        // Formaterer teksten til boblen
        $formattedText = "📌 OPLYSNING OM MEDDEBITOR & UBETALTE MÅNEDER:\n"
            . "• Meddebitor: {$this->meddebitorNavn}\n"
            . "• Ubetalte måneder: {$this->ubetalteMaaneder}";

        $dialog = $this->sag->dialogs()->firstOrCreate([
            'type' => 'klientinformation',
        ]);

        // Opretter boblen med den indloggede brugers ID
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
        ]);
    }
}