<?php

namespace App\Livewire\Sager;

use Livewire\Component;
use App\Models\Brev;
use App\Models\Sager;
use App\Services\BrevMergeService;

class MergeBrev extends Component
{
    public Sager $sag;

    public ?int $brevId = null;
    public string $emne = '';
    public string $preview = '';
    public string $tekst = '';
    public array $missingTokens = [];
    public array $availableTokens = [];
    public bool $dirty = false;
    public string $mode = 'preview'; // preview | edit
    public array $breveList = []; // for tabs
    
    public string $newBrevTitle = ''; // new Brev input

    public function mount(Sager $sag)
    {
        $this->sag = $sag;
        $this->availableTokens = $this->buildAvailableTokens();

        // Load all tabs
        $this->loadBreveList();

        // Load first Brev as active
        if (!empty($this->breveList)) {
            $this->loadBrev($this->breveList[0]['id']);
        }
    }

    /** Load tabs */
    private function loadBreveList(): void
    {
        $this->breveList = Brev::orderBy('brevpos')->get()->toArray();
        
    }

    /** Load first Brev if exists */
    private function loadFirstBrev(): void
    {
        if ($first = Brev::orderBy('brevpos')->first()) {
            $this->loadBrev($first['id']);
        }
    }

    public function loadBrev(int $id): void
    {
        $brev = Brev::findOrFail($id);

        $this->brevId = $brev->id;
        $this->emne = $brev->emne;
        $this->tekst = $brev->tekst;
        $this->dirty = false;

        $this->generatePreview();
    }

    public function updated($property)
    {
        if (in_array($property, ['emne', 'tekst'])) {
            $this->dirty = true;
        }
    }

    public function generatePreview(): void
    {
        if (!$this->brevId) return;

        $service = app(BrevMergeService::class);
        $result = $service->mergeWithMeta($this->tekst, $this->sag);

        $this->preview = $result['text'];
        $this->missingTokens = $result['missing'];
    }

    public function saveTemplate(): void
    {
        if (!$this->brevId) return;

        Brev::where('id', $this->brevId)->update([
            'emne' => $this->emne,
            'tekst' => $this->tekst,
        ]);

        $this->dirty = false;

        // Dispatch Livewire 3 toast
        $this->dispatch('toast', [
            'message' => 'Skabelonen er gemt',
            'type' => 'success',
        ]);
    }

    private function buildAvailableTokens(): array
    {
        $tokens = [];

        foreach ($this->sag->getFillable() as $field) {
            $tokens[] = $field;
        }

        if ($this->sag->debitor) {
            foreach ($this->sag->debitor->getFillable() as $field) {
                $tokens[] = 'debitor_' . $field;
            }
        }

        $tokens[] = 'today';

        return $tokens;
    }
/** Update Brev title inline */
    public function updateBrevTitle($brevId, $newTitle)
    {
        $brev = Brev::find($brevId);
        if (!$brev) return;

        $brev->update(['titel' => $newTitle]);
        $this->loadBreveList(); // refresh tabs
        $this->dispatch('toast', [
            'message' => 'Titel opdateret',
            'type' => 'success'
        ]);
    }

    /** Create a new Brev */
    public function createNewBrev()
    {
        if (!$this->newBrevTitle) {
            $this->dispatch('toast', [
                'message' => 'Titel må ikke være tom',
                'type' => 'error'
            ]);
            return;
        }

        $brev = Brev::create([
            'titel' => $this->newBrevTitle,
            'emne' => '',
            'tekst' => '',
            'brevpos' => Brev::max('brevpos') + 1,
        ]);

        $this->newBrevTitle = '';
        $this->loadBreveList();
        $this->loadBrev($brev->id);

        $this->dispatch('toast', [
            'message' => 'Nyt brev oprettet',
            'type' => 'success'
        ]);
    }
    
    public function render()
    {
        return view('livewire.sager.merge-brev', [
            'breve' => Brev::orderBy('brevpos')->get(),
            'breveList' => $this->breveList,
        ]);
    }
}