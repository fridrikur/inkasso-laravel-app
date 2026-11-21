<?php

namespace App\Livewire\Sager;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Sager;

class Dokumenter extends Component
{
    use WithFileUploads;

    public Sager $sag;
    public $newDokument = null;

    public function uploadDokument(): void
    {
        if (! $this->sag || ! $this->sag->exists) {
            return;
        }

        $this->validate([
            'newDokument' => 'required|file|max:20480', // 20 MB – justér efter behov
        ]);

        // Tilpas felt- og disk-navne, så de matcher din eksisterende uploadDokument()-logik i SagEditor
        $path = $this->newDokument->store("sager/{$this->sag->id}/dokumenter", 'local');

        $this->sag->dokumenter()->create([
            'file_name'     => $this->newDokument->getClientOriginalName(),
            'file_path'     => $path,
            'file_size'     => $this->newDokument->getSize(),
            'uploaded_date' => now(),
        ]);

        $this->reset('newDokument');

        $this->dispatch('dokumentChanged');
        $this->dispatch('toast', message: 'Dokument uploadet.', type: 'success');
    }

    public function deleteDokument(int $dokumentId): void
    {
        if (! $this->sag || ! $this->sag->exists) {
            return;
        }

        $dokument = $this->sag->dokumenter()->find($dokumentId);

        if ($dokument) {
            // Slet evt. fysisk fil på disk her, hvis din nuværende deleteDokument() gør det
            $dokument->delete();

            $this->dispatch('dokumentChanged');
            $this->dispatch('toast', message: 'Dokument slettet.', type: 'info');
        }
    }

    public function render()
    {
        $dokumenter = ($this->sag && $this->sag->exists)
            ? $this->sag->dokumenter()->latest()->get()
            : collect();

        return view('livewire.sager.dokumenter', [
            'dokumenter' => $dokumenter,
        ]);
    }
}