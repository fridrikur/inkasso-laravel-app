<?php

namespace App\Livewire\Status;

use Livewire\Component;
use App\Models\Status;

class StatusIndex extends Component
{
    public $statusId = null;
    public $tekst = '';
    public $forkortelse = '';
    
    // Styrer om modalen er synlig
    public $showModal = false;

    protected function rules()
    {
        return [
            'tekst' => 'required|string|max:255',
            'forkortelse' => 'required|string|max:20',
        ];
    }

    // Nulstil og åbn modal til oprettelse
    public function openCreateModal()
    {
        $this->resetValidation();
        $this->reset(['statusId', 'tekst', 'forkortelse']);
        $this->showModal = true;
    }

    // Hent data og åbn modal til redigering
    public function openEditModal($id)
    {
        $this->resetValidation();
        $status = Status::findOrFail($id);
        
        $this->statusId = $status->id;
        $this->tekst = $status->tekst;
        $this->forkortelse = $status->forkortelse;

        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['statusId', 'tekst', 'forkortelse']);
    }

    // Gem enten ny eller opdater eksisterende
    public function saveStatus()
    {
        $this->validate();

        if ($this->statusId) {
            $status = Status::findOrFail($this->statusId);
            $status->update([
                'tekst' => $this->tekst,
                'forkortelse' => strtoupper($this->forkortelse),
            ]);
        } else {
            Status::create([
                'tekst' => $this->tekst,
                'forkortelse' => strtoupper($this->forkortelse),
            ]);
        }

        $this->closeModal();
    }

    public function deleteStatus($id)
    {
        Status::find($id)?->delete();
    }

    public function render()
    {
        return view('livewire.status.index', [
            'statuses' => Status::all(),
        ]);
    }
}