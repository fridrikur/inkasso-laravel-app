<?php

namespace App\Livewire\Sager;

use Livewire\Component;
use App\Models\Sager;
use App\Models\Dialog;
use App\Models\DialogMessage;
use App\Traits\HasSagDialog;

class Historik extends Component
{
    use HasSagDialog;
    
    public Sager $sag;
    public string $messageText = '';
    public bool $isAutotekstSelected = false;

    // 🟢 Tilføj disse til styring af slette-modalen i fanen
    public bool $showDeleteMessageModal = false;
    public ?int $messageToDeleteId = null;

    protected $listeners = [
        'klientinformationUpdated' => '$refresh',
        'dialogUpdated' => '$refresh',
    ];

    protected function getDialogType(): string
    {
        return 'historik';
    }

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

        $historikDialog = Dialog::firstOrCreate([
            'sag_id' => $this->sag->id,
            'type'   => 'historik',
        ]);

        $historikDialog->messages()->create([
            'sender_id' => $user->id,
            'tekst'     => $this->messageText,
            'dato'      => now(),
        ]);

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

        $this->reset(['messageText', 'isAutotekstSelected']);
        $this->dispatch('dialogUpdated');
    }

    // 🟢 Åbn slette-modal
    public function confirmDeleteMessage(int $id): void
    {
        $this->messageToDeleteId = $id;
        $this->showDeleteMessageModal = true;
    }

    // 🟢 Udfør soft delete
    public function executeDeleteMessage()
    {
        if (!$this->messageToDeleteId) return;

        $message = DialogMessage::withTrashed()->find($this->messageToDeleteId);

        if ($message) {
            $message->delete(); 

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

    // 🟢 Gendan besked ved "Fortryd"
    public function restoreMessage(int $id)
    {
        $message = DialogMessage::withTrashed()->find($id);

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
        $dialog = Dialog::where('sag_id', $this->sag->id)
            ->where('type', 'historik')
            ->with(['messages.sender'])
            ->first();

        return view('livewire.sager.historik', [
            'messages' => $dialog ? $dialog->messages()->orderBy('created_at', 'asc')->get() : collect(),
        ]);
    }
}