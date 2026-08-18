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

class KreditorSagEditor extends Component
{
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

    public array $fieldLabels = [

    'sagsnr' => 'Sagsnummer',

    'navn' => 'Debitor navn',
    'adresse' => 'Adresse',
    'postnr' => 'Postnr',
    'by' => 'By',

    'hovedstol' => 'Hovedstol',
    'renter' => 'Renter',
    'gebyr' => 'Gebyr',
    'indbetalt' => 'Indbetalt',

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
            // Standard fallback-felter hvis databasen ikke har konfigureret nogen endnu
            $this->allowedFields = [
                'sagsnr',
                'navn',
                'adresse',
                'postnr',
                'by',
                'hovedstol',
                'renter',
                'gebyr',
                'indbetalt'
            ];
        }

        $user = Auth::user();

        if ($sag) {
            $this->sag = Sager::findOrFail($sag);
            $this->isEditMode = true;
        } else {
            $this->sag = new Sager();
        }

        // Attach form model
        $this->form->sag = $this->sag;
        $this->form->sag_id = $this->sag->id;

        // Find kreditor belonging to user
        $kreditor = $user->kreditorer()->firstOrFail();
        $this->form->kreditor = $kreditor->id;

        // Load sagsbehandlere belonging to this kreditor
        $this->sagsbehandlerOptions =
            Sagsbehandler::forKreditor($kreditor->id) ?? [];
    }

    /**
     * Normalize a string/float/int into a float
     */
    public function parseNumber(string|float|int|null $value): float
    {
        if ($value === null) return 0;

        if (is_numeric($value)) return (float) $value;

        // Remove thousands separator and convert comma to dot
        $normalized = str_replace('.', '', $value);
        $normalized = str_replace(',', '.', $normalized);

        return (float) $normalized;
    }

    /**
     * Format a number into Danish style (2 decimals, comma)
     */
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
    public function saveOLD()
    {
        $this->validate([
            'form.sagsnr' => 'required',
            'form.navn' => 'required',
            'form.sagsbehandler' => 'required',
            'form.aktiv' => 'required',
        ]);

        $this->showConfirmModal = true;
    }

    public function save()
    {
        try {
            $this->form->validate();

            foreach (['hovedstol', 'renter', 'gebyr', 'indbetalt'] as $field) {
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
            // 🔒 Validerer formularen igen
            $this->form->validate();

            $data = $this->sanitizeFormData();

            // 🟢 Sæt automatisk 'modtaget' til dags dato/tidstempel ved indsendelse
            $data['modtaget'] = now();

            $this->sag = Sager::create($data);

            // 5️⃣ Opdater pivot-relationer (Kreditor, Sagsbehandler, Debitor osv.)
            if ($this->sag->exists) {

                // Kreditor pivot
                $kreditorId = $this->form->UpdateKreditor($this->sag);
                if ($kreditorId) {
                    $this->sag->sagerkreditor()->sync([$kreditorId]);
                } else {
                    $this->sag->sagerkreditor()->detach();
                }

                // Sagsbehandler pivot
                $sagsbehandlerId = $this->form->UpdateSagsbehandler();
                if ($sagsbehandlerId) {
                    $this->sag->sagersagsbehandler()->sync([$sagsbehandlerId]);
                } else {
                    $this->sag->sagersagsbehandler()->detach();
                }

                // Debitor pivot
                $debitorId = $this->form->UpdateDebitor($this->sag);
                if ($debitorId) {
                    $this->sag->sagerdebitor()->sync([$debitorId]);
                } else {
                    $this->sag->sagerdebitor()->detach();
                }

                // Øvrige pivot-relationer
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
        } 
        catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('toast',
                message: 'Validering fejlede ved indsendelse!',
                type: 'error'
            );
        } 
        catch (\Exception $e) {
            \Log::error("Sag confirm error: " . $e->getMessage());

            $this->dispatch('toast',
                message: 'Fejl ved indsendelse af sag',
                type: 'error'
            );
        }
    }

    protected function sanitizeFormData(): array
    {
        $data = $this->form->toArray();

        $data['hovedstol'] = $this->normalizeDanishNumber($data['hovedstol'] ?? null);
        $data['renter'] = $this->normalizeDanishNumber($data['renter'] ?? null);

        // Hent de dynamiske felter
        $allowed = collect($data)
            ->only($this->allowedFields)
            ->map(fn ($value) => $value === '' ? null : $value)
            ->toArray();

        // 🟢 Sikr at 'modtaget' altid inkluderes i arrayet
        if (isset($data['modtaget'])) {
            $allowed['modtaget'] = $data['modtaget'];
        }

        return $allowed;
    }

    function dk_number($value)
    {
        if ($value === null) {
            return null;
        }

        return number_format((float)$value, 2, ',', '.');
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
        $this->reset([
            'form',
            'sag',
            'reviewMode',
            'readyForReview',
            'sentSuccessfully',
            'showSuccessModal'
        ]);

        $this->mount(); // re-init everything
    }

    public function updatedFormPostnr($value)
    {
        if (!$value) {
            $this->form->by = null;
            return;
        }

        $postnummer = \App\Models\Postnr::where('postnr', $value)->first();

        $this->form->by = $postnummer?->by;
    }
    
    public function updatedFormHovedstol($value)
    {
        $this->form->hovedstol = $this->formatNumber(
            $this->parseNumber($value)
        );
    }

    public function updatedFormRenter($value)
    {
        $this->form->renter = $this->formatNumber(
            $this->parseNumber($value)
        );
    }

    public function updatedFormGebyr($value)
    {
        $this->form->gebyr = $this->formatNumber(
            $this->parseNumber($value)
        );
    }

    public function updatedFormIndbetalt($value)
    {
        $this->form->indbetalt = $this->formatNumber(
            $this->parseNumber($value)
        );
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

    public function updatedFormBy($value): void
    {
        $term = trim(mb_strtolower($value));

        $this->bySuggestions = [];
        $this->showByDropdown = false;

        if (mb_strlen($term) < 1) {
            $this->form->postnr = ''; // Nulstil postnr hvis feltet tømmes
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

        // 🟢 NYT: Hvis der kun er 1 resultat, eller hvis top-resultatet er et 100% match
        if ($results->count() === 1 || $results->first()['by_lc'] === $term) {
            $match = $results->first();
            $this->form->postnr = $match['postnr'];
            $this->form->by = $match['by']; // Sørg for pæn casing (f.eks. "Sønderborg" i stedet for "sønderborg")
            return;
        }

        // Ellers vis dropdown med forslag som du plejer
        $this->bySuggestions = $results->toArray();
        $this->showByDropdown = true;
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
    
    public function selectBy(string $by, string $postnr): void
    {
        $this->form->by = $by;
        $this->form->postnr = $postnr;

        $this->bySuggestions = [];
        $this->showByDropdown = false;
    }
    
    public function render()
    {
        return view('livewire.sager.kreditor-sag-editor');
    }
}