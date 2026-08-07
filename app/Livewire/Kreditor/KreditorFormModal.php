<?php

namespace App\Livewire\Kreditor;

use Livewire\Component;
use App\Models\Kreditorer;
use App\Traits\HasCrudModal; // 🟢 BRUGER DET GENERISKE UI TRAIT

class KreditorFormModal extends Component
{
    use HasCrudModal;

    public string $navn = '';
    public ?int $lotusID = null;
    public array $usedLotusIds = [];

    protected $listeners = [
        'open-kreditor-modal' => 'openCreateModal',
        'edit-kreditor-modal' => 'openEditModal',
    ];

    public function mount()
    {
        $this->usedLotusIds = Kreditorer::pluck('lotusID')->filter()->toArray();
    }

    /**
     * Krav fra HasCrudModal: Nulstil felter ved oprettelse/lukning
     */
    public function resetForm(): void
    {
        $this->navn = '';
        $this->lotusID = $this->suggestedLotusId;
    }

    /**
     * Krav fra HasCrudModal: Indlæs data ved redigering
     */
    public function loadItemData($id): void
    {
        $kreditor = Kreditorer::findOrFail($id);
        $this->navn = $kreditor->navn;
        $this->lotusID = $kreditor->lotusID;
    }

    public function getSuggestedLotusIdProperty()
    {
        return (Kreditorer::max('lotusID') ?? 0) + 1;
    }

    public function save()
    {
        $this->validate([
            'navn' => ['required', 'string', 'max:255'],
            'lotusID' => ['required', 'integer', 'unique:kreditors,lotusID,' . $this->editingId],
        ]);

        if ($this->editingId) {
            $kreditor = Kreditorer::findOrFail($this->editingId);
            $kreditor->update([
                'navn' => $this->navn,
                'lotusID' => $this->lotusID,
            ]);
        } else {
            $kreditor = Kreditorer::create([
                'navn' => $this->navn,
                'lotusID' => $this->lotusID,
            ]);
        }

        $this->closeFormModal();
        $this->dispatch('kreditor-saved', payload: ['kreditorId' => $kreditor->id, 'navn' => $kreditor->navn]);
        $this->dispatch('toast', message: 'Kreditor gemt succesfuldt.', type: 'success');
    }

    public function render()
    {
        return view('livewire.kreditor.kreditor-form-modal');
    }
}