<?php

namespace App\Livewire\Sager;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;
use Livewire\Component;
use App\Models\Sager;
use App\Models\Sagsbehandler;
use App\Models\Postnr;
use Illuminate\Support\Facades\Auth;
use App\Livewire\Forms\SagForm;
use App\Models\SagFieldSetting;
use App\Models\Ktr;
use App\Models\Udlaeg;
use Livewire\WithFileUploads;

class KreditorSagEditor extends Component
{
    use WithFileUploads;
    
    public $newDokument;
    
    public ?Sager $sag = null;
    public SagForm $form;

    public bool $isEditMode = false;

    public bool $showConfirmModal = false;
    public bool $savedSuccessfully = false;
    public bool $reviewMode = false;

    public array $sagsbehandlerOptions = [];
    public bool $readyForReview = false;

    public bool $sentSuccessfully = false;
    public bool $showSuccessModal = false;

    public array $selectOptions = [];

    public array $fieldLabels = [
        'sagsnr'      => 'Sagsnummer',
        'navn'        => 'Debitor navn',
        'co'          => 'c/o',
        'adresse'     => 'Adresse',
        'postnr'      => 'Postnr',
        'by'          => 'By',
        'tlf'     => 'tlf',
        'stelnr'      => 'Stelnummer',
        'ktr'         => 'KTR',
        'udlaeg'      => 'Udlæg',
        'hovedstol'   => 'Hovedstol',
        'renter'      => 'Renter',
        'gebyr'       => 'Gebyr',
        'n_mdlydelse' => 'Normal ydelse',
        'indbetalt'   => 'Indbetalt',
        'aktiv'       => 'Aktiv',         
        'cvr'         => 'CVR',
    ];

    protected $listeners = ['closeModal' => 'closeModal'];

    public array $bySuggestions = [];
    public bool $showByDropdown = false;

    public function closeModal()
    {
        $this->showSuccessModal = false;
        $this->dispatch('stopRedirectTimer');
    }

    public array $allowedFields = [];

    public function mount($sag = null)
    {
        $settings = SagFieldSetting::first();

        if ($settings && !empty($settings->allowed_fields)) {
            $this->allowedFields = $settings->allowed_fields;
        } else {
            // Standard fallback-felter med små bogstaver, så de matcher fieldLabels
            $this->allowedFields = [
                'sagsnr',
                'navn',
                'co',
                'adresse',
                'postnr',
                'by',
                'tlf',
                'stelnr',
                'ktr',
                'udlaeg',
                'hovedstol',
                'renter',
                'gebyr',
                'n_mdlydelse',
                'indbetalt',
                'aktiv',
                'cvr'
            ];
        }

        $user = Auth::user();

        if ($sag) {
            $this->sag = Sager::findOrFail($sag);
            $this->isEditMode = true;
        } else {
            $this->sag = new Sager();
        }

        $this->form->sag = $this->sag;
        $this->form->sag_id = $this->sag->id;

        $kreditor = $user->kreditorer()->firstOrFail();
        $this->form->kreditor = $kreditor->id;

        $this->sagsbehandlerOptions =
            Sagsbehandler::forKreditor($kreditor->id) ?? [];

        $this->loadSelectOptions();
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
    
    public function save()
    {
        try {
            $this->form->validate();

            foreach (['hovedstol', 'renter', 'gebyr', 'indbetalt', 'n_mdlydelse'] as $field) {
                if (property_exists($this->form, $field)) {
                    $this->form->$field = $this->formatNumber(
                        $this->parseNumber($this->form->$field)
                    );
                }
            }

            $this->readyForReview = true;
            $this->dispatch('focus-review');

        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('toast',
                message: 'Tjek dine inputfelter!',
                type: 'error'
            );
        }
    }

    public function confirmSave()
    {
        try {
            $this->form->validate();
            $data = $this->sanitizeFormData();
            $data['modtaget'] = now();

            // 1. Opret sagen først, så vi har et sag->id
            $this->sag = Sager::create($data);

            if ($this->sag->exists) {
                // 2. Hvis der er valgt et dokument, så brug din upload-logik her!
                if ($this->newDokument) {
                    $file = $this->newDokument;
                    $path = $file->store('dokumenter/' . $this->sag->id, 'public');

                    \App\Models\Dokument::create([
                        'sag_id'        => $this->sag->id,
                        'file_name'     => $file->getClientOriginalName(),
                        'file_path'     => $path,
                        'file_size'     => $file->getSize(),
                        'uploaded_date' => now(),
                    ]);
                }

                // Kreditor, Sagsbehandler, Debitor og øvrige relationer...
                $kreditorId = $this->form->UpdateKreditor($this->sag);
                if ($kreditorId) {
                    $this->sag->kreditor()->sync([$kreditorId]);
                } else {
                    $this->sag->kreditor()->detach();
                }

                $sagsbehandlerId = $this->form->UpdateSagsbehandler();
                if ($sagsbehandlerId) {
                    $this->sag->sagsbehandler()->sync([$sagsbehandlerId]);
                } else {
                    $this->sag->sagsbehandler()->detach();
                }

                $debitorId = $this->form->UpdateDebitor($this->sag);
                if ($debitorId) {
                    $this->sag->debitor()->sync([$debitorId]);
                } else {
                    $this->sag->debitor()->detach();
                }

                foreach (['status', 'bemaerkning', 'ktr', 'afslutning', 'udlaeg', 'konsulent'] as $relation) {
                    $this->form->updateRelation($relation, $this->sag->id);
                }
            }

            $this->reviewMode = false;
            $this->sentSuccessfully = true;
            $this->showSuccessModal = true;
            $this->dispatch('startRedirectTimer');

            $this->dispatch('toast',
                message: 'Din sag er nu sendt til DKG',
                type: 'success'
            );

        } catch (\Exception $e) {
            \Log::error("Sag confirm error: " . $e->getMessage());
            $this->dispatch('toast', message: 'Fejl ved indsendelse af sag', type: 'error');
        }
    }

    protected function sanitizeFormData(): array
    {
        $data = $this->form->toArray();

        $data['hovedstol'] = $this->normalizeDanishNumber($data['hovedstol'] ?? null);
        $data['renter'] = $this->normalizeDanishNumber($data['renter'] ?? null);
        $data['n_mdlydelse'] = $this->normalizeDanishNumber($data['n_mdlydelse'] ?? null);

        $allowed = collect($data)
            ->only($this->allowedFields)
            ->map(fn ($value) => $value === '' ? null : $value)
            ->toArray();

        if (isset($data['modtaget'])) {
            $allowed['modtaget'] = $data['modtaget'];
        }

        return $allowed;
    }

    protected function normalizeDanishNumber($value)
    {
        if (!$value) return null;
        $value = str_replace('.', '', $value);
        $value = str_replace(',', '.', $value);
        return $value;
    }

    public function goToReview()
    {
        $this->reviewMode = true;
        $this->dispatch('focus-review');
    }

    public function editAgain()
    {
        $this->reviewMode = false;
        $this->dispatch('focus-form');
    }

    public function resetForm()
    {
        $this->reset(['form', 'sag', 'reviewMode', 'readyForReview', 'sentSuccessfully', 'showSuccessModal']);
        $this->mount();
    }

    public function updatedFormPostnr($value)
    {
        if (!$value) {
            $this->form->by = null;
            return;
        }
        $postnummer = Postnr::where('postnr', $value)->first();
        $this->form->by = $postnummer?->by;
    }
    
    public function updatedFormHovedstol($value) { $this->form->hovedstol = $this->formatNumber($this->parseNumber($value)); }
    public function updatedFormRenter($value) { $this->form->renter = $this->formatNumber($this->parseNumber($value)); }
    public function updatedFormGebyr($value) { $this->form->gebyr = $this->formatNumber($this->parseNumber($value)); }
    public function updatedFormIndbetalt($value) { $this->form->indbetalt = $this->formatNumber($this->parseNumber($value)); }
    public function updatedFormNMdlydelse($value) { $this->form->n_mdlydelse = $this->formatNumber($this->parseNumber($value)); }

    protected function fuzzyScore(string $needle, string $haystack): int
    {
        if (str_starts_with($haystack, $needle)) return 0;
        if (str_contains($haystack, $needle)) return 5;
        $distance = levenshtein($needle, $haystack);
        return $distance < 10 ? 10 + $distance : 100;
    }

    public function updatedFormBy($value): void
    {
        $term = trim(mb_strtolower($value));
        $this->bySuggestions = [];
        $this->showByDropdown = false;

        if (mb_strlen($term) < 1) {
            $this->form->postnr = '';
            return;
        }

        $results = Cache::rememberForever('postnr.index', function () {
            return Postnr::select('by', 'postnr')->get()->map(fn ($r) => [
                'by' => $r->by,
                'by_lc' => mb_strtolower($r->by),
                'postnr' => $r->postnr,
            ]);
        })->map(function ($row) use ($term) {
            $row['score'] = $this->fuzzyScore($term, $row['by_lc']);
            return $row;
        })->filter(fn ($row) => $row['score'] <= 25)->sortBy('score')->take(10)->values();

        if ($results->isEmpty()) return;

        if ($results->count() === 1 || $results->first()['by_lc'] === $term) {
            $match = $results->first();
            $this->form->postnr = $match['postnr'];
            $this->form->by = $match['by'];
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

    protected function loadSelectOptions(): void
    {
        $this->selectOptions['ktr'] = Cache::rememberForever('select.ktr', fn () => Ktr::pluck('tekst', 'id')->toArray());
        $this->selectOptions['udlaeg'] = Cache::rememberForever('select.udlaeg', fn () => Udlaeg::pluck('tekst', 'id')->toArray());
    }

    public function render()
    {
        return view('livewire.sager.kreditor-sag-editor');
    }
}