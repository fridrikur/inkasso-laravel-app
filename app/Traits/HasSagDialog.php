<?php

namespace App\Traits;

use App\Models\Dialog;
use App\Models\DialogParticipant;
use Illuminate\Support\Collection;

trait HasSagDialog
{
    public string $tekst = '';

    // 🟢 Egenskaber til redigering af eksisterende beskeder
    public ?int $editingMessageId = null;
    public string $editingText = '';

    /**
     * Angiv dialogtypen i din Livewire-komponent ('klientinformation' | 'historik' | 'bogholderi')
     */
    abstract protected function getDialogType(): string;

    /**
     * Hent den underliggende Dialog-model for den aktuelle sag
     */
    public function getDialogProperty(): ?Dialog
    {
        return $this->sag->dialogs()
            ->where('type', $this->getDialogType())
            ->first();
    }

    /**
     * Hent beskeder og markér dem som læst.
     */
    public function getDialogMessages(): Collection
    {
        if (! auth()->check()) {
            return collect();
        }

        $dialog = $this->dialog;

        if (! $dialog) {
            return collect();
        }

        $messages = $dialog->messages()
            ->with(['sender.roles'])
            ->orderBy('dato', 'desc')
            ->get();

        // Markér andres ulæste beskeder som læst ved visning
        foreach ($messages as $message) {
            if ($message->read_at === null && $message->sender_id !== auth()->id()) {
                $message->update(['read_at' => now()]);
            }
        }

        return $messages;
    }

    /**
     * Genanvendelig gem-metode til oprettelse af besked & automatisk oprettelse af Dialog
     */
    public function sendMessage(?int $senderId = null, ?string $senderType = null): void
    {
        if (trim($this->tekst) === '') {
            return;
        }

        $dialog = $this->sag->dialogs()->firstOrCreate([
            'type' => $this->getDialogType(),
        ]);

        $userId = $senderId ?? auth()->id();

        if ($senderType) {
            DialogParticipant::firstOrCreate([
                'dialog_id' => $dialog->id,
                'user_type' => $senderType,
                'user_id'   => $userId,
            ]);
        }

        $dialog->messages()->create([
            'tekst'       => $this->tekst,
            'sender_id'   => $userId,
            'sender_type' => $senderType,
            'dato'        => now(),
        ]);

        $this->reset('tekst');

        $this->dispatch('dialogUpdated');
    }

    // =========================================================================
    // 🟢 REDIGERING AF BESKEDER
    // =========================================================================

    /**
     * Klargør redigering af en besked
     */
    public function editMessage(int $messageId): void
    {
        $dialog = $this->dialog;
        if (! $dialog) {
            return;
        }

        $message = $dialog->messages()->find($messageId);
        if (! $message) {
            return;
        }

        $this->editingMessageId = $message->id;
        $this->editingText      = $message->tekst;
    }

    /**
     * Gemmer den opdaterede beskedtekst
     */
    public function updateMessage(): void
    {
        if (trim($this->editingText) === '' || ! $this->editingMessageId) {
            return;
        }

        $dialog = $this->dialog;
        if ($dialog) {
            $message = $dialog->messages()->find($this->editingMessageId);

            if ($message) {
                $message->update([
                    'tekst' => $this->editingText,
                ]);

                $this->dispatch('toast', message: 'Notat opdateret!', type: 'success');
            }
        }

        $this->cancelEdit();
        $this->dispatch('dialogUpdated');
    }

    /**
     * Afbryd redigering
     */
    public function cancelEdit(): void
    {
        $this->editingMessageId = null;
        $this->editingText      = '';
    }
}