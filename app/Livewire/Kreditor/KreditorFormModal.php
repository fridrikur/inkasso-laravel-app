<?php

namespace App\Livewire\Kreditor;

use Livewire\Component;
use App\Models\Kreditorer;
use App\Traits\HasCrudModal;
use Livewire\Attributes\On;
use App\Services\KreditorManagementService;

class KreditorFormModal extends Component
{
    use HasCrudModal;

    public string $navn = '';
    public $lotusID = null;
    public array $usedLotusIds = [];

    #[On('open-edit-modal')]
    public function openEditModalFromParent($id): void
    {
        $this->openEditModal($id);
    }

    protected $listeners = [
        'open-kreditor-modal' => 'openCreateModal',
        'edit-kreditor-modal' => 'openEditModal',
    ];

    public function mount($id = null): void
    {
        $this->refreshLotusList();

        if ($id) {
            $this->openEditModal($id);
        } else {
            $this->setNextAvailableLotusId();
        }
    }

    // Denne kører automatisk, når HasCrudModal åbner oprettelsesmodalen
    public function resetForm(): void
    {
        $this->navn = '';
        $this->refreshLotusList();
        $this->setNextAvailableLotusId();
    }

    public function refreshLotusList(): void
    {
        $this->usedLotusIds = Kreditorer::withTrashed()->pluck('lotusID')->filter()->toArray();
    }

    public function setNextAvailableLotusId(): void
    {
        $nextId = (int) (Kreditorer::withTrashed()->max('lotusID') ?? 0) + 1;
        
        // Sikr at vi finder det første ledige ID hvis der er huller i rækken
        while (in_array($nextId, $this->usedLotusIds)) {
            $nextId++;
        }

        $this->lotusID = $nextId;
    }

    public function loadItemData($id): void
    {
        $kreditor = Kreditorer::withTrashed()->findOrFail($id);
        $this->navn = $kreditor->navn;
        $this->lotusID = $kreditor->lotusID;
    }

    public function getSuggestedLotusIdProperty()
    {
        $nextId = (int) (Kreditorer::withTrashed()->max('lotusID') ?? 0) + 1;
        while (in_array($nextId, $this->usedLotusIds)) {
            $nextId++;
        }
        return $nextId;
    }

    public function save()
    {
        $this->validate([
            'navn' => [
                'required', 
                'string', 
                'max:255', 
                'unique:kreditors,navn,' . $this->editingId . ',id,deleted_at,NULL'
            ],
            'lotusID' => [
                'required', 
                'integer', 
                'unique:kreditors,lotusID,' . $this->editingId . ',id,deleted_at,NULL'
            ],
        ]);

        $management = app(KreditorManagementService::class);

        if ($this->editingId) {
            $kreditor = Kreditorer::withTrashed()->findOrFail($this->editingId);
            $management->update($kreditor, [
                'navn' => $this->navn,
                'lotusID' => $this->lotusID,
            ]);
            $msg = 'Kreditor opdateret.';

            $this->closeFormModal();
            $this->dispatch('kreditor-saved');
            $this->dispatch('toast', ['message' => $msg, 'type' => 'success']);
        } else {
            $kreditor = $management->create([
                'navn' => $this->navn,
                'lotusID' => $this->lotusID,
            ]);

            $msg = 'Ny kreditor er oprettet og tilføjet til oversigten.';

            $this->closeFormModal();
            
            $this->dispatch('kreditor-saved', id: $kreditor->id);
            $this->dispatch('toast', ['message' => $msg, 'type' => 'success']);
        }
    }

    public function render()
    {
        return view('livewire.kreditor.kreditor-form-modal');
    }
}