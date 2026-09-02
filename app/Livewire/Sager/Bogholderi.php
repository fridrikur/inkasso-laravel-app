<?php

namespace App\Livewire\Sager;

use Livewire\Component;
use App\Models\Sager;
use App\Models\Konsulenter;
use App\Models\DialogMessage;
use App\Traits\HasSagDialog;

class Bogholderi extends Component
{
    use HasSagDialog;

    public Sager $sag;
    public $konsulent_id;

    // 🟢 Tilføj til slette-modal
    public bool $showDeleteMessageModal = false;
    public ?int $messageToDeleteId = null;

    protected function getDialogType(): string
    {
        return 'bogholderi';
    }

    public function mount(Sager $sag): void
    {
        $this->sag = $sag;

        $konsulenter = $sag->sagerkonsulent ?? collect();

        $hoved = $konsulenter->first(function ($k) {
            return method_exists($k, 'isHovedKonsulent') ? $k->isHovedKonsulent() : false;
        });

        $firstAssigned = $konsulenter->first();

        $this->konsulent_id = $hoved?->id ?? $firstAssigned?->id;
    }

    public function save(): void
    {
        $this->validate([
            'tekst' => 'required|string|min:1',
        ]);

        $this->sendMessage(
            senderId: auth()->id()
        );
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
        return view('livewire.sager.bogholderi', [
            'dialogMessages' => $this->getDialogMessages(),
            'konsulenter'    => Konsulenter::orderBy('navn')->get(),
        ]);
    }
}