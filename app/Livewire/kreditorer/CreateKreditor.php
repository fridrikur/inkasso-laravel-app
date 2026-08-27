<?php

namespace App\Livewire\Kreditorer;

use App\Livewire\Forms\KreditorForm;
use App\Models\Kreditorer;
use App\Services\KreditorManagementService;
use Livewire\Component;

class CreateKreditor extends Component
{
    public KreditorForm $form;
    public array $usedLotusIds = [];

    public function mount()
    {
        $this->refreshLotusList();

        // 🟢 Sørg for at tildele værdien direkte til form-objektet, så feltet faktisk bliver udfyldt!
        if (!$this->form->lotusID) {
            $this->form->lotusID = $this->suggestedLotusId;
        }
    }

    public function refreshLotusList(): void
    {
        $this->usedLotusIds = Kreditorer::withTrashed()->pluck('lotusID')->filter()->toArray();
    }

    public function getLotusIdExistsProperty()
    {
        return in_array(
            $this->form->lotusID,
            $this->usedLotusIds
        );
    }

    public function getSuggestedLotusIdProperty()
    {
        $nextId = (int) (Kreditorer::withTrashed()->max('lotusID') ?? 0) + 1;
        
        // Spring over hvis ID'et allerede findes (hvis der er huller)
        while (in_array($nextId, $this->usedLotusIds)) {
            $nextId++;
        }

        return $nextId;
    }

    public function save(KreditorManagementService $management)
    {
        $this->validate();

        // 1. Tjek om Lotus ID'et allerede er i brug af en AKTIV kreditor
        $existingActive = Kreditorer::where('lotusID', $this->form->lotusID)->first();
        if ($existingActive) {
            $this->addError('form.lotusID', 'Dette Lotus ID er allerede i brug af en anden aktiv kreditor.');
            return;
        }

        // 2. Tjek om der findes en SLETTET kreditor med samme ID eller navn (Soft Delete)
        $trashedKreditor = Kreditorer::onlyTrashed()
            ->where(function ($q) {
                $q->where('navn', $this->form->navn)
                  ->orWhere('lotusID', $this->form->lotusID);
            })
            ->first();

        if ($trashedKreditor) {
            $trashedKreditor->restore();
            $management->update($trashedKreditor, [
                'navn'    => $this->form->navn,
                'lotusID' => $this->form->lotusID,
            ]);

            session()->flash('kreditor_lige_oprettet', true);
            return $this->redirect(route('kreditor.manage', [$trashedKreditor->id, 'oprettet' => 1]), navigate: false);
        }

        try {
            // 3. Opret ny kreditor via servicen
            $nyKreditor = $management->create([
                'navn'    => $this->form->navn,
                'lotusID' => $this->form->lotusID,
            ]);

            session()->flash('kreditor_lige_oprettet', true);
            return $this->redirect(route('kreditor.manage', [$nyKreditor->id, 'oprettet' => 1]), navigate: false);

        } catch (\Illuminate\Database\QueryException $e) {
            // MySQL fejl 1062 = Unique constraint violation
            if ($e->errorInfo[1] === 1062) {
                $this->addError('form.lotusID', 'Dette Lotus ID er allerede i brug i databasen. Vælg venligst et andet.');
                return;
            }

            throw $e;
        }
    }

    public function render()
    {
        return view('livewire.kreditorer.create-kreditor');
    }
}