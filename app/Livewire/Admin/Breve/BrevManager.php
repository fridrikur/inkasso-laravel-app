<?php
namespace App\Livewire\Admin\Breve;

use Livewire\Component;
use App\Models\Brev;
use App\Models\Sager;
use App\Services\BrevMergeService;

class BrevManager extends Component
{
    public ?int $brevId = null;

    public string $titel = '';
    public string $emne = '';
    public string $tekst = '';

    public array $breveList = [];

    public string $newBrevTitle = '';

    public string $previewHtml = '';
    public ?Sager $previewSag = null;

    public bool $previewExpanded = false;

    public $showDeleteBrevModal = false;
    public $brevToDeleteId = null;
    public $brevToDeleteTitle = '';

    public ?string $highlightedToken = null;

    // -----------------------------------------
    // INIT
    // -----------------------------------------
    public function mount(): void
    {
        $this->loadBreveList();

        if (!empty($this->breveList)) {
            $this->loadBrev($this->breveList[0]['id']);
        }
    }

    // -----------------------------------------
    // LIST
    // -----------------------------------------
    public function loadBreveList(): void
    {
        $this->breveList = Brev::orderBy('brevpos')->get()->toArray();
    }

    // -----------------------------------------
    // LOAD BREV
    // -----------------------------------------
    public function loadBrev(int $id): void
    {
        $brev = Brev::findOrFail($id);

        $this->brevId = $brev->id;
        $this->titel  = $brev->titel;
        $this->emne   = $brev->emne ?? '';
        $this->tekst  = $brev->tekst ?? '';

        $this->generatePreview();
    }

    // -----------------------------------------
    // CREATE
    // -----------------------------------------
    public function createNewBrev(): void
    {
        if (!$this->newBrevTitle) return;

        $brev = Brev::create([
            'titel'   => $this->newBrevTitle,
            'emne'    => '',
            'tekst'   => '',
            'brevpos' => (Brev::max('brevpos') ?? 0) + 1,
        ]);

        $this->newBrevTitle = '';

        $this->loadBreveList();
        $this->loadBrev($brev->id);

        $this->dispatch('toast', [
            'message' => 'Nyt brev oprettet',
            'type' => 'success'
        ]);
    }

    // -----------------------------------------
    // SAVE
    // -----------------------------------------
    public function saveTemplate(): void
    {
        if (!$this->brevId) return;

        Brev::where('id', $this->brevId)->update([
            'titel' => $this->titel,
            'emne'  => $this->emne,
            'tekst' => $this->tekst,
        ]);

        $this->loadBreveList();

        $this->dispatch('toast', [
            'message' => 'Brev gemt',
            'type' => 'success'
        ]);
    }

    // -----------------------------------------
    // DELETE
    // -----------------------------------------
    public function deleteBrev(int $id): void
    {
        Brev::where('id', $id)->delete();

        $this->loadBreveList();

        if ($first = Brev::orderBy('brevpos')->first()) {
            $this->loadBrev($first->id);
        } else {
            $this->brevId = null;
            $this->titel = '';
            $this->emne = '';
            $this->tekst = '';
            $this->previewHtml = '';
        }

        $this->dispatch('toast', [
            'message' => 'Brev slettet',
            'type' => 'success'
        ]);
    }

    // -----------------------------------------
    // PREVIEW
    // -----------------------------------------
    public function generatePreview(): void
    {
        if (!$this->brevId) return;

        if (!$this->previewSag) {
            $this->previewSag = Sager::inRandomOrder()->first();
        }

        if (!$this->previewSag) {
            $this->previewHtml = '<em>Ingen sager tilgængelige</em>';
            return;
        }

        $service = app(BrevMergeService::class);

        $result = $service->mergeWithMeta(
            $this->tekst,
            $this->previewSag
        );

        $this->previewHtml =
            $result['text']
            ?? $result['html']
            ?? '';
    }

    // -----------------------------------------
    // RANDOM SAG
    // -----------------------------------------
    public function loadRandomSag(): void
    {
        $this->previewSag = Sager::inRandomOrder()->first();

        if (!$this->previewSag) return;

        $this->generatePreview();

        $this->dispatch('toast', [
            'message' => 'Random sag loaded',
            'type' => 'success'
        ]);
    }

    // -----------------------------------------
    // LIVE UPDATE
    // -----------------------------------------
    public function updatedTekst(): void
    {
        $this->generatePreview();
    }

    public function updatedTitel(): void
    {
        $this->generatePreview();
    }

    public function updatedEmne(): void
    {
        $this->generatePreview();
    }

    // -----------------------------------------
    // MODAL TOGGLE
    // -----------------------------------------
    public function togglePreview(): void
    {
        $this->previewExpanded = !$this->previewExpanded;
    }

    // -----------------------------------------
    // VIEW
    // -----------------------------------------
    
    public function updateBrevTitle($brevId, $newTitle)
    {
        if (empty(trim($newTitle))) {
            return;
        }

        $brev = \App\Models\Brev::find($brevId);
        if ($brev) {
            $brev->titel = $newTitle;
            $brev->save();
            
            // Genindlæs listen af breve så de opdateres i UI'et
            $this->loadBreveList(); // Eller det kald I bruger til at hente breve
        }
    }

    // Åbn modal og gem ID'et på det brev der ønskes slettet
    public function confirmDeleteBrev($id)
    {
        $brev = \App\Models\Brev::find($id);
        if ($brev) {
            $this->brevToDeleteId = $brev->id;
            $this->brevToDeleteTitle = $brev->titel;
            $this->showDeleteBrevModal = true;
        }
    }

    // Annuller
    public function cancelDeleteBrev()
    {
        $this->showDeleteBrevModal = false;
        $this->brevToDeleteId = null;
        $this->brevToDeleteTitle = '';
    }

    // Udfør selve sletningen
    public function executeDeleteBrev()
    {
        if ($this->brevToDeleteId) {
            \App\Models\Brev::where('id', $this->brevToDeleteId)->delete();
            
            // Nulstil og genindlæs listen
            $this->showDeleteBrevModal = false;
            $this->brevToDeleteId = null;
            $this->brevToDeleteTitle = '';
            
            // Genindlæs dine breve (tilpas evt. til dit eget metodekald for at hente listen)
            $this->loadBreveList(); 
        }
    }

    // -----------------------------------------
    // VALIDERING AF FLETTEFELTER (TOKENS)
    // -----------------------------------------
    public function getInvalidTokensProperty(): array
    {
        // Hent alle tilladte gyldige felter præcis som i din blade-fil
        $validTokens = array_merge(
            (new \App\Models\Sager())->getFillable(),
            ['today', 'aktiv', 'firmanavn', 'debitor_navn', 'ktr', 'debitor_email']
        );

        // Find alle felter i tekst med curly brackets {feltnavn}
        preg_match_all('/\{([^}]+)\}/', $this->tekst, $matches);
        
        $foundTokens = $matches[1] ?? [];
        $invalidTokens = [];

        foreach ($foundTokens as $token) {
            if (!in_array($token, $validTokens)) {
                $invalidTokens[] = $token;
            }
        }

        return array_unique($invalidTokens);
    }

    // Opdater metoden så den vælger det tætteste match og sætter det som fremhævet
    public function getClosestTokenSuggestion(string $invalidToken): ?string
    {
        $validTokens = array_merge(
            (new \App\Models\Sager())->getFillable(),
            ['today', 'aktiv', 'firmanavn', 'debitor_navn', 'ktr', 'debitor_email']
        );

        $closest = null;
        $highestPercent = 0;

        foreach ($validTokens as $token) {
            similar_text($invalidToken, $token, $percent);
            if ($percent > $highestPercent) {
                $highestPercent = $percent;
                $closest = $token;
            }
        }

        $suggestion = $highestPercent > 40 ? $closest : null;
        
        // Sæt automatisk det fremhævede felt til det foreslåede match
        if ($suggestion) {
            $this->highlightedToken = $suggestion;
        }

        return $suggestion;
    }

    // Metode til at rydde fremhævningen igen
    public function clearHighlight(): void
    {
        $this->highlightedToken = null;
    }

    // -----------------------------------------
    // ERSTAT UGYLDIGT FELT MED FORSLAG
    // -----------------------------------------
    public function replaceToken(string $invalidField, string $validField): void
    {
        $this->tekst = str_replace('{' . $invalidField . '}', '{' . $validField . '}', $this->tekst);
        $this->highlightedToken = null;
        $this->generatePreview();
    }
    
    public function render()
    {
        return view('livewire.admin.breve.brev-manager');
    }
}