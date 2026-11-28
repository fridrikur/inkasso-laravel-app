<?php


namespace App\Livewire\Admin\Breve;

use Livewire\Component;
use App\Models\Brev;
use App\Models\BrevFeltIndstilling;
use App\Services\ToastService;
use Livewire\WithFileUploads;
use App\Models\BrevFilter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use App\Models\BrevType;

class BrevManager extends Component
{
    use WithFileUploads;

    // Import / Export af brevskabeloner
    public bool $showBackupModal = false;
    public string $exportScope = 'all'; // 'all' eller 'protected'
    public $importJsonFile = null;
    public bool $overwriteExistingBreve = false;

    public ?int $brevId = null;
    public string $titel = '';
    public string $emne = '';
    public string $sub_emne = '';
    public string $tekst = '';
    
    // Layout marginaler og skriftstørrelser (i cm / pt)
    public $navn_top_margin = 5.45;
    public $dato_margin_top = 7.57;
    public $emne_top_margin = 8.91;
    public $sub_emne_top_margin = 0.05;
    public $body_margin_top = 1.00;
    
    public int $margin_left = 0;
    public int $margin_right = 0;

    public float $navn_font_size = 9.5;
    public float $emne_font_size = 10.5;
    public float $body_font_size = 9.5;
    public string $font_family = "Century Schoolbook, Georgia, serif";
    public string $font_color = '#000000';

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

    public bool $settingsOpen = true;

    public array $filter = [
        'adresse'   => true,
        'dato'      => true,
        'navn'      => true,
        'sagsnr'    => true,
        'emne'      => true,
        'skjulalle' => false,
        'visalle'   => false,
    ];

    public $brevtype_id = '';
    public string $selectedBrevTypeFilter = '';

    // Admin-modal til brevtyper fra databasen
    public bool $showBrevTyperModal = false;
    public string $newBrevTypeName = '';
    public ?int $editingBrevTypeId = null;
    public string $editingBrevTypeName = '';
    public array $brevTyperList = [];

    public array $serverBackups = [];
    public string $backupStorageFolder = 'backups/breve';
    public string $selectedStorageBackup = '';
    public bool $overwriteOnStorageImport = false;

    public function getAllowedFontColorsProperty(): array
    {
        return [
            '#000000' => 'Ren sort (#000000)',
            '#111827' => 'Mørk kulsort (#111827)',
            '#1e293b' => 'Skifer-sort (#1e293b)',
            '#27272a' => 'Zink-sort (#27272a)',
            '#374151' => 'Koksgrå / mørk (#374151)',
        ];
    }

    protected function sanitizeFontColor(?string $color): string
    {
        $normalized = strtolower(trim((string) $color));
        $allowed = array_map('strtolower', array_keys($this->allowedFontColors));

        return in_array($normalized, $allowed, true) ? $normalized : '#000000';
    }

    public function mount()
    {
        $this->loadBrevTyperList();
        $this->loadBreveList();

        if (!empty($this->breveList)) {
            $this->loadBrev($this->breveList[0]['id']);
        }
    }

    public function loadBrev($id): void
    {
        $brev = Brev::find($id);
        if (!$brev) {
            return;
        }

        // Skjul top-indstillingerne automatisk, når der skiftes til en fane/skabelon
        $this->settingsOpen = false;

        if ($brev) {
            $this->brevId      = $brev->id;
            $this->brevtype_id = $brev->brevtype_id ? (int) $brev->brevtype_id : '';
            $this->titel               = $brev->titel ?? '';
            $this->editTitle           = $brev->titel ?? '';
            $this->emne                = $brev->emne ?? '';
            $this->sub_emne            = strip_tags($brev->sub_emne ?? '');
            $this->tekst               = $brev->tekst ?? '';  
            
            // Indlæs nye layout- og skriftværdier (med faste standarder som fallback)
            $this->navn_top_margin     = $brev->navn_top_margin ?? 5.45;
            $this->dato_margin_top     = $brev->dato_margin_top ?? 7.57;
            $this->emne_top_margin     = $brev->emne_top_margin ?? 8.91;
            $this->sub_emne_top_margin = $brev->sub_emne_top_margin ?? 0.40;
            $this->body_margin_top     = $brev->body_margin_top ?? 1.00;
            
            $this->margin_left         = $brev->margin_left ?? 0;
            $this->margin_right        = $brev->margin_right ?? 0;
            
            $this->navn_font_size      = $brev->navn_font_size ?? 9.5;
            $this->emne_font_size      = $brev->emne_font_size ?? 10.5;
            $this->body_font_size      = $brev->body_font_size ?? 9.5;
            $this->font_family         = $brev->font_family ?? "Century Schoolbook, Georgia, serif";
            $this->font_color          = $this->sanitizeFontColor($brev->font_color ?? '#000000');

            $this->beskyttet           = (bool) ($brev->beskyttet ?? 0);
            $this->linjeafstand        = $brev->linjeafstand ?? 1.5;

            $this->visfirmabrevhoved   = (bool) ($brev->visfirmabrevhoved ?? false);
            $this->firma_navn          = $brev->firma_navn ?? 'DANSK KONSULENT GRUPPE A/S';
            $this->firma_adresse       = $brev->firma_adresse ?? 'SCT. HANSGADE 9, 1 SAL, 4100 RINGSTED';
            $this->firma_telefon       = $brev->firma_telefon ?? '+45 22 22 68 94';
            $this->firma_mail          = $brev->firma_mail ?? 'DKG@DKG-APS.DK';
            $this->firma_web           = $brev->firma_web ?? 'WWW.DKG-APS.DK';
            $this->firma_cvr           = $brev->firma_cvr ?? '21 24 72 86';
            $this->brevhoved_top_margin = $brev->brevhoved_top_margin ?? 1.50;
            
            $this->firma_font_size     = $brev->firma_font_size ?? 8.5;
            $this->firma_left_margin   = $brev->firma_left_margin ?? 11.80;
            
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

        $cleanSubEmne = strip_tags($this->sub_emne);
        $this->sub_emne = $cleanSubEmne;

        $cleanFontColor = $this->sanitizeFontColor($this->font_color);
        $this->font_color = $cleanFontColor;

        $cleanSubEmneMargin = (float) str_replace(',', '.', (string) ($this->sub_emne_top_margin ?: 0.40));

        $cleanBrevtypeId = ($this->brevtype_id !== '' && $this->brevtype_id !== null)
        ? (int) $this->brevtype_id
        : null;

        // Gem selve brevet inkl. alle nye layout- og skriftfelter
        Brev::where('id', $this->brevId)->update([
            'titel'               => $this->titel,
            'brevtype_id'         => $cleanBrevtypeId,
            'emne'                => $this->emne,
            'sub_emne'            => $cleanSubEmne,
            'tekst'               => $this->tekst,
            'navn_top_margin'     => $this->navn_top_margin,
            'dato_margin_top'     => $this->dato_margin_top,
            'emne_top_margin'     => $this->emne_top_margin,
            'sub_emne_top_margin' => $cleanSubEmneMargin,
            'body_margin_top'     => $this->body_margin_top,
            'margin_left'         => $this->margin_left,
            'margin_right'        => $this->margin_right,
            'navn_font_size'      => $this->navn_font_size,
            'emne_font_size'      => $this->emne_font_size,
            'body_font_size'      => $this->body_font_size,
            'font_family'         => $this->font_family,
            'font_color'          => $cleanFontColor,
            'beskyttet'           => $this->beskyttet ? 1 : 0, 
            'linjeafstand'        => $this->linjeafstand,
            'visfirmabrevhoved'   => $this->visfirmabrevhoved,
            'firma_navn'          => $this->firma_navn,
            'firma_adresse'       => $this->firma_adresse,
            'firma_telefon'       => $this->firma_telefon,
            'firma_mail'          => $this->firma_mail,
            'firma_web'           => $this->firma_web,
            'firma_cvr'           => $this->firma_cvr,
            'brevhoved_top_margin' => (float) str_replace(',', '.', (string) ($this->brevhoved_top_margin ?: 1.50)),
            'firma_font_size'     => (float) str_replace(',', '.', (string) ($this->firma_font_size ?: 8.5)),
            'firma_left_margin'   => (float) str_replace(',', '.', (string) ($this->firma_left_margin ?: 11.80)),
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
        $this->loadBrevTyperList();
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
        $this->sub_emne_top_margin = 0.40;
        $this->body_margin_top = 1.00;
        $this->font_color = '#000000';
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
            'titel'                => $this->newBrevTitle,
            'brevtype_id'          => null,
            'emne'                 => '',
            'sub_emne'             => '',
            'tekst'                => '',
            'navn_top_margin'      => 5.45,
            'dato_margin_top'      => 7.57,
            'emne_top_margin'      => 8.91,
            'sub_emne_top_margin'  => 0.40,
            'body_margin_top'      => 1.00,
            'margin_left'          => 0,
            'margin_right'         => 0,
            'navn_font_size'       => 9.5,
            'emne_font_size'       => 10.5,
            'body_font_size'       => 9.5,
            'font_family'          => "Century Schoolbook, Georgia, serif",
            'font_color'           => '#000000',
            'beskyttet'            => 0,
            'linjeafstand'         => 1.5,
            'visfirmabrevhoved'    => false,
            'brevhoved_top_margin' => 1.50,
            'firma_font_size'      => 8.5,
            'firma_left_margin'    => 11.80,
            'brevpos'              => (Brev::max('brevpos') ?? 0) + 1,
        ]);

        $this->closeCreateModal();
        $this->loadBreveList();
        $this->loadBrevTyperList();
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
            'navn_top_margin'     => 0,
            'dato_margin_top'     => 0,
            'emne_top_margin'     => 0,
            'sub_emne_top_margin' => 0,
            'body_margin_top'     => 0,
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

    public function updatedSubEmneTopMargin($value)
    {
        $this->sub_emne_top_margin = str_replace(',', '.', $value);
    }

    public function updatedBodyMarginTop($value)
    {
        $this->body_margin_top = str_replace(',', '.', $value);
    }

    public function updatedFontColor(string $value): void
    {
        $this->font_color = $this->sanitizeFontColor($value);
    }

    public function updatedVisfirmabrevhoved(bool $value): void
    {
        if ($this->brevId) {
            Brev::whereKey($this->brevId)->update([
                'visfirmabrevhoved' => $value,
            ]);
        }
    }

    public function openBackupModal(): void
    {
        $this->exportScope = 'all';
        $this->overwriteOnStorageImport = false;
        $this->loadServerBackups();
        $this->showBackupModal = true;
    }

    public function closeBackupModal(): void
    {
        $this->showBackupModal = false;
        $this->importJsonFile = null;
    }

    public function getAntalAlleBreveProperty(): int
    {
        return Brev::count();
    }

    public function getAntalBeskyttedeBreveProperty(): int
    {
        return Brev::where('beskyttet', 1)->count();
    }

    public function exportBreveJson()
    {
        $query = Brev::with('brevType')
            ->orderBy('brevpos')
            ->orderBy('id');

        if ($this->exportScope === 'protected') {
            $query->where('beskyttet', 1);
        }

        $breve = $query->get();

        if ($breve->isEmpty()) {
            $besked = $this->exportScope === 'protected'
                ? 'Der er ingen beskyttede breve at eksportere.'
                : 'Der er ingen breve at eksportere.';

            $this->dispatch('toast', ['type' => 'error', 'message' => $besked]);
            return null;
        }

        $filtersByBrevId = BrevFilter::whereIn('brevID', $breve->pluck('id'))
            ->get()
            ->keyBy('brevID');

        $payload = [
            'exported_at' => now()->toIso8601String(),
            'scope'       => $this->exportScope,
            'count'       => $breve->count(),
            'breve'       => $breve->map(function (Brev $brev) use ($filtersByBrevId) {
                $data = $brev->toArray();
                unset($data['id'], $data['created_at'], $data['updated_at'], $data['brev_type']);

                // Gem brevtypen på navn så skabelonen kan matches på tværs af miljøer
                $data['brevtype_navn'] = $brev->brevType?->navn;

                $filter = $filtersByBrevId->get($brev->id);
                $data['filter'] = $filter ? [
                    'adresse'   => (bool) $filter->adresse,
                    'dato'      => (bool) $filter->dato,
                    'navn'      => (bool) $filter->navn,
                    'sagsnr'    => (bool) $filter->sagsnr,
                    'emne'      => (bool) $filter->emne,
                    'skjulalle' => (bool) $filter->skjulalle,
                    'visalle'   => (bool) $filter->visalle,
                ] : null;

                return $data;
            })->values()->all(),
        ];

        $prefix = $this->exportScope === 'protected' ? 'beskyttede-breve' : 'alle-breve';
        $filename = $prefix . '-' . now()->format('Y-m-d-His') . '.json';

        return response()->streamDownload(
            function () use ($payload) {
                echo json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            },
            $filename,
            ['Content-Type' => 'application/json; charset=UTF-8']
        );
    }

    public function getAntalSkabelonerProperty(): int
    {
        return count($this->breveList);
    }

    public function getAntalBeskyttedeSkabelonerProperty(): int
    {
        return collect($this->breveList)->where('beskyttet', true)->count();
    }

    public function selectBrevTab(int $id): void
    {
        $this->settingsOpen = false;
        $this->loadBrev($id);
    }

    

    public function loadBrevTyperList(): void
    {
        $this->brevTyperList = BrevType::withCount('breve')
            ->orderBy('sorteringsorden')
            ->orderBy('navn')
            ->get()
            ->toArray();
    }

    public function openBrevTyperModal(): void
    {
        $this->newBrevTypeName = '';
        $this->editingBrevTypeId = null;
        $this->editingBrevTypeName = '';
        $this->loadBrevTyperList();
        $this->showBrevTyperModal = true;
    }

    public function closeBrevTyperModal(): void
    {
        $this->showBrevTyperModal = false;
        $this->newBrevTypeName = '';
        $this->editingBrevTypeId = null;
        $this->editingBrevTypeName = '';
    }

    public function createBrevType(ToastService $toast): void
    {
        $navn = trim($this->newBrevTypeName);

        if ($navn === '') {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'Angiv et navn til brevtypen.']);
            return;
        }

        $findesAllerede = BrevType::whereRaw('LOWER(navn) = ?', [mb_strtolower($navn)])->exists();
        if ($findesAllerede) {
            $this->dispatch('toast', ['type' => 'error', 'message' => "Brevtypen '{$navn}' findes allerede."]);
            return;
        }

        $naesteSortering = ((int) BrevType::max('sorteringsorden')) + 1;

        BrevType::create([
            'navn'            => $navn,
            'sorteringsorden' => $naesteSortering,
            'aktiv'           => true,
        ]);

        $this->newBrevTypeName = '';
        $this->loadBrevTyperList();

        $this->dispatch('toast', $toast->success("Brevtypen '{$navn}' blev oprettet."));
    }

    public function startEditBrevType(int $id): void
    {
        $type = BrevType::find($id);
        if (!$type) {
            return;
        }

        $this->editingBrevTypeId = $type->id;
        $this->editingBrevTypeName = $type->navn;
    }

    public function cancelEditBrevType(): void
    {
        $this->editingBrevTypeId = null;
        $this->editingBrevTypeName = '';
    }

    public function renameBrevType(ToastService $toast): void
    {
        if (!$this->editingBrevTypeId) {
            return;
        }

        $navn = trim($this->editingBrevTypeName);
        if ($navn === '') {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'Brevtypen skal have et navn.']);
            return;
        }

        $type = BrevType::find($this->editingBrevTypeId);
        if (!$type) {
            return;
        }

        $dublet = BrevType::whereRaw('LOWER(navn) = ?', [mb_strtolower($navn)])
            ->where('id', '!=', $type->id)
            ->exists();

        if ($dublet) {
            $this->dispatch('toast', ['type' => 'error', 'message' => "En anden brevtype hedder allerede '{$navn}'."]);
            return;
        }

        $type->update(['navn' => $navn]);

        $this->editingBrevTypeId = null;
        $this->editingBrevTypeName = '';
        $this->loadBrevTyperList();

        $this->dispatch('toast', $toast->success("Brevtypen blev omdøbt til '{$navn}'."));
    }

    public function updatedSelectedBrevTypeFilter(): void
    {
        $this->loadBreveList();

        if (!empty($this->breveList)) {
            $ids = array_column($this->breveList, 'id');
            if (!in_array($this->brevId, $ids, true)) {
                $this->loadBrev($this->breveList[0]['id']);
            }
        }
    }

    public function loadBreveList(): void
    {
        $query = Brev::with('brevType')->orderBy('brevpos');

        if ($this->selectedBrevTypeFilter !== null && $this->selectedBrevTypeFilter !== '') {
            if ($this->selectedBrevTypeFilter === 'none') {
                $query->whereNull('brevtype_id');
            } else {
                $query->where('brevtype_id', (int) $this->selectedBrevTypeFilter);
            }
        }

        $this->breveList = $query->get()->toArray();
    }
    
    public function updatedBrevtypeId($value): void
    {
        $this->brevtype_id = ($value === '' || $value === null) ? '' : (int) $value;
    }

    public function getAktivBrevTypeNavnProperty(): ?string
    {
        if ($this->brevtype_id === '' || $this->brevtype_id === null) {
            return null;
        }

        $match = collect($this->brevTyperList)->firstWhere('id', (int) $this->brevtype_id);
        return $match['navn'] ?? null;
    }
    
    public function importBreveJson(ToastService $toast): void
    {
        $this->validate([
            'importJsonFile' => 'required|file|mimes:json,txt|max:10240',
        ]);

        try {
            $raw = file_get_contents($this->importJsonFile->getRealPath());
            $decoded = json_decode($raw, true);

            $items = is_array($decoded) && isset($decoded['breve']) && is_array($decoded['breve'])
                ? $decoded['breve']
                : (is_array($decoded) ? $decoded : null);

            if (!$items) {
                $this->dispatch('toast', ['type' => 'error', 'message' => 'Ugyldig JSON-fil: Kunne ikke læse brevskabeloner.']);
                return;
            }

            $columns = Schema::getColumnListing('breve');
            $feltMapping = [
                '{Statistik}' => '{restgaeld_dkg}',
                '{statistik}' => '{restgaeld_dkg}',
                '{restgaeld}'  => '{ialt}',
            ];

            $oprettet = 0;
            $opdateret = 0;
            $sprungetOver = 0;
            $setTitlerIFil = [];
            $typeIdCache = [];

            DB::transaction(function () use (
                $items,
                $columns,
                $feltMapping,
                &$oprettet,
                &$opdateret,
                &$sprungetOver,
                &$setTitlerIFil,
                &$typeIdCache
            ) {
                foreach ($items as $item) {
                    $titel = trim((string) ($item['titel'] ?? ''));
                    if (!is_array($item) || $titel === '') {
                        continue;
                    }

                    // Bremse: spring dubletter med samme titel inden for selve JSON-filen over
                    $titelKey = mb_strtolower($titel);
                    if (isset($setTitlerIFil[$titelKey])) {
                        $sprungetOver++;
                        continue;
                    }
                    $setTitlerIFil[$titelKey] = true;

                    $filterData = is_array($item['filter'] ?? null) ? $item['filter'] : null;
                    $brevTypeNavn = trim((string) ($item['brevtype_navn'] ?? ''));

                    unset($item['id'], $item['created_at'], $item['updated_at'], $item['filter'], $item['brevtype_navn']);
                    $item['titel'] = $titel;

                    // Oversæt brevtype_navn til lokalt brevtype_id i dette miljø
                    $resolvedBrevTypeId = null;
                    if ($brevTypeNavn !== '') {
                        $typeKey = mb_strtolower($brevTypeNavn);

                        if (!array_key_exists($typeKey, $typeIdCache)) {
                            $existingType = BrevType::whereRaw('LOWER(navn) = ?', [$typeKey])->first();

                            if ($existingType) {
                                $typeIdCache[$typeKey] = (int) $existingType->id;
                            } else {
                                $nextSort = ((int) BrevType::max('sorteringsorden')) + 1;
                                $createdType = BrevType::create([
                                    'navn'            => $brevTypeNavn,
                                    'sorteringsorden' => $nextSort,
                                    'aktiv'           => true,
                                ]);
                                $typeIdCache[$typeKey] = (int) $createdType->id;
                            }
                        }

                        $resolvedBrevTypeId = $typeIdCache[$typeKey];
                    }

                    foreach (['tekst', 'emne', 'sub_emne'] as $felt) {
                        if (isset($item[$felt]) && is_string($item[$felt])) {
                            $item[$felt] = str_replace(
                                array_keys($feltMapping),
                                array_values($feltMapping),
                                $item[$felt]
                            );
                        }
                    }

                    if (isset($item['sub_emne'])) {
                        $item['sub_emne'] = strip_tags((string) $item['sub_emne']);
                    }

                    if (isset($item['font_color'])) {
                        $item['font_color'] = $this->sanitizeFontColor($item['font_color']);
                    }

                    $cleanRow = array_intersect_key($item, array_flip($columns));
                    $cleanRow['titel'] = $titel;
                    $cleanRow['tekst'] = (string) ($cleanRow['tekst'] ?? '');

                    if (in_array('brevtype_id', $columns, true)) {
                        $cleanRow['brevtype_id'] = $resolvedBrevTypeId;
                    }

                    if (in_array('emne', $columns, true) && (!array_key_exists('emne', $cleanRow) || $cleanRow['emne'] === null)) {
                        $cleanRow['emne'] = '';
                    }

                    if (in_array('sub_emne', $columns, true) && (!array_key_exists('sub_emne', $cleanRow) || $cleanRow['sub_emne'] === null)) {
                        $cleanRow['sub_emne'] = '';
                    }

                    $eksisterende = Brev::where('titel', $titel)->first();

                    if ($eksisterende) {
                        if (!$this->overwriteExistingBreve) {
                            $sprungetOver++;
                            continue;
                        }

                        $eksisterende->update($cleanRow);
                        $targetBrevId = $eksisterende->id;
                        $opdateret++;
                    } else {
                        $cleanRow['brevpos'] = $cleanRow['brevpos'] ?? (((int) Brev::max('brevpos')) + 1);
                        $cleanRow['brevtype'] = $cleanRow['brevtype'] ?? '0';

                        try {
                            $nyBrev = Brev::create($cleanRow);
                            $targetBrevId = $nyBrev->id;
                            $oprettet++;
                        } catch (\Illuminate\Database\QueryException $qe) {
                            // Hvis unik constraint (1062 / 23000) udløses, tæl som sprunget over
                            if (($qe->errorInfo[1] ?? null) === 1062 || str_contains($qe->getMessage(), '1062')) {
                                $sprungetOver++;
                                continue;
                            }
                            throw $qe;
                        }
                    }

                    if ($filterData && $targetBrevId) {
                        BrevFilter::updateOrCreate(
                            ['brevID' => $targetBrevId],
                            [
                                'adresse'   => (bool) ($filterData['adresse'] ?? true),
                                'dato'      => (bool) ($filterData['dato'] ?? true),
                                'navn'      => (bool) ($filterData['navn'] ?? true),
                                'sagsnr'    => (bool) ($filterData['sagsnr'] ?? true),
                                'emne'      => (bool) ($filterData['emne'] ?? true),
                                'skjulalle' => (bool) ($filterData['skjulalle'] ?? false),
                                'visalle'   => (bool) ($filterData['visalle'] ?? false),
                            ]
                        );
                    }
                }
            });

            $this->loadBrevTyperList();
            $this->loadBreveList();
            if ($this->brevId) {
                $this->loadBrev($this->brevId);
            } elseif (!empty($this->breveList)) {
                $this->loadBrev($this->breveList[0]['id']);
            }

            $this->closeBackupModal();

            $this->dispatch('toast', $toast->success(
                "Import fuldført: {$oprettet} oprettet, {$opdateret} opdateret, {$sprungetOver} sprunget over"
            ));
        } catch (\Throwable $e) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'Fejl ved import: ' . $e->getMessage()]);
        }
    }

    public function getAktivBrevTitelProperty(): string
    {
        if (trim($this->titel) !== '') {
            return $this->titel;
        }

        if ($this->brevId) {
            $match = collect($this->breveList)->firstWhere('id', $this->brevId);
            if (!empty($match['titel'])) {
                return $match['titel'];
            }
        }

        return 'Unavngivet skabelon';
    }

    protected function buildBreveBackupPayload(): ?array
    {
        $query = Brev::with('brevType')
            ->orderBy('brevpos')
            ->orderBy('id');

        if ($this->exportScope === 'protected') {
            $query->where('beskyttet', 1);
        }

        $breve = $query->get();

        if ($breve->isEmpty()) {
            $besked = $this->exportScope === 'protected'
                ? 'Der er ingen beskyttede breve at sikkerhedskopiere.'
                : 'Der er ingen breve at sikkerhedskopiere.';

            $this->dispatch('toast', ['type' => 'error', 'message' => $besked]);
            return null;
        }

        $filtersByBrevId = BrevFilter::whereIn('brevID', $breve->pluck('id'))
            ->get()
            ->keyBy('brevID');

        return [
            'exported_at' => now()->toIso8601String(),
            'scope'       => $this->exportScope,
            'count'       => $breve->count(),
            'breve'       => $breve->map(function (Brev $brev) use ($filtersByBrevId) {
                $data = $brev->toArray();
                unset($data['id'], $data['created_at'], $data['updated_at'], $data['brev_type']);

                $data['brevtype_navn'] = $brev->brevType?->navn;

                $filter = $filtersByBrevId->get($brev->id);
                $data['filter'] = $filter ? [
                    'adresse'   => (bool) $filter->adresse,
                    'dato'      => (bool) $filter->dato,
                    'navn'      => (bool) $filter->navn,
                    'sagsnr'    => (bool) $filter->sagsnr,
                    'emne'      => (bool) $filter->emne,
                    'skjulalle' => (bool) $filter->skjulalle,
                    'visalle'   => (bool) $filter->visalle,
                ] : null;

                return $data;
            })->values()->all(),
        ];
    }

    public function saveBackupToStorage(ToastService $toast): void
    {
        $payload = $this->buildBreveBackupPayload();
        if (!$payload) {
            return;
        }

        $prefix = $this->exportScope === 'protected' ? 'beskyttede-breve' : 'alle-breve';
        $filename = $prefix . '-' . now()->format('Y-m-d-His') . '.json';
        $path = $this->backupStorageFolder . '/' . $filename;

        Storage::disk('local')->put(
            $path,
            json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );

        $this->loadServerBackups();
        $this->dispatch('toast', $toast->success("Backup gemt på serveren: {$filename}"));
    }

    public function downloadServerBackup(string $filename)
    {
        $safeFilename = basename($filename);
        $path = $this->backupStorageFolder . '/' . $safeFilename;
        $disk = Storage::disk('local');

        if (!$disk->exists($path)) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'Backup-filen blev ikke fundet på serveren.']);
            return null;
        }

        return $disk->download($path, $safeFilename, [
            'Content-Type' => 'application/json; charset=UTF-8',
        ]);
    }

    
    public function loadServerBackups(): void
    {
        $disk = Storage::disk('local');

        if (!$disk->exists($this->backupStorageFolder)) {
            $this->serverBackups = [];
            $this->selectedStorageBackup = '';
            return;
        }

        $files = collect($disk->files($this->backupStorageFolder))
            ->filter(fn (string $path) => str_ends_with(strtolower($path), '.json'))
            ->sortByDesc(fn (string $path) => $disk->lastModified($path))
            ->values();

        $this->serverBackups = $files->map(function (string $path) use ($disk) {
            return [
                'filename'      => basename($path),
                'size_kb'       => round($disk->size($path) / 1024, 1),
                'last_modified' => date('d.m.Y H:i:s', $disk->lastModified($path)),
            ];
        })->all();

        if ($this->selectedStorageBackup === '' && !empty($this->serverBackups)) {
            $this->selectedStorageBackup = $this->serverBackups[0]['filename'];
        }
    }

    /**
     * STORAGE EXPORT: Gemmer backup direkte i Storage på serveren
     */
    public function exportToStorage(ToastService $toast): void
    {
        $query = Brev::with('brevType')
            ->orderBy('brevpos')
            ->orderBy('id');

        if ($this->exportScope === 'protected') {
            $query->where('beskyttet', 1);
        }

        $breve = $query->get();

        if ($breve->isEmpty()) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'Der er ingen breve at eksportere til Storage.']);
            return;
        }

        $filtersByBrevId = BrevFilter::whereIn('brevID', $breve->pluck('id'))
            ->get()
            ->keyBy('brevID');

        $payload = [
            'exported_at' => now()->toIso8601String(),
            'scope'       => $this->exportScope,
            'count'       => $breve->count(),
            'breve'       => $breve->map(function (Brev $brev) use ($filtersByBrevId) {
                $data = $brev->toArray();
                unset($data['id'], $data['created_at'], $data['updated_at'], $data['brev_type']);

                $data['brevtype_navn'] = $brev->brevType?->navn;

                $filter = $filtersByBrevId->get($brev->id);
                $data['filter'] = $filter ? [
                    'adresse'   => (bool) $filter->adresse,
                    'dato'      => (bool) $filter->dato,
                    'navn'      => (bool) $filter->navn,
                    'sagsnr'    => (bool) $filter->sagsnr,
                    'emne'      => (bool) $filter->emne,
                    'skjulalle' => (bool) $filter->skjulalle,
                    'visalle'   => (bool) $filter->visalle,
                ] : null;

                return $data;
            })->values()->all(),
        ];

        $prefix = $this->exportScope === 'protected' ? 'beskyttede-breve' : 'alle-breve';
        $filename = $prefix . '-' . now()->format('Y-m-d-His') . '.json';
        $path = $this->backupStorageFolder . '/' . $filename;

        Storage::disk('local')->put(
            $path,
            json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );

        $this->selectedStorageBackup = $filename;
        $this->loadServerBackups();

        $this->dispatch('toast', $toast->success("Storage export fuldført: {$filename}"));
    }

    /**
     * STORAGE IMPORT: Gendanner skabeloner direkte fra valgt fil i Storage
     */
    public function importFromStorage(ToastService $toast): void
    {
        $safeFilename = basename($this->selectedStorageBackup);
        if ($safeFilename === '') {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'Vælg en backup-fil på serveren først.']);
            return;
        }

        $path = $this->backupStorageFolder . '/' . $safeFilename;
        $disk = Storage::disk('local');

        if (!$disk->exists($path)) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'Backup-filen findes ikke på serveren.']);
            $this->loadServerBackups();
            return;
        }

        try {
            $raw = $disk->get($path);
            $decoded = json_decode($raw, true);

            $items = is_array($decoded) && isset($decoded['breve']) && is_array($decoded['breve'])
                ? $decoded['breve']
                : (is_array($decoded) ? $decoded : null);

            if (!$items) {
                $this->dispatch('toast', ['type' => 'error', 'message' => 'Ugyldigt backup-indhold i filen på Storage.']);
                return;
            }

            $columns = Schema::getColumnListing('breve');
            $feltMapping = [
                '{Statistik}' => '{restgaeld_dkg}',
                '{statistik}' => '{restgaeld_dkg}',
                '{restgaeld}'  => '{ialt}',
            ];

            $oprettet = 0;
            $opdateret = 0;
            $sprungetOver = 0;
            $setTitlerIFil = [];
            $typeIdCache = [];

            DB::transaction(function () use (
                $items,
                $columns,
                $feltMapping,
                &$oprettet,
                &$opdateret,
                &$sprungetOver,
                &$setTitlerIFil,
                &$typeIdCache
            ) {
                foreach ($items as $item) {
                    $titel = trim((string) ($item['titel'] ?? ''));
                    if (!is_array($item) || $titel === '') {
                        continue;
                    }

                    $titelKey = mb_strtolower($titel);
                    if (isset($setTitlerIFil[$titelKey])) {
                        $sprungetOver++;
                        continue;
                    }
                    $setTitlerIFil[$titelKey] = true;

                    $filterData = is_array($item['filter'] ?? null) ? $item['filter'] : null;
                    $brevTypeNavn = trim((string) ($item['brevtype_navn'] ?? ''));

                    unset($item['id'], $item['created_at'], $item['updated_at'], $item['filter'], $item['brevtype_navn']);
                    $item['titel'] = $titel;

                    $resolvedBrevTypeId = null;
                    if ($brevTypeNavn !== '') {
                        $typeKey = mb_strtolower($brevTypeNavn);

                        if (!array_key_exists($typeKey, $typeIdCache)) {
                            $existingType = BrevType::whereRaw('LOWER(navn) = ?', [$typeKey])->first();

                            if ($existingType) {
                                $typeIdCache[$typeKey] = (int) $existingType->id;
                            } else {
                                $nextSort = ((int) BrevType::max('sorteringsorden')) + 1;
                                $createdType = BrevType::create([
                                    'navn'            => $brevTypeNavn,
                                    'sorteringsorden' => $nextSort,
                                    'aktiv'           => true,
                                ]);
                                $typeIdCache[$typeKey] = (int) $createdType->id;
                            }
                        }

                        $resolvedBrevTypeId = $typeIdCache[$typeKey];
                    }

                    foreach (['tekst', 'emne', 'sub_emne'] as $felt) {
                        if (isset($item[$felt]) && is_string($item[$felt])) {
                            $item[$felt] = str_replace(
                                array_keys($feltMapping),
                                array_values($feltMapping),
                                $item[$felt]
                            );
                        }
                    }

                    if (isset($item['sub_emne'])) {
                        $item['sub_emne'] = strip_tags((string) $item['sub_emne']);
                    }

                    if (isset($item['font_color'])) {
                        $item['font_color'] = $this->sanitizeFontColor($item['font_color']);
                    }

                    $cleanRow = array_intersect_key($item, array_flip($columns));
                    $cleanRow['titel'] = $titel;
                    $cleanRow['tekst'] = (string) ($cleanRow['tekst'] ?? '');

                    if (in_array('brevtype_id', $columns, true)) {
                        $cleanRow['brevtype_id'] = $resolvedBrevTypeId;
                    }

                    if (in_array('emne', $columns, true) && (!array_key_exists('emne', $cleanRow) || $cleanRow['emne'] === null)) {
                        $cleanRow['emne'] = '';
                    }

                    if (in_array('sub_emne', $columns, true) && (!array_key_exists('sub_emne', $cleanRow) || $cleanRow['sub_emne'] === null)) {
                        $cleanRow['sub_emne'] = '';
                    }

                    $eksisterende = Brev::where('titel', $titel)->first();

                    if ($eksisterende) {
                        if (!$this->overwriteOnStorageImport) {
                            $sprungetOver++;
                            continue;
                        }

                        $eksisterende->update($cleanRow);
                        $targetBrevId = $eksisterende->id;
                        $opdateret++;
                    } else {
                        $cleanRow['brevpos'] = $cleanRow['brevpos'] ?? (((int) Brev::max('brevpos')) + 1);
                        $cleanRow['brevtype'] = $cleanRow['brevtype'] ?? '0';

                        try {
                            $nyBrev = Brev::create($cleanRow);
                            $targetBrevId = $nyBrev->id;
                            $oprettet++;
                        } catch (\Illuminate\Database\QueryException $qe) {
                            if (($qe->errorInfo[1] ?? null) === 1062 || str_contains($qe->getMessage(), '1062')) {
                                $sprungetOver++;
                                continue;
                            }
                            throw $qe;
                        }
                    }

                    if ($filterData && $targetBrevId) {
                        BrevFilter::updateOrCreate(
                            ['brevID' => $targetBrevId],
                            [
                                'adresse'   => (bool) ($filterData['adresse'] ?? true),
                                'dato'      => (bool) ($filterData['dato'] ?? true),
                                'navn'      => (bool) ($filterData['navn'] ?? true),
                                'sagsnr'    => (bool) ($filterData['sagsnr'] ?? true),
                                'emne'      => (bool) ($filterData['emne'] ?? true),
                                'skjulalle' => (bool) ($filterData['skjulalle'] ?? false),
                                'visalle'   => (bool) ($filterData['visalle'] ?? false),
                            ]
                        );
                    }
                }
            });

            $this->loadBrevTyperList();
            $this->loadBreveList();
            if ($this->brevId) {
                $this->loadBrev($this->brevId);
            } elseif (!empty($this->breveList)) {
                $this->loadBrev($this->breveList[0]['id']);
            }

            $this->dispatch('toast', $toast->success(
                "Storage import fuldført: {$oprettet} oprettet, {$opdateret} opdateret, {$sprungetOver} sprunget over"
            ));
        } catch (\Throwable $e) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'Fejl ved storage import: ' . $e->getMessage()]);
        }
    }
    
    public function render()
    {
        return view('livewire.admin.breve.brev-manager');
    }
}