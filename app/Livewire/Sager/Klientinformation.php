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

    public bool $showDeleteMessageModal = false;
    public ?int $messageToDeleteId = null;

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

    public function deleteMessage($messageId)
    {
        // Antager at dine beskeder er baseret på f.eks. \App\Models\Message eller \App\Models\DialogMessage
        $message = \App\Models\DialogMessage::find($messageId);

        if ($message) {
            $message->delete(); // Sletter beskeden

            $this->dispatch('toast', [
                'message' => 'Beskeden blev slettet.',
                'type' => 'success'
            ]);
        }
    }

    public function confirmDeleteMessage(int $id): void
    {
        $this->messageToDeleteId = $id;
        $this->showDeleteMessageModal = true;
    }

    public function executeDeleteMessage()
    {
        if (!$this->messageToDeleteId) return;

        $message = \App\Models\DialogMessage::withTrashed()->find($this->messageToDeleteId);

        if ($message) {
            $message->delete(); // Soft delete

            $this->showDeleteMessageModal = false;
            $msgId = $this->messageToDeleteId;
            $this->messageToDeleteId = null;

            $this->dispatch('toast', [
                'message' => 'Beskeden blev flyttet til papirkurven.',
                'type' => 'success',
                'action' => [
                    'label' => 'Fortryd',
                    'method' => "restoreMessage({$msgId})"
                ]
            ]);
            
            $this->dispatch('dialogUpdated');
        }
    }

    public function restoreMessage(int $id)
    {
        $message = \App\Models\DialogMessage::withTrashed()->find($id);

        if ($message) {
            $message->restore();

            $this->dispatch('toast', [
                'message' => 'Sletning fortrydt – beskeden er gendannet.',
                'type' => 'info'
            ]);

            $this->dispatch('dialogUpdated');
        }
    }
    
    /**
     * Henter slettede beskeder (soft deleted) der ligger i papirkurven for denne dialog-type
     */
    public function getTrashMessagesProperty()
    {
        $dialog = \App\Models\Dialog::where('sag_id', $this->sag->id)
            ->where('type', $this->getDialogType())
            ->first();

        if (!$dialog) return collect();

        return $dialog->messages()->onlyTrashed()->orderBy('deleted_at', 'desc')->get();
    }
    
    public function render()
    {
        return view('livewire.sager.klientinformation', [
            'dialogMessages' => $this->getDialogMessages(),
            'sagName'        => $this->getSagNameProperty(),
        ]);
    }
}