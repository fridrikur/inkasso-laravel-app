<?php

namespace App\Livewire\Kreditor;

use Livewire\Component;
use App\Models\Kreditorer;
use App\Traits\HasCrudModal;
use Livewire\Attributes\On;

class KreditorFormModal extends Component
{
    use HasCrudModal;

    public string $navn = '';
    public $lotusID = null; // 🟢 Ændret fra public ?int $lotusID = null;
    public array $usedLotusIds = [];

    #[On('open-edit-modal')]
    public function openEditModalFromParent($id): void
    {
        // Kalder traitens indbyggede metode, som sætter $editingId = $id,
        // henter data via loadItemData() og åbner modalen ($showFormModal = true)
        $this->openEditModal($id);
    }

    protected $listeners = [
        'open-kreditor-modal' => 'openCreateModal',
        'edit-kreditor-modal' => 'openEditModal',
    ];

    public function mount($id = null): void
    {
        // Hent kun aktive LotusIDs til forslag
        $this->usedLotusIds = Kreditorer::pluck('lotusID')->filter()->toArray();

        if ($id) {
            $this->openEditModal($id);
        }
    }

    public function resetForm(): void
    {
        $this->navn = '';
        $this->lotusID = $this->suggestedLotusId;
    }

    public function loadItemData($id): void
    {
        // Brug withTrashed så vi også kan åbne/redigere en der er i papirkurven
        $kreditor = Kreditorer::withTrashed()->findOrFail($id);
        $this->navn = $kreditor->navn;
        $this->lotusID = $kreditor->lotusID;
    }

    public function getSuggestedLotusIdProperty()
    {
        // Tæller også soft-deleted med for at undgå LotusID-sammenstød
        return (int) (Kreditorer::withTrashed()->max('lotusID') ?? 0) + 1;
    }

    public function save()
    {
        // 1. Tjek om der findes en SOFT-DELETED kreditor med samme navn eller LotusID
        if (! $this->editingId) {
            $trashedKreditor = Kreditorer::onlyTrashed()
                ->where(function ($q) {
                    $q->where('navn', $this->navn)
                      ->orWhere('lotusID', $this->lotusID);
                })
                ->first();

            // 🟢 HVIS DEN FINDES I PAPIRKURVEN: GENSKAB OG OPDATER DEN!
            if ($trashedKreditor) {
                $trashedKreditor->restore(); // Genskaber (fjerner deleted_at timestamp)
                $trashedKreditor->update([
                    'navn' => $this->navn,
                    'lotusID' => $this->lotusID,
                ]);

                $this->closeFormModal();
                $this->dispatch('kreditor-saved');
                $this->dispatch('toast', [
                    'message' => 'Kreditoren var tidligere slettet og er nu genoprettet succesfuldt!',
                    'type'    => 'success'
                ]);
                return;
            }
        }

        // 2. Normal Validering (Ignorerer soft-deleted i unik-tjekket, da vi har håndteret dem ovenfor)
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
        ], [
            'navn.unique' => 'En aktiv kreditor med dette navn findes allerede.',
            'lotusID.unique' => 'Dette Lotus ID er allerede optaget af en aktiv kreditor.',
        ]);

        // 3. Normal Opret / Gem
        if ($this->editingId) {
            $kreditor = Kreditorer::withTrashed()->findOrFail($this->editingId);
            $kreditor->update([
                'navn' => $this->navn,
                'lotusID' => $this->lotusID,
            ]);
            $msg = 'Kreditor opdateret.';
        } else {
            $kreditor = Kreditorer::create([
                'navn' => $this->navn,
                'lotusID' => $this->lotusID,
            ]);
            $msg = 'Kreditor oprettet succesfuldt.';
        }

        $this->closeFormModal();
        $this->dispatch('kreditor-saved');
        $this->dispatch('toast', ['message' => $msg, 'type' => 'success']);
    }

    public function render()
    {
        return view('livewire.kreditor.kreditor-form-modal');
    }
}