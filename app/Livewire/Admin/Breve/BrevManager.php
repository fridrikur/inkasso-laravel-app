<?php

namespace App\Livewire\Admin\Breve;

use Livewire\Component;
use App\Models\Brev;
use App\Models\BrevFeltIndstilling;
use App\Services\ToastService;

class BrevManager extends Component
{
    public ?int $brevId = null;
    public string $titel = '';
    public string $emne = '';
    public string $tekst = '';
    
    // Layout marginaler
    public int $margin_top = 0;
    public int $margin_left = 0;
    public int $margin_right = 0;

    public array $breveList = [];
    public string $newBrevTitle = '';
    public $showDeleteBrevModal = false;
    public $brevToDeleteId = null;
    public $brevToDeleteTitle = '';
    public ?string $highlightedToken = null;
    public bool $beskyttet = false;

    public float $linjeafstand = 1.5; // Standard linjeafstand

    public bool $showCreateModal = false;
    public bool $showRenameModal = false;
    public string $editTitle = '';

    public array $filter = [
        'adresse'   => true,
        'dato'      => true,
        'navn'      => true,
        'sagsnr'    => true,
        'emne'      => true,
        'skjulalle' => false,
        'visalle'   => false,
    ];

    public int $right_margin_top = 0;

    public function mount()
    {
        $this->loadBreveList();

        if (!empty($this->breveList)) {
            $this->loadBrev($this->breveList[0]['id']);
        }
    }

    public function loadBreveList(): void
    {
        $this->breveList = Brev::orderBy('brevpos')->get()->toArray();
    }

    public function loadBrev($id): void
    {
        $brev = Brev::find($id);

        if ($brev) {
            $this->brevId       = $brev->id;
            $this->titel        = $brev->titel ?? '';
            $this->editTitle    = $brev->titel ?? '';
            $this->emne         = $brev->emne ?? '';
            $this->tekst        = $brev->tekst ?? '';  
            $this->margin_top   = $brev->margin_top ?? 0;
            $this->margin_left  = $brev->margin_left ?? 0;
            $this->margin_right = $brev->margin_right ?? 0;
            $this->beskyttet    = (bool) ($brev->beskyttet ?? 0);
            $this->linjeafstand = $brev->linjeafstand ?? 1.5;
            $this->right_margin_top = $brev->right_margin_top ?? 0;

            // Hent eller opret standard filter for dette brev
            $brevFilter = \App\Models\BrevFilter::where('brevID', $this->brevId)->first();
            if ($brevFilter) {
                $this->filter = [
                    'adresse'   => (bool) $brevFilter->adresse,
                    'dato'      => (bool) $brevFilter->dato,
                    'navn'      => (bool) $brevFilter->navn,
                    'sagsnr'    => (bool) $brevFilter->sagsnr,
                    'emne'      => (bool) $brevFilter->emne,
                    'skjulalle' => (bool) $brevFilter->skjulalle,
                    'visalle'   => (bool) $brevFilter->visalle,
                ];
            } else {
                // Standardværdier hvis der ikke findes et filter endnu
                $this->filter = [
                    'adresse' => true, 'dato' => true, 'navn' => true, 
                    'sagsnr' => true, 'emne' => true, 'skjulalle' => false, 'visalle' => false
                ];
            }
        }
    }

    public function saveTemplate(ToastService $toast): void
    {
        if (!$this->brevId) return;

        $brev = Brev::find($this->brevId);
        
        if ($brev && $brev->beskyttet && $this->beskyttet) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'Fejl: Denne brevskabelon er beskyttet og kan ikke ændres!']);
            return;
        }

        // Gem selve brevet
        Brev::where('id', $this->brevId)->update([
            'titel'        => $this->titel,
            'emne'         => $this->emne,
            'tekst'        => $this->tekst,
            'margin_top'   => $this->margin_top,
            'margin_left'  => $this->margin_left,
            'margin_right' => $this->margin_right,
            'right_margin_top' => $this->right_margin_top, 
            'beskyttet'    => $this->beskyttet ? 1 : 0, 
            'linjeafstand' => $this->linjeafstand,
        ]);

        // Gem / Opdater BrevFilter i databasen
        \App\Models\BrevFilter::updateOrCreate(
            ['brevID' => $this->brevId],
            [
                'adresse'   => $this->filter['adresse'] ?? true,
                'dato'      => $this->filter['dato'] ?? true,
                'navn'      => $this->filter['navn'] ?? true,
                'sagsnr'    => $this->filter['sagsnr'] ?? true,
                'emne'      => $this->filter['emne'] ?? true,
                'skjulalle' => $this->filter['skjulalle'] ?? false,
                'visalle'   => $this->filter['visalle'] ?? false,
            ]
        );

        $this->loadBreveList();
        $this->loadBrev($this->brevId);

        $this->dispatch('toast', $toast->success('Skabelonen og brevhoved blev gemt'));
    }

    public function confirmDeleteBrev($id)
    {
        $brev = Brev::find($id);
        if ($brev) {
            $this->brevToDeleteId = $brev->id;
            $this->brevToDeleteTitle = $brev->titel;
            $this->showDeleteBrevModal = true;
        }
    }

    public function cancelDeleteBrev()
    {
        $this->showDeleteBrevModal = false;
        $this->brevToDeleteId = null;
        $this->brevToDeleteTitle = '';
    }

    public function executeDeleteBrev()
    {
        if ($this->brevToDeleteId) {
            Brev::where('id', $this->brevToDeleteId)->delete();
            $this->showDeleteBrevModal = false;
            $this->brevToDeleteId = null;
            $this->brevToDeleteTitle = '';
            $this->loadBreveList();
            if (!empty($this->breveList)) {
                $this->loadBrev($this->breveList[0]['id']);
            }
        }
    }

    public function getValidTokensProperty(): array
    {
        if (class_exists(BrevFeltIndstilling::class)) {
            return BrevFeltIndstilling::where('aktiv', true)->pluck('felt_navn')->toArray();
        }

        return [
            'today', 'aktiv', 'firmanavn', 'debitor_navn', 'debitor_adresse', 
            'debitor_postnr', 'debitor_postnummer', 'debitor_by', 'debitor_email', 
            'ktr', 'dato', 'sagsnr', 'restgaeld_dkg', 'restgaeld_kreditor'
        ];
    }

    public function resetTemplateLayout(): void
    {
        if (!$this->brevId) return;

        // Hent brevet og tjek om det er beskyttet
        $brev = Brev::find($this->brevId);
        if ($brev && $brev->beskyttet) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'Denne skabelon er beskyttet og kan ikke nulstilles!']);
            return;
        }

        // Rens teksten for farlige inline styles (som position: absolute, top: -200px osv.)
        // Vi bruger et simpelt regex til at fjerne style="..." attributter fra tags, hvis det ønskes,
        // eller renser blot marginer. Her fjerner vi style-attributter der indeholder 'position' eller 'top'.
        $tekstUdenAbsolut = preg_replace('/style="[^"]*?(position|top|left|right|bottom)[^"]*?"/i', '', $this->tekst);

        $this->tekst = $tekstUdenAbsolut;
        $this->margin_top = 0;
        $this->margin_left = 0;
        $this->margin_right = 0;

        $this->dispatch('toast', ['type' => 'success', 'message' => 'Layout og marginaler blev nulstillet. Husk at gemme!']);
    }

    // Åbn/luk Opret-modal
    public function openCreateModal(): void
    {
        $this->newBrevTitle = '';
        $this->showCreateModal = true;
    }

    public function closeCreateModal(): void
    {
        $this->showCreateModal = false;
        $this->newBrevTitle = '';
    }

    // Opret selve brevet
    public function createNewBrev(): void
    {
        if (empty(trim($this->newBrevTitle))) return;

        $brev = Brev::create([
            'titel'        => $this->newBrevTitle,
            'emne'         => '',
            'tekst'        => '',
            'margin_top'   => 0,
            'margin_left'  => 0,
            'margin_right' => 0,
            'beskyttet'    => 0,
            'linjeafstand' => 1.5,
            'brevpos'      => (Brev::max('brevpos') ?? 0) + 1,
            'brevtype' => '0',
        ]);

        $this->closeCreateModal();
        $this->loadBreveList();
        $this->loadBrev($brev->id);
    }

    // Åbn/luk Omdøb-modal
    public function openRenameModal(): void
    {
        if (!$this->brevId) return;

        // Hent brevet friskt fra databasen, og sæt editTitle med det samme
        $brev = Brev::find($this->brevId);
        if ($brev) {
            $this->editTitle = $brev->titel;
        }

        $this->showRenameModal = true;
    }

    public function closeRenameModal(): void
    {
        $this->showRenameModal = false;
    }

    public function updateBrevTitle(): void
    {
        if (!$this->brevId || empty(trim($this->editTitle))) return;

        $brev = Brev::find($this->brevId);
        if ($brev) {
            $brev->titel = $this->editTitle;
            $brev->save();
            $this->loadBreveList();
            $this->closeRenameModal();
        }
    }
    
    public function render()
    {
        return view('livewire.admin.breve.brev-manager');
    }
}