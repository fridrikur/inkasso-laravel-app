<?php

namespace App\Livewire\Status;

use Livewire\Component;
use App\Models\Status;

class Index extends Component
{
    public bool $showModal = false;
    public ?int $statusId = null;
    public string $tekst = '';
    public string $forkortelse = '';

    public function render()
    {
        return view('livewire.status.index', [
            'statuses' => Status::all(),
        ]);
    }

    public function openCreateModal()
    {
        $this->reset(['statusId', 'tekst', 'forkortelse']);
        $this->showModal = true;
    }

    public function openEditModal($id)
    {
        $status = Status::findOrFail($id);
        $this->statusId = $status->id;
        $this->tekst = $status->tekst;
        $this->forkortelse = $status->forkortelse;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function saveStatus()
    {
        $this->validate([
            'tekst' => 'required|string|max:255',
            'forkortelse' => 'required|string|max:50',
        ]);

        Status::updateOrCreate(
            ['id' => $this->statusId],
            [
                'tekst' => $this->tekst,
                'forkortelse' => strtoupper($this->forkortelse),
            ]
        );

        $this->closeModal();
    }

    public function deleteStatus($id)
    {
        Status::destroy($id);
    }
}