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
        
        // Sørg for kun at tage de første 10 tegn (YYYY-MM-DD), hvis det er en datetime-streng
        $this->dato = $item->dato ? substr($item->dato, 0, 10) : now()->format('Y-m-d');
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

    public function confirmDelete($id = null): void
    {
        // 1. Hvis der sendes et ID med fra tabellen, så åbn slette-modalen
        if ($id) {
            $this->deletingId = $id;
            $this->showDeleteModal = true;
            return;
        }

        // 2. Hvis der ikke er et deletingId, så luk modalen og afbryd
        if (!$this->deletingId) {
            $this->cancelDelete();
            return;
        }

        // 3. Udfør selve sletningen i databasen
        Autotekster::find($this->deletingId)?->delete();

        // 4. Luk modalen og nulstil ID'et
        $this->cancelDelete();

        // 5. Giv besked via toast
        $this->dispatch('toast', message: 'Autotekst slettet!', type: 'success');
    }

    public function render()
    {
        return view('livewire.autotekster.index', [
            'autotekster' => Autotekster::orderBy('id', 'desc')->get(),
        ]);
    }
}