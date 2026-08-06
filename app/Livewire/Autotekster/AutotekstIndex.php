<?php

namespace App\Livewire\Autotekster;

use Livewire\Component;
use App\Models\Autotekster;
use App\Traits\HasCrudModal;

class AutotekstIndex extends Component
{
    use HasCrudModal;

    public string $tekst = '';
    public string $dato = '';

    protected function rules()
    {
        return [
            'tekst' => 'required|string',
            'dato' => 'required|date',
        ];
    }

    public function resetForm(): void
    {
        $this->tekst = '';
        $this->dato = now()->format('Y-m-d');
    }

    public function loadItemData($id): void
    {
        $item = Autotekster::findOrFail($id);
        $this->tekst = $item->tekst;
        $this->dato = $item->dato;
    }

    public function save()
    {
        $this->validate();

        if ($this->editingId) {
            Autotekster::findOrFail($this->editingId)->update([
                'tekst' => $this->tekst,
                'dato' => $this->dato,
            ]);
            $msg = 'Autotekst opdateret!';
        } else {
            Autotekster::create([
                'tekst' => $this->tekst,
                'dato' => $this->dato,
            ]);
            $msg = 'Autotekst oprettet!';
        }

        $this->closeFormModal();
        $this->dispatch('toast', message: $msg, type: 'success');
    }

    public function confirmDelete()
    {
        if ($this->deletingId) {
            Autotekster::find($this->deletingId)?->delete();
            $this->cancelDelete();
            $this->dispatch('toast', message: 'Autotekst slettet!', type: 'success');
        }
    }

    public function render()
    {
        return view('livewire.autotekster.index', [
            'autotekster' => Autotekster::orderBy('id', 'desc')->get(),
        ]);
    }
}