<?php

namespace App\Livewire\Sager;

use Livewire\Attributes\On;
use Livewire\Component;
use App\Models\Sager;
use App\Models\Konsulenter;
use App\Models\Sagsbehandler;
use App\Models\Tokens;
use App\Models\Sagervalglistetype;
use App\Models\Sagervalgliste;
use App\Models\Kreditorer;
use App\Models\Debitorer;
use App\Models\Status;
use App\Models\Bemaerkning;
use App\Models\afslutning;
use App\Models\Ktr;
use App\Models\Udlaeg;
use App\Livewire\Forms\SagForm;
use Illuminate\Support\Facades\Auth;
use App\Models\SagerFieldSetting;
use App\Models\DebitorFieldSetting;
use DB;
use ReflectionClass;
use ReflectionProperty;
use Illuminate\Support\Collection;
use App\Models\Postnr;
use App\Models\Dialog;
use App\Models\SagActivity;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Hash;
use App\Models\SagLock;
use Illuminate\Support\Facades\Cache;
use App\Models\SagEditRequest;
use Illuminate\Support\Facades\Broadcast;

class SagEditor extends Component
{
    #[On('tab-changed')]
    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public ?Sager $sag = null;

    public string $activeTab = 'stamdata';

    public bool $isSearchMode = false;

    public string $lockState = 'unlocked';

    public bool $hasLock = false;

    public bool $isLockedByOther = false;

    public bool $savedRecently = false;

    public string $reminderType = 'date'; // 'date' eller 'reason'
    
    protected bool $ready = false;

    protected $listeners = [
        'kreditor-changed' => 'onKreditorChanged',
        'klientinformationUpdated' => 'refreshKlientinformation',
    ];

    protected bool $totalsReady = false;

    public bool $loading = true;
    
    public array $aliases = [];

    public Collection $financialFields;
    public Collection $relationFields;
    public Collection $generalFields;
    public float $restgaeldInk = 0; // restgæld inkl. inkassosalær
    
    public array $financialFieldNames = [];
    public array $selectOptions = [];

    public int $kreditorHighlightIndex = 0;
    public array $kreditorSuggestions = [];
    public bool $showKreditorDropdown = false;
    public string $kreditorSearch = '';
    public string $kreditor_navn_search = '';
    public array $debitorAliases = [];
    public array $debitorRequiredFields = [];

    public $sagerFieldSettings = [];
    public $debitorFieldSettings = [];

    public SagForm $form;
    public $isEditMode = false;
    public $message;
    public $token_id = '';

    // Calculation fields
    public float $hovedstol = 0;
    public float $renter = 0;
    public float $gebyr = 0;
    public float $ialt = 0;
    public float $indbetalt = 0;
    public float $restgaeld = 0;
    public $highlightIalt = false;
    public $highlightRestgaeld = false;
    public $visibleFields = [];
    public $requiredFields = [];
    public $fieldSettings = [];
    public $allDynamicFields = [];
    public $role;
    public array $columns = [];
    public array $readonlyFields = [];
    public $preparedFields = [];
    public array $renderFields = [];
    public array $bySuggestions = [];
    public bool $showByDropdown = false;
    public string $lastByValue = '';
    public $newMessages = 0;
    public $newKlientinformation = 0;
    public $klientinformationUnread = 0;
    public ?array $lock = null;

    public bool $showAfsluttetDateReminder = false;
    
    public bool $showUnlockModal = false;
    public string $unlock_code = '';
    
    public bool $currentsagLocked = false;
    public string $unlockCode = '';

    public bool $showTakeoverModal = false;

    public \Illuminate\Support\Collection $pendingRequests;

    public ?int $acceptedModalUntil = null;

    public ?\App\Models\SagEditRequest $myTakeoverRequest = null;

    public bool $showAcceptedModal = false;

    public function lockSag(): void
    {
        \App\Models\SagLock::updateOrCreate(
            ['sag_id' => $this->sag->id],
            [
                'user_id' => auth()->id(),
                'locked_at' => now(),
            ]
        );

        $this->syncLockState();
    }

    public function openUnlockModal(): void
    {
        $this->showUnlockModal = true;
    }

    public function unlockSag(): void
    {
        if (!$this->sag?->id) return;

        SagLock::where('sag_id', $this->sag->id)
            ->where('user_id', auth()->id())
            ->delete();

        $this->syncLockState();
    }

    public function refreshKlientinformation()
    {
        $this->dispatch('sager.klientinformation', 'updateUnreadCount');
    }
    
    protected function loadFormRelations()
    {
        if (!$this->sag) return;

        $this->form->sag = $this->sag;
        $this->form->sag_id = $this->sag->id;

        foreach (['status', 'bemaerkning', 'ktr', 'afslutning','konsulent','udlaeg'] as $relation) {
            $this->form->setRelation($relation, $this->sag);
        }
        
        $this->form->SetKreditor($this->sag);
        $this->form->SetDebitor($this->sag);
        $this->form->SetSag($this->sag);
    }

    protected function loadSelectOptions(): void
    {
        $this->selectOptions['konsulent'] = Konsulenter::query()
            ->leftJoin('skjult_konsulent', 'konsulenters.id', '=', 'skjult_konsulent_id')
            ->whereNull('skjult_konsulent_id')
            ->pluck('konsulenters.navn', 'konsulenters.id')
            ->toArray();

        if (!$this->sag->exists) {
            $this->form->konsulent = DB::table('hoved_konsulent')->value('hoved_konsulent_id');
        }

        $this->selectOptions['status']      = Cache::rememberForever('select.status', fn () => Status::pluck('tekst', 'id')->toArray());
        $this->selectOptions['bemaerkning'] = Cache::rememberForever('select.bemaerkning', fn () => Bemaerkning::pluck('tekst', 'id')->toArray());
        $this->selectOptions['afslutning']  = Cache::rememberForever('select.afslutning', fn () => Afslutning::pluck('tekst', 'id')->toArray());
        $this->selectOptions['ktr']         = Cache::rememberForever('select.ktr', fn () => Ktr::pluck('tekst', 'id')->toArray());
        $this->selectOptions['udlaeg']      = Cache::rememberForever('select.udlaeg', fn () => Udlaeg::pluck('tekst', 'id')->toArray());
    }

    public function updatedFormKreditorLotusID($lotusId)
    {
        if (!$lotusId) {
            $this->form->kreditor_navn = '';
            $this->form->sagsbehandler = null;
            $this->selectOptions['sagsbehandler'] = [];
            return;
        }

        $kreditor = Kreditorer::where('lotusID', $lotusId)
            ->with('hovedsagsbehandlere')
            ->first();

        if (!$kreditor) {
            $this->form->kreditor_navn = '';
            $this->form->sagsbehandler = null;
            $this->selectOptions['sagsbehandler'] = [];
            return;
        }

        $this->form->kreditor_navn = $kreditor->navn;
        $this->selectOptions['sagsbehandler'] = Sagsbehandler::forKreditor($kreditor->id) ?? [];

        $firstHoved = $kreditor->hovedsagsbehandlere->first();
        if ($firstHoved && array_key_exists($firstHoved->id, $this->selectOptions['sagsbehandler'])) {
            $this->form->sagsbehandler = $firstHoved->id;
            $this->form->kreditor = $kreditor->id;
        } else {
            $this->form->sagsbehandler = null;
        }
    }

    public function mount($sag = null, $isSearchMode = false)
    {
        $this->loading = true;

        $this->sag = $sag?->load([
            'sagerkreditor',
            'sagersagsbehandler',
            'sagerdebitor'
        ]) ?? new Sager();

        $this->isEditMode = $this->sag->exists;

        if ($this->isEditMode) {
            $lock = SagLock::where('sag_id', $this->sag->id)->first();

            if ($lock) {
                if ($lock->user_id === auth()->id()) {
                    $this->hasLock = true;
                    $this->isLockedByOther = false;
                } else {
                    $this->hasLock = false;
                    $this->isLockedByOther = true;
                    $this->lock = [
                        'user_name' => $lock->user?->name ?? 'Ukendt',
                    ];
                }
            } else {
                $this->hasLock = false;
                $this->isLockedByOther = false;
            }
        }

        $this->selectOptions['sagsbehandler'] = [];

        if ($this->isEditMode) {
            $this->acquireLock();
        }

        if ($this->isEditMode) {
            $kreditor = $this->sag->sagerkreditor->first();
            if ($kreditor) {
                $this->hydrateFromKreditor($kreditor);
            }
        } else {
            $this->form->konsulent = null;
            $this->loadKonsulentOptions();
        }

        if ($this->isEditMode) {
            $this->refreshBadge();
        }

        $this->loadFormRelations();
        $this->loadSelectOptions();

        // 🟢 Beregn og formater alle tal ved opstart
        $this->calculateTotals();

        $this->databaseValuePostnr();

        $this->kreditorSuggestions = [];
        $this->showKreditorDropdown = false;
        $this->isSearchMode = $isSearchMode;

        if ($this->isEditMode) {
            SagActivity::updateOrCreate(
                [
                    'sag_id' => $this->sag->id,
                    'user_id' => auth()->id(),
                ],
                [
                    'last_viewed_at' => now(),
                    'heartbeat_at' => now(),
                    'is_editing' => true,
                ]
            );
        }

        if ($this->isEditMode) {
            $this->syncLockState();
        }

        $this->loading = false;
        $this->pendingRequests = collect();

        $this->loadMyTakeoverRequest();

        $myLock = SagLock::where('sag_id', $this->sag->id)
            ->where('user_id', auth()->id())
            ->first();

        if ($myLock?->currentsag_locked) {
            $this->currentsagLocked = true;
        }
    }

    

    public function parseNumber(string|float|int|null $value): float
    {
        if ($value === null) return 0;
        if (is_numeric($value)) return (float) $value;

        $normalized = str_replace('.', '', $value);
        $normalized = str_replace(',', '.', $normalized);

        return (float) $normalized;
    }

    public function formatNumber(mixed $value): string
    {
        if ($value === null || $value === '') return '0,00';

        if (is_string($value)) {
            $normalized = str_replace('.', '', $value);
            $normalized = str_replace(',', '.', $normalized);
            $number = (float) $normalized;
        } elseif (is_numeric($value)) {
            $number = (float) $value;
        } else {
            $number = 0;
        }

        return number_format($number, 2, ',', '.');
    }

    
    public function formatOnBlur(string $field): void
    {
        if (!property_exists($this->form, $field)) {
            return;
        }

        $value = $this->parseNumber($this->form->$field);
        $this->form->$field = $this->formatNumber($value);

        $this->calculateTotals();
    }

    public function updatedForm($value, $name): void
    {
        if (in_array($name, [
            'hovedstol',
            'renter',
            'gebyr',
            'indbetalt',
            'restgaeld_kreditor',
            'startgebyr',
            'n_mdlydelse',
        ])) {
            $this->calculateTotals();
        }
    }

    public function calculateTotals(): void
    {
        // Indtastede felter (A, B, C, F, G)
        $A_hovedstol = $this->parseNumber($this->form->hovedstol ?? 0);
        $B_renter    = $this->parseNumber($this->form->renter ?? 0);
        $C_gebyr     = $this->parseNumber($this->form->gebyr ?? 0);
        $F_indbetalt = $this->parseNumber($this->form->indbetalt ?? 0);
        $G_restKred  = $this->parseNumber($this->form->restgaeld_kreditor ?? 0);

        // Øvrige felter
        $startgebyr  = $this->parseNumber($this->form->startgebyr ?? 0);
        $n_mdlydelse  = $this->parseNumber($this->form->n_mdlydelse ?? 0);

        // 🟢 Formler jf. arket (D, E, H)
        $D_ialt          = $A_hovedstol + $B_renter + $C_gebyr;  // D = A + B + C
        $E_resterende    = $D_ialt - $F_indbetalt;               // E = D - F
        $H_restgaeldDkg  = $G_restKred + $C_gebyr;               // H = G + C

        // Gem formaterede tal i formularen
        $this->form->hovedstol          = $this->formatNumber($A_hovedstol);
        $this->form->renter             = $this->formatNumber($B_renter);
        $this->form->gebyr              = $this->formatNumber($C_gebyr);
        $this->form->indbetalt          = $this->formatNumber($F_indbetalt);
        $this->form->restgaeld_kreditor = $this->formatNumber($G_restKred);

        // Beregnede felter
        $this->form->ialt               = $this->formatNumber($D_ialt);
        $this->form->resterende         = $this->formatNumber($E_resterende);
        $this->form->restgaeld          = $this->formatNumber($E_resterende);
        $this->form->restgaeld_dkg      = $this->formatNumber($H_restgaeldDkg);

        // Øvrige felter
        $this->form->startgebyr         = $this->formatNumber($startgebyr);
        $this->form->n_mdlydelse        = $this->formatNumber($n_mdlydelse);
    }

    public function save()
    {
        try {
            // I stedet for kun $validated, henter vi alt data fra formen
            // Vi beholder valideringen, men bruger hele form-data til fill()
            $this->form->validate(); 
            $allData = $this->form->all(); 

            if (!($this->sag instanceof Sager)) {
                $this->sag = new Sager();
                $isNew = true;
            } else {
                $isNew = !$this->sag->exists;
            }

            // Brug $allData i stedet for $validated
            $this->sag->fill($allData);
            $this->sag->save();

            if ($isNew) {
                $token = Tokens::create([
                    'token' => bin2hex(random_bytes(16)),
                    'created_at' => now(),
                ]);
                $this->sag->sagertokens()->sync([$token->id]);
            }

            if ($this->sag && $this->sag->exists) {
                $pivotRelations = ['status', 'bemaerkning', 'ktr', 'afslutning', 'udlaeg', 'konsulent', 'sagsbehandler'];
                foreach ($pivotRelations as $relation) {
                    $this->form->updateRelation($relation, $this->sag->id);
                }

                $debitorId = $this->form->UpdateDebitor($this->sag);
                if ($debitorId) $this->sag->sagerdebitor()->sync([$debitorId]);

                $kreditorId = $this->form->UpdateKreditor($this->sag);
                if ($kreditorId) {
                    $this->sag->sagerkreditor()->sync([$kreditorId]);
                } else {
                    $this->sag->sagerkreditor()->detach();
                }

                $sagsbehandlerId = $this->form->UpdateSagsbehandler();
                if ($sagsbehandlerId) {
                    $this->sag->sagersagsbehandler()->sync([$sagsbehandlerId]);
                } else {
                    $this->sag->sagersagsbehandler()->detach();
                }
            }

            // 🟢 Marker at sagen er gemt (Viser grøn knap)
            $this->savedRecently = true;
            $this->js('setTimeout(() => { $wire.savedRecently = false; }, 2500)');

            // 🟢 SAMLET TOAST-LOGIK (Kun 1 samlet notifikation afsendes)
            if ($this->sag->wasChanged('afsluttet')) {
                if (!empty($this->form->afsluttet)) {
                    $this->dispatch('toast', 
                        message: 'Sagen er gemt og markeret som AFSLUTTET.', 
                        type: 'success', 
                        icon: 'check'
                    );
                } else {
                    $this->dispatch('toast', 
                        message: 'Afslutningsdatoen blev fjernet – sagen er nu aktiv igen.', 
                        type: 'warning', 
                        icon: 'warning'
                    );
                }
            } elseif (!empty($this->form->afsluttet)) {
                $this->dispatch('toast', 
                    message: 'Sagen er gemt (AFSLUTTET).', 
                    type: 'success', 
                    icon: 'check'
                );
            } else {
                $this->dispatch('toast', 
                    message: 'Sagen er gemt.', 
                    type: 'success', 
                    icon: 'check'
                );
            }

            SagActivity::where('sag_id', $this->sag->id)
                ->where('user_id', auth()->id())
                ->update([
                    'last_edited_at' => now(),
                ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('toast', message: 'Tjek dine inputfelter! Fejlbeskrivelse: '.$e->getMessage(), type: 'error', icon: 'error');
        } catch (\Exception $e) {
            \Log::error("Sag save error: " . $e->getMessage());
            $this->dispatch('toast', message: 'Der opstod en fejl ved gemning af sagen! Fejlbeskrivelse: '.$e->getMessage(), type: 'error', icon: 'error');
        }
    }

    public function render()
    {
        $currentsagLocked = SagLock::where('sag_id', $this->sag->id)
            ->where('user_id', auth()->id())
            ->whereNotNull('locked_at')
            ->exists();

        if ($this->showAcceptedModal && $this->acceptedModalUntil) {
            if (now()->timestamp >= $this->acceptedModalUntil) {
                $this->showAcceptedModal = false;
                $this->acceptedModalUntil = null;
            }
        }

        if ($this->sag?->exists && $this->sag->isEligibleForGdprDeletion()) {
            abort(403, 'Adgang nægtet: Sagen har overskredet GDPR 5-års grænsen og er låst for visning indtil anonymisering/sletning.');
        }

        return view('livewire.sager.sag-editor', [
            'currentsagLocked' => $currentsagLocked,
        ]);
    }

    protected function loadKonsulentOptions(): void
    {
        $allKonsulenter = Konsulenter::all()->filter(function ($k) {
            return !$k->isSkjult();
        });

        $this->selectOptions['konsulent'] = $allKonsulenter->pluck('navn', 'id')->toArray();

        if (!$this->sag || !$this->sag->exists) {
            $hoved = Konsulenter::all()->firstWhere(fn($k) => $k->isHoved());
            if ($hoved) {
                $this->form->konsulent = $hoved->id;
            }
        }
    }

    protected function hydrateFromKreditor(Kreditorer $kreditor): void
    {
        $this->form->kreditor = $kreditor->id;
        $this->form->kreditor_navn = $kreditor->navn;
        $this->form->kreditor_lotusID = $kreditor->lotusID;

        $this->selectOptions['sagsbehandler'] = Sagsbehandler::forKreditor($kreditor->id) ?? [];

        $saved = $this->sag?->sagersagsbehandler->first()?->id;

        $this->form->sagsbehandler =
            ($saved && isset($this->selectOptions['sagsbehandler'][$saved]))
                ? $saved
                : null;
    }

    protected function getSagsbehandlerOptions(int $kreditorId): array
    {
        return Cache::remember(
            "sagsbehandler.kreditor.$kreditorId",
            3600,
            fn () => Sagsbehandler::forKreditor($kreditorId) ?? []
        );
    }

    public function initTotals()
    {
        if ($this->totalsReady) return;

        $this->calculateTotals();
        $this->totalsReady = true;
    }

    
    public function placeholder(array $params = [])
    {
        return view('livewire.sager.loading_sag');
    }

    public function databaseValuePostnr(){
        $postnr = $this->form->postnr;
        
        if ($postnr === '') {
            $this->form->by = '';
            return;
        }

        $by = Cache::remember(
            "postnr:$postnr",
            86400,
            fn () => Postnr::where('postnr', $postnr)->value('by')
        );

        $this->form->by = $by ?? '';
    }
    
    public function updatedFormPostnr($value)
    {
        if ($value !== '' && $this->showByDropdown === false) {
            return;
        }

        $postnr = trim($value);

        if ($postnr === '') {
            $this->form->by = '';
            return;
        }

        $by = Cache::remember(
            "postnr:$postnr",
            86400,
            fn () => Postnr::where('postnr', $postnr)->value('by')
        );

        $this->form->by = $by ?? '';
    }

    public function updatedFormBy($value): void
    {
        $term = trim(mb_strtolower($value));

        $this->bySuggestions = [];
        $this->showByDropdown = false;

        if (mb_strlen($term) < 1) {
            return;
        }

        $results = $this->getPostnrIndex()
            ->map(function ($row) use ($term) {
                $row['score'] = $this->fuzzyScore($term, $row['by_lc']);
                return $row;
            })
            ->filter(fn ($row) => $row['score'] <= 25)
            ->sortBy('score')
            ->take(10)
            ->values();

        if ($results->isEmpty()) {
            return;
        }

        $this->bySuggestions = $results->toArray();
        $this->showByDropdown = true;
    }

    public function selectBy(string $by, string $postnr): void
    {
        $this->form->by = $by;
        $this->form->postnr = $postnr;

        $this->bySuggestions = [];
        $this->showByDropdown = false;
    }

    protected function getPostnrIndex(): Collection
    {
        return Cache::rememberForever('postnr.index', function () {
            return Postnr::select('by', 'postnr')
                ->get()
                ->map(fn ($r) => [
                    'by' => $r->by,
                    'by_lc' => mb_strtolower($r->by),
                    'postnr' => $r->postnr,
                ]);
        });
    }

    public function updatedFormKreditorNavn($value): void
    {
        $term = trim(mb_strtolower($value));

        $this->kreditorSuggestions = [];
        $this->showKreditorDropdown = false;

        if (mb_strlen($term) < 1) {
            return;
        }

        $results = $this->getKreditorIndex()
            ->map(function ($row) use ($term) {
                $row['score'] = $this->fuzzyScore($term, $row['navn_lc']);
                return $row;
            })
            ->filter(fn ($row) => $row['score'] <= 25)
            ->sortBy('score')
            ->take(10)
            ->values();

        if ($results->isEmpty()) {
            return;
        }

        if ($results->count() === 1 && $results->first()['score'] <= 3) {
            $k = $results->first();

            $this->form->kreditor_navn    = $k['navn'];
            $this->form->kreditor_lotusID = $k['lotusID'];
            $this->form->kreditor         = $k['id'];

            $this->updatedFormKreditorLotusID($k['lotusID']);
            return;
        }

        $this->kreditorSuggestions = $results->toArray();
        $this->kreditorHighlightIndex = 0;
        $this->showKreditorDropdown = true;
    }

    public function selectKreditor(int $id): void
    {
        $k = collect($this->getKreditorIndex())
            ->firstWhere('id', $id);

        if (!$k) {
            return;
        }

        $this->form->kreditor          = $k['id'];
        $this->form->kreditor_navn     = $k['navn'];
        $this->form->kreditor_lotusID  = $k['lotusID'];

        $this->kreditorSuggestions = [];
        $this->showKreditorDropdown = false;

        $this->updatedFormKreditorLotusID($k['lotusID']);
    }

    protected function getKreditorIndex(): Collection
    {
        return Cache::rememberForever('kreditor.index', function () {
            return Kreditorer::query()
                ->select('id', 'navn', 'lotusID')
                ->get()
                ->map(fn ($k) => [
                    'id'        => $k->id,
                    'navn'      => $k->navn,
                    'navn_lc'   => mb_strtolower($k->navn),
                    'lotusID'   => $k->lotusID,
                ]);
        });
    }

    public function handleKreditorKeydown(string $key): void
    {
        if (!$this->showKreditorDropdown || empty($this->kreditorSuggestions)) {
            return;
        }

        $max = count($this->kreditorSuggestions) - 1;

        match ($key) {
            'ArrowDown' => $this->kreditorHighlightIndex = min(
                $this->kreditorHighlightIndex + 1,
                $max
            ),

            'ArrowUp' => $this->kreditorHighlightIndex = max(
                $this->kreditorHighlightIndex - 1,
                0
            ),

            'Enter' => $this->selectKreditor(
                $this->kreditorSuggestions[$this->kreditorHighlightIndex]['id']
            ),

            'Escape' => $this->closeKreditorDropdown(),

            default => null,
        };
    }

    public function closeKreditorDropdown(): void
    {
        $this->showKreditorDropdown = false;
        $this->kreditorSuggestions = [];
        $this->kreditorHighlightIndex = 0;
    }

    protected function fuzzyScore(string $needle, string $haystack): int
    {
        if (str_starts_with($haystack, $needle)) {
            return 0;
        }

        if (str_contains($haystack, $needle)) {
            return 5;
        }

        $distance = levenshtein($needle, $haystack);

        return $distance < 10 ? 10 + $distance : 100;
    }

    public function getDokumenterCountPropertyOLD()
    {
        return $this->sag->dokumenter()->count();
    }

    public function getDokumenterCountProperty()
    {
        if(!$this->sag) return 0;

        return $this->sag->dokumenter()->count();
    }

    public function refreshBadge()
    {
        $dialog = Dialog::where('sag_id', $this->sag->id)
            ->where('type', 'klientinformation')
            ->first();

        if (!$dialog) {
            $this->klientinformationUnread = 0;
            return;
        }

        $this->klientinformationUnread = $dialog->unreadForUser(auth()->user());
    }

    public function releaseLock(): void
    {
        SagLock::where('sag_id', $this->sag->id)
            ->where('user_id', auth()->id())
            ->delete();

        $this->hasLock = false;
    }
    
    public function acquireLock(): void
    {
        if (!$this->sag?->id) return;

        $lock = SagLock::where('sag_id', $this->sag->id)->first();

        if (!$lock) {
            SagLock::create([
                'sag_id' => $this->sag->id,
                'user_id' => auth()->id(),
                'locked_at' => now(),
            ]);
        }

        $this->syncLockState();
    }

    public function heartbeat(): void
    {
        if (!$this->sag?->id) {
            return;
        }

        // 1. Opdatér låsens seneste aktivitet
        SagLock::where('sag_id', $this->sag->id)
            ->where('user_id', auth()->id())
            ->update([
                'locked_at' => now(),
            ]);

        // 2. Genindlæs låsens tilstand
        $lock = SagLock::where('sag_id', $this->sag->id)
            ->where('user_id', auth()->id())
            ->first();

        $this->currentsagLocked = (bool) $lock?->currentsag_locked;

        // 3. 🟢 HENT DE SENESTE OVERTAGELSES-ANMODNINGER
        $this->pendingRequests = SagEditRequest::where('sag_id', $this->sag->id)
            ->where('status', 'pending')
            ->with('requester')
            ->latest()
            ->get();

        // 4. 🟢 HVIS DER ER KOMMET NYE ANMODNINGER, ÅBN MODALEN AUTOMATISK
        if ($this->pendingRequests->isNotEmpty() && !$this->showTakeoverModal) {
            $this->showTakeoverModal = true;
        }

        $this->syncLockState();
        $this->loadMyTakeoverRequest();
    }

    public function rejectTakeover(int $requestId): void
    {
        SagEditRequest::where('id', $requestId)
            ->update(['status' => 'rejected']);
    }

    #[On('currentsagLockActivated')]
    public function activatecurrentsagLock(): void
    {
        SagLock::updateOrCreate(
            ['sag_id' => $this->sag->id, 'user_id' => auth()->id()],
            ['locked_at' => now()]
        );
    }

    #[On('verifycurrentsagUnlock')]
    public function verifycurrentsagUnlock(string $code): void
    {
        $hash = SystemSetting::get('global_unlock_code');

        if ($hash && Hash::check($code, $hash)) {
            SagLock::where('sag_id', $this->sagId)
                ->where('user_id', auth()->id())
                ->delete();

            $this->dispatch('currentsagUnlockSuccess');
        } else {
            $this->dispatch('currentsagUnlockFailed');
        }
    }

    public function lockcurrentsag(): void
    {
        SagLock::updateOrCreate(
            [
                'sag_id' => $this->sag->id,
                'user_id' => auth()->id(),
            ],
            [
                'locked_at' => now(),
                'currentsag_locked' => true,
            ]
        );

        $this->currentsagLocked = true;
    }

    public function unlockcurrentsag(): void
    {
        $hash = SystemSetting::get('global_unlock_code');

        if (!$hash || !Hash::check($this->unlockCode, $hash)) {
            $this->dispatch('toast',
                message: 'Forkert kode',
                type: 'error'
            );
            return;
        }

        SagLock::where('sag_id', $this->sag->id)
            ->where('user_id', auth()->id())
            ->update([
                'currentsag_locked' => false,
                'locked_at' => now(),
            ]);

        $this->currentsagLocked = false;
        $this->unlockCode = '';

        $this->dispatch('toast',
            message: 'Skærm låst op',
            type: 'success'
        );
    }

    public function acceptTakeover(int $requestId): void
    {
        $request = SagEditRequest::findOrFail($requestId);

        // 1. Slet eksisterende lås
        SagLock::where('sag_id', $this->sag->id)->delete();

        // 2. Opret ny lås til den bruger, der anmodede
        SagLock::create([
            'sag_id' => $this->sag->id,
            'user_id' => $request->requested_by,
            'locked_at' => now(),
        ]);

        // 3. Markér anmodningen som accepteret
        $request->update([
            'status' => 'accepted',
            'responded_at' => now(),
        ]);

        $this->showTakeoverModal = false;
        $this->syncLockState();

        $this->dispatch('toast', message: 'Overtagelse accepteret. Låsen er overdraget.', type: 'success');
    }

    public function getLockStateProperty(): string
    {
        if (!$this->sag?->id) return 'unlocked';

        $lock = SagLock::where('sag_id', $this->sag->id)->first();

        if (!$lock) return 'unlocked';

        return $lock->user_id === auth()->id()
            ? 'mine'
            : 'foreign';
    }

    public function getPendingRequestsProperty()
    {
        return SagEditRequest::where('sag_id', $this->sag->id)
            ->where('status', 'pending')
            ->with('requester')
            ->get();
    }

    public function loadMyTakeoverRequest(): void
    {
        if (!$this->sag?->id) {
            $this->myTakeoverRequest = null;
            return;
        }

        $this->myTakeoverRequest = SagEditRequest::where('sag_id', $this->sag->id)
            ->where('requested_by', auth()->id())
            ->latest()
            ->first();
    }

    public function requestTakeover(): void
    {
        if (! $this->sag?->id) {
            $this->dispatch('toast', message: 'Ingen aktiv sag fundet.', type: 'error');
            return;
        }

        // Tjek om brugeren allerede har en aktiv eller rejected anmodning
        $request = SagEditRequest::firstOrCreate(
            [
                'sag_id' => $this->sag->id,
                'requested_by' => auth()->id(),
            ],
            [
                'status' => 'pending',
            ]
        );

        // Hvis den var afvist eller behandlet tidligere, genåbn den som pending
        if ($request->status !== 'pending') {
            $request->update([
                'status' => 'pending',
                'created_at' => now(),
            ]);
        }

        $this->loadMyTakeoverRequest();
        $this->syncLockState();

        // 🟢 Giv øjeblikkelig visuel feedback til brugeren!
        $this->dispatch('toast', 
            message: 'Anmodning om overtagelse er sendt til ' . ($this->lock['user_name'] ?? 'brugeren') . '!', 
            type: 'success'
        );
    }

    public function continueAfterTakeoverAccepted(): void
    {
        if ($this->myTakeoverRequest?->status === 'accepted') {
            $this->myTakeoverRequest->delete();
            $this->myTakeoverRequest = null;
            $this->syncLockState();
            $this->redirect(request()->header('Referer'));
        }
    }

    public function getIsExpiringSoonProperty(): bool
    {
        if (!$this->sag?->exists) {
            return false;
        }

        return $this->sag->gdpr_status['code'] === 'warning';
    }

    /**
     * Tjekker i baggrunden om ANDRE brugere har anmodet om overtagelse
     */
    public function checkTakeoverRequests(): void
    {
        if (!$this->sag?->id) {
            return;
        }

        // Hent KUN anmodninger fra ANDRE brugere (frafiltrerer dig selv)
        $newRequests = SagEditRequest::where('sag_id', $this->sag->id)
            ->where('requested_by', '!=', auth()->id())
            ->where('status', 'pending')
            ->with('requester')
            ->latest()
            ->get();

        // Åbn modalen ELLER opdatér listen HVIS der er nye anmodninger
        if ($newRequests->isNotEmpty()) {
            $this->pendingRequests = $newRequests;
            if (!$this->showTakeoverModal) {
                $this->showTakeoverModal = true;
            }
        } else {
            // Nulstil hvis der ikke er nogen ventende
            $this->pendingRequests = collect();
        }

        // Tjek om din egen anmodning blev accepteret af den anden bruger
        if ($this->myTakeoverRequest?->status === 'pending') {
            $this->loadMyTakeoverRequest();
            if ($this->myTakeoverRequest?->status === 'accepted') {
                $this->syncLockState();
            }
        }
    }

    protected function syncLockState(): void
    {
        if (!$this->sag?->id) {
            $this->lockState = 'unlocked';
            $this->hasLock = false;
            $this->isLockedByOther = false;
            return;
        }

        $lock = SagLock::where('sag_id', $this->sag->id)->first();

        // 🟢 VIGTIGT: Hent KUN anmodninger fra ANDRE brugere!
        $this->pendingRequests = SagEditRequest::where('sag_id', $this->sag->id)
            ->where('requested_by', '!=', auth()->id())
            ->where('status', 'pending')
            ->latest()
            ->get();

        if (!$lock) {
            $this->lockState = 'unlocked';
            $this->lock = null;
            return;
        }

        if ($lock->user_id === auth()->id()) {
            $this->lockState = 'mine';
            $this->hasLock = true;
            $this->isLockedByOther = false;
            $this->lock = null;
            return;
        }

        $this->lockState = 'foreign';
        $this->hasLock = false;
        $this->isLockedByOther = true;

        $this->lock = [
            'user_name' => $lock->user?->name ?? 'Ukendt',
        ];
    }

    /**
     * Hjælpemetode fra modalen: Sætter dags dato i Afsluttet-feltet
     */
    public function applyTodayAfsluttetDate(): void
    {
        $this->form->afsluttet = now()->format('Y-m-d');
        $this->showAfsluttetDateReminder = false;

        $this->dispatch('toast', 
            message: 'Afslutningsdato sat til i dag. Husk at gemme sagen.', 
            type: 'info'
        );
    }

    /**
     * Reagerer i realtid når afslutningsårsag vælges
     */
    public function updatedFormAfslutning($value): void
    {
        if (!empty($value) && empty($this->form->afsluttet)) {
            $this->reminderType = 'date';
            $this->showAfsluttetDateReminder = true;
        }
    }

    /**
     * Reagerer i realtid når afslutningsdato vælges
     */
    public function updatedFormAfsluttet($value): void
    {
        if (!empty($value) && empty($this->form->afslutning)) {
            $this->reminderType = 'reason';
            $this->showAfsluttetDateReminder = true;
        }
    }
    
}