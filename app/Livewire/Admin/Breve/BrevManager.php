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
    
    // Layout marginaler og skriftstørrelser (i cm / pt)
    public $navn_top_margin = 5.45;
    public $dato_margin_top = 7.57;
    public $emne_top_margin = 8.91;
    public $body_margin_top = 1.00;
    
    public int $margin_left = 0;
    public int $margin_right = 0;

    public float $navn_font_size = 9.5;
    public float $emne_font_size = 10.5;
    public float $body_font_size = 9.5;
    public string $font_family = "Century Schoolbook, Georgia, serif";

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

    public bool $visfirmabrevhoved = false;
    public string $firma_navn = 'DANSK KONSULENT GRUPPE A/S';
    public string $firma_adresse = 'SCT. HANSGADE 9, 1 SAL, 4100 RINGSTED';
    public string $firma_telefon = '+45 22 22 68 94';
    public string $firma_mail = 'DKG@DKG-APS.DK';
    public string $firma_web = 'WWW.DKG-APS.DK';
    public string $firma_cvr = '21 24 72 86';
    public $brevhoved_top_margin = 1.50;
    
    public string|float $firma_font_size = 8.5;
    public string|float $firma_left_margin = 11.80;

    public array $filter = [
        'adresse'   => true,
        'dato'      => true,
        'navn'      => true,
        'sagsnr'    => true,
        'emne'      => true,
        'skjulalle' => false,
        'visalle'   => false,
    ];

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
            $this->brevId           = $brev->id;
            $this->titel            = $brev->titel ?? '';
            $this->editTitle        = $brev->titel ?? '';
            $this->emne             = $brev->emne ?? '';
            $this->tekst            = $brev->tekst ?? '';  
            
            // Indlæs nye layout- og skriftværdier (med faste standarder som fallback)
            $this->navn_top_margin  = $brev->navn_top_margin ?? 5.45;
            $this->dato_margin_top  = $brev->dato_margin_top ?? 7.57;
            $this->emne_top_margin  = $brev->emne_top_margin ?? 8.91;
            $this->body_margin_top  = $brev->body_margin_top ?? 1.00;
            
            $this->margin_left      = $brev->margin_left ?? 0;
            $this->margin_right     = $brev->margin_right ?? 0;
            
            $this->navn_font_size   = $brev->navn_font_size ?? 9.5;
            $this->emne_font_size   = $brev->emne_font_size ?? 10.5;
            $this->body_font_size   = $brev->body_font_size ?? 9.5;
            $this->font_family      = $brev->font_family ?? "Century Schoolbook, Georgia, serif";

            $this->beskyttet        = (bool) ($brev->beskyttet ?? 0);
            $this->linjeafstand     = $brev->linjeafstand ?? 1.5;

            $this->visfirmabrevhoved = (bool) ($brev->visfirmabrevhoved ?? false);
            $this->firma_navn        = $brev->firma_navn ?? 'DANSK KONSULENT GRUPPE A/S';
            $this->firma_adresse     = $brev->firma_adresse ?? 'SCT. HANSGADE 9, 1 SAL, 4100 RINGSTED';
            $this->firma_telefon     = $brev->firma_telefon ?? '+45 22 22 68 94';
            $this->firma_mail        = $brev->firma_mail ?? 'DKG@DKG-APS.DK';
            $this->firma_web         = $brev->firma_web ?? 'WWW.DKG-APS.DK';
            $this->firma_cvr         = $brev->firma_cvr ?? '21 24 72 86';
            $this->brevhoved_top_margin = $brev->brevhoved_top_margin ?? 1.50;
            
            $this->firma_font_size   = $brev->firma_font_size ?? 8.5;
            $this->firma_left_margin = $brev->firma_left_margin ?? 11.80;
            
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

        // Gem selve brevet inkl. alle nye layout- og skriftfelter
        Brev::where('id', $this->brevId)->update([
            'titel'           => $this->titel,
            'emne'            => $this->emne,
            'tekst'           => $this->tekst,
            'navn_top_margin' => $this->navn_top_margin,
            'dato_margin_top' => $this->dato_margin_top,
            'emne_top_margin' => $this->emne_top_margin,
            'body_margin_top' => $this->body_margin_top,
            'margin_left'     => $this->margin_left,
            'margin_right'    => $this->margin_right,
            'navn_font_size'  => $this->navn_font_size,
            'emne_font_size'  => $this->emne_font_size,
            'body_font_size'  => $this->body_font_size,
            'font_family'     => $this->font_family,
            'beskyttet'       => $this->beskyttet ? 1 : 0, 
            'linjeafstand'    => $this->linjeafstand,
            'visfirmabrevhoved' => $this->visfirmabrevhoved,
            'firma_navn'        => $this->firma_navn,
            'firma_adresse'     => $this->firma_adresse,
            'firma_telefon'     => $this->firma_telefon,
            'firma_mail'        => $this->firma_mail,
            'firma_web'         => $this->firma_web,
            'firma_cvr'         => $this->firma_cvr,
            'brevhoved_top_margin' => (float) str_replace(',', '.', (string) ($this->brevhoved_top_margin ?: 1.50)),
            'firma_font_size'   => (float) str_replace(',', '.', (string) ($this->firma_font_size ?: 8.5)),
            'firma_left_margin' => (float) str_replace(',', '.', (string) ($this->firma_left_margin ?: 11.80)),
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

        $brev = Brev::find($this->brevId);
        if ($brev && $brev->beskyttet) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'Denne skabelon er beskyttet og kan ikke nulstilles!']);
            return;
        }

        $tekstUdenAbsolut = preg_replace('/style="[^"]*?(position|top|left|right|bottom)[^"]*?"/i', '', $this->tekst);

        $this->tekst = $tekstUdenAbsolut;
        $this->navn_top_margin = 5.45;
        $this->dato_margin_top = 7.57;
        $this->emne_top_margin = 8.91;
        $this->body_margin_top = 1.00;
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
            'titel'             => $this->newBrevTitle,
            'emne'              => '',
            'tekst'             => '',
            'navn_top_margin'   => 5.45,
            'dato_margin_top'   => 7.57,
            'emne_top_margin'   => 8.91,
            'body_margin_top'   => 1.00,
            'margin_left'       => 0,
            'margin_right'      => 0,
            'navn_font_size'    => 9.5,
            'emne_font_size'    => 10.5,
            'body_font_size'    => 9.5,
            'font_family'       => "Century Schoolbook, Georgia, serif",
            'beskyttet'         => 0,
            'linjeafstand'      => 1.5,
            'visfirmabrevhoved' => false,
            'brevpos'           => (Brev::max('brevpos') ?? 0) + 1,
            'brevtype'          => '0',
        ]);

        $this->closeCreateModal();
        $this->loadBreveList();
        $this->loadBrev($brev->id);
    }

    // Åbn/luk Omdøb-modal
    public function openRenameModal(): void
    {
        if (!$this->brevId) return;

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
    
    public function getSkjulAltProperty(): bool
    {
        return $this->filter['skjulalle'] ?? false;
    }

    public function getVisAltProperty(): bool
    {
        return $this->filter['visalle'] ?? false;
    }

    public function getVisNavnProperty(): bool
    {
        return $this->visAlt || (!$this->skjulAlt && ($this->filter['navn'] ?? true));
    }

    public function getVisAdresseProperty(): bool
    {
        return $this->visAlt || (!$this->skjulAlt && ($this->filter['adresse'] ?? true));
    }

    public function getVisDatoProperty(): bool
    {
        return $this->visAlt || (!$this->skjulAlt && ($this->filter['dato'] ?? true));
    }

    public function getVisSagsnrProperty(): bool
    {
        return $this->visAlt || (!$this->skjulAlt && ($this->filter['sagsnr'] ?? true));
    }

    public function getVisEmneProperty(): bool
    {
        return $this->visAlt || (!$this->skjulAlt && ($this->filter['emne'] ?? true));
    }

    public function getBrevVisningstilstandProperty(): array
    {
        $skjulAlt = $this->filter['skjulalle'] ?? false;
        $visAlt   = $this->filter['visalle'] ?? false;

        return [
            'skjulAlt' => $skjulAlt,
            'visAlt'   => $visAlt,
            'visNavn'    => $visAlt || (!$skjulAlt && ($this->filter['navn'] ?? true)),
            'visAdresse' => $visAlt || (!$skjulAlt && ($this->filter['adresse'] ?? true)),
            'visDato'    => $visAlt || (!$skjulAlt && ($this->filter['dato'] ?? true)),
            'visSagsnr'  => $visAlt || (!$skjulAlt && ($this->filter['sagsnr'] ?? true)),
            'visEmne'    => $visAlt || (!$skjulAlt && ($this->filter['emne'] ?? true)),
        ];
    }

    public function nulstilAlleMarginer()
    {
        // Opdaterer samtlige brevskabeloner i databasen på én gang
        Brev::query()->update([
            'navn_top_margin' => 0,
            'dato_margin_top' => 0,
            'emne_top_margin' => 0,
            'body_margin_top' => 0,
            'brevhoved_top_margin' => 0,
        ]);

        // Genindlæs det aktuelt valgte brev, så visningen opdateres med det samme
        if ($this->brevId) {
            $this->loadBrev($this->brevId);
        }

        session()->flash('message', 'Alle marginer er nulstillet til 0 for samtlig skabeloner.');
    }

    public function updatedNavnTopMargin($value)
    {
        $this->navn_top_margin = str_replace(',', '.', $value);
    }

    public function updatedDatoMarginTop($value)
    {
        $this->dato_margin_top = str_replace(',', '.', $value);
    }

    public function updatedEmneMarginTop($value)
    {
        $this->emne_top_margin = str_replace(',', '.', $value);
    }

    public function updatedBodyMarginTop($value)
    {
        $this->body_margin_top = str_replace(',', '.', $value);
    }

    public function updatedVisfirmabrevhoved(bool $value): void
    {
        if ($this->brevId) {
            Brev::whereKey($this->brevId)->update([
                'visfirmabrevhoved' => $value,
            ]);
        }
    }

    public function render()
    {
        return view('livewire.admin.breve.brev-manager');
    }
}