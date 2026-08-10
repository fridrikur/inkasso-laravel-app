<?php

namespace App\Livewire\Sager;

use Livewire\Component;
use App\Models\Sager;
use App\Models\Dialog;
use App\Traits\HasSagDialog;

class Historik extends Component
{
    use HasSagDialog;
    
    public Sager $sag;
    public string $messageText = '';
    public bool $isAutotekstSelected = false; // 🟢 Holder styr på om autotekst er valgt

    protected $listeners = [
        'klientinformationUpdated' => '$refresh',
        'dialogUpdated' => '$refresh',
    ];

    protected function getDialogType(): string
    {
        return 'historik';
    }

    /**
     * Markerer at en autotekst er blevet valgt via Alpine / Dropdown
     */
    public function setAutotekstSelected(bool $status = true): void
    {
        $this->isAutotekstSelected = $status;
    }

    public function saveNote(): void
    {
        $this->validate([
            'messageText' => 'required|string|min:1',
        ]);

        $user = auth()->user();

        // 1️⃣ Gem ALTID i Historik Dialog
        $historikDialog = Dialog::firstOrCreate([
            'sag_id' => $this->sag->id,
            'type'   => 'historik',
        ]);

        $historikDialog->messages()->create([
            'sender_id' => $user->id,
            'tekst'     => $this->messageText,
            'dato'      => now(),
        ]);

        // 2️⃣ Gem KUN i Klientinformation, hvis der er valgt en autotekst
        if ($this->isAutotekstSelected) {
            $klientDialog = Dialog::firstOrCreate([
                'sag_id' => $this->sag->id,
                'type'   => 'klientinformation',
            ]);

            $klientDialog->messages()->create([
                'sender_id' => $user->id,
                'tekst'     => $this->messageText,
                'dato'      => now(),
            ]);

            $this->dispatch('klientinformationUpdated');
            $this->dispatch('toast', message: 'Notat gemt i Historik og Klientinformation!', type: 'success');
        } else {
            $this->dispatch('toast', message: 'Notat gemt i Historik!', type: 'success');
        }

        // Nulstil felter og status
        $this->reset(['messageText', 'isAutotekstSelected']);
        $this->dispatch('dialogUpdated');
    }

    public function render()
    {
        $dialog = Dialog::where('sag_id', $this->sag->id)
            ->where('type', 'historik')
            ->with(['messages.sender'])
            ->first();

        return view('livewire.sager.historik', [
            'messages' => $dialog ? $dialog->messages()->orderBy('created_at', 'asc')->get() : collect(),
        ]);
    }
}