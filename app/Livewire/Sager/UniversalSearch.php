<?php

namespace App\Livewire\Sager;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Sager;
use App\Models\Kreditorer;
use App\Models\Status;
use App\Models\afslutning;
use App\Models\SavedSearch;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SagerExport;
use App\Services\Search\SagerSearchService;
use App\Models\Konsulenter;

class UniversalSearch extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'tailwind';

    public bool $isNewSearch = false;

    public string $lastAutoSearchName = '';

    public bool $hasActiveSearch = false;
    
    public bool $autoSearch = true;

    public bool $showExportModal = false;

    public int $searchVersion = 0;
    public bool $loadingResults = false;
    public ?int $selectedSavedSearch = null;

    public bool $showSearchNameInput = false;
    
    public bool $hasActiveFilters = false;

    public bool $hasResults = false;
    
    public $konsulenter = [];

    public bool $showResults = false;

    public bool $showFilters = true;

    public string $activeFilterTab = 'status';

    public bool $isBuildingSearch = true;
    public bool $showSavedSearches = true;
    public bool $showResultSummary = true;

    public bool $hasActiveQuery = false;
    
    public array $searchSummary = [];

    public function openResults()
    {
        $this->showResults = true;
    }

    public array $filters = [
        'search' => '',
        'sagsnr' => '',
        'stelnr' => '',
        'kreditor_id' => null,
        'debitor' => '',
        'sagsbehandler_id' => null,
        'status_ids' => [],
        'afslutning_id' => null,
        'betalt' => null,
        'restgaeld_min' => null,
        'restgaeld_max' => null,
        'status' => 'all',
        'modtaget_from' => null,
        'modtaget_to' => null,
        'afsluttet_from' => null,
        'afsluttet_to' => null,
    ];

    public string $searchName = '';
    
    public $kreditorer = [];
    public $statuses = [];
    public $afslutninger = [];
    public $status = [];

    public string $sortField = 'modtaget';
    public string $sortDirection = 'desc';

    public function mount(): void
    {
        $this->kreditorer = Kreditorer::orderBy('navn')->get();
        $this->statuses = Status::orderBy('tekst')->get();
        $this->afslutninger = Afslutning::orderBy('tekst')->get();

        $this->konsulenter = Konsulenter::orderBy('navn')->get();
    }

    /**
     * SINGLE SOURCE OF TRUTH
     */
    protected function baseQuery()
    {
        return app(SagerSearchService::class)
            ->query($this->filters)
            ->orderBy(
                $this->sortField,
                $this->sortDirection
            );
    }

    public function getResultsProperty()
    {
        return $this->baseQuery()->paginate(50);
    }

    public function getTotalProperty()
    {
        return $this->baseQuery()->count();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
            return;
        }

        $this->sortField = $field;
        $this->sortDirection = 'asc';
    }

    public function saveSearch(): void
    {
        $this->validate([
            'searchName' => 'required|string|max:120',
        ]);

        $filters = $this->normalizeFilters($this->filters);

        $saved = SavedSearch::create([
            'name' => $this->searchName,
            'filters' => $filters,
            'user_id' => auth()->id(),
        ]);

        $this->savedSearches = SavedSearch::where('user_id', auth()->id())
            ->latest()
            ->get();

        $this->selectedSavedSearch = $saved->id;

        $this->showSavedSearches = true;

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Søgning gemt',
        ]);
    }

    public function loadSearch($id): void
    {
        $saved = SavedSearch::findOrFail($id);

        $this->filters = $this->normalizeAllFilters($saved->filters ?? []);

        $this->selectedSavedSearch = $id;

        $this->hasActiveSearch = true; // ✅ CRITICAL FIX

        $this->showResultSummary = true;

        $this->searchVersion++;

        $this->searchSummary = $this->getSearchSummaryProperty();

        $this->dispatch('toast', [
            'type' => 'info',
            'message' => 'Søgning indlæst',
        ]);
    }

    public function deleteSearch(int $id): void
    {
        SavedSearch::where('user_id', auth()->id())
            ->where('id', $id)
            ->delete();
    }

    public function resetFilters(): void
    {
        $this->filters = $this->normalizeAllFilters([
            'status_ids' => $this->statuses->pluck('id')->toArray(),
        ]);

        $this->selectedSavedSearch = null;
        $this->hasActiveFilters = false;
        $this->hasResults = false;

        $this->hasActiveSearch = false; // ✅ RESET UI STATE

        $this->showSearchNameInput = false;
        $this->searchName = '';

        $this->searchVersion++;
        $this->resetPage();
    }

    private function buildExportFilename(): string
    {
        $sagsnr = $this->filters['sagsnr'] ?? null;
        $search = $this->filters['search'] ?? null;

        $base = 'sager';

        if ($sagsnr) {
            $base .= '_sagsnr-' . preg_replace('/[^a-zA-Z0-9\-]/', '', $sagsnr);
        } elseif ($search) {
            $base .= '_search-' . preg_replace('/[^a-zA-Z0-9\-]/', '', $search);
        }

        return $base . '_' . now()->format('Y-m-d_His') . '.xlsx';
    }
    
    public function exportExcel()
    {
        $columns = array_values(array_filter($this->selectedColumns));
        $columns = array_unique($columns);

        return Excel::download(
            new SagerExport(
                $this->filters,
                $this->sortField,
                $this->sortDirection,
                $columns
            ),
            $this->buildExportFilename()
        );
    }

    /**
     * Available export columns (whitelist)
     */
    public array $availableColumns = [
        'sagsnr' => 'Sagsnr',
        'modtaget' => 'Oprettelsesdato',
        'afsluttet' => 'Lukkedato',
        'debitor' => 'Debitor',
        'kreditor' => 'Kreditor',
        'status' => 'Status',
        'afslutning' => 'Afslutning',
        'sagsbehandler' => 'Sagsbehandler',
        'konsulent' => 'Konsulent',
    ];

    /**
     * Default selected export columns
     */
    public array $selectedColumns = [
        'sagsnr',
        'modtaget',
        'debitor',
        'kreditor',
        'status',
    ];

    public function openExportModal(): void
    {
        $this->showExportModal = true;
    }

    public function closeExportModal(): void
    {
        $this->showExportModal = false;
    }

    private function filtersHaveValues(): bool
    {
        foreach ($this->filters as $key => $value) {
            if (is_array($value)) {
                if (!empty($value)) {
                    return true;
                }
            } elseif (!empty($value)) {
                return true;
            }
        }

        return false;
    }

    public function updatedFilters()
    {
        $this->searchSummary = $this->getSearchSummaryProperty();

        $this->hasActiveSearch = $this->filtersHaveValues();
        
        $this->searchName = $this->autoSearchName;
    }
    
    public function toggleFilters(): void
    {
        $this->showFilters = ! $this->showFilters;
    }

    public function showResults(): void
    {
        $this->showResults = true;
    }

    public function closeResults(): void
    {
        $this->showResults = false;
    }
    
    public function newSearch(): void
    {
        $this->resetFilters();
        $this->selectedSavedSearch = null;

        $this->hasActiveSearch = true; // ✅ THIS WAS MISSING

        $this->showSearchNameInput = true;

        $this->showSavedSearches = false;
    }

    public function cancelNewSearch(): void
    {
        $this->isNewSearch = false;
    }

    public function setTab(string $tab): void
    {
        $this->activeFilterTab = $tab;
    }
    
    private function normalizeFilters(array $filters): array
    {
        return collect($filters)
            ->filter(fn ($value) => $value !== null && $value !== '')
            ->toArray();
    }
    
    public function getCanSaveProperty(): bool
    {
        return !empty($this->searchName) && !empty($this->filters);
    }

    public function getSearchSummaryProperty(): array
    {
        $f = $this->normalizeAllFilters($this->filters);
        
        return [
            'status' => $f['status'],

            'statuses' => $this->resolveStatusNames($f['status_ids']),

            // IMPORTANT: this was previously inconsistent (array vs null vs string)
            'afslutning' => $this->resolveAfslutningNames(
                is_array($f['afslutning_id'])
                    ? $f['afslutning_id']
                    : ($f['afslutning_id'] ? [$f['afslutning_id']] : [])
            ),

            'kreditor' => $this->resolveKreditorName($f['kreditor_id']),

            'debitor' => $f['debitor'],
            'sagsnr' => $f['sagsnr'],
            'stelnr' => $f['stelnr'],

            'date_range' => [
                'from' => $f['modtaget_from'],
                'to' => $f['modtaget_to'],
            ],

            'oekonomisk_status' => $f['oekonomisk_status'] ?? null,
        ];
    }

    private function resolveStatusNames($ids): array
    {
        if (!is_array($ids) || empty($ids)) return [];

        return Status::whereIn('id', $ids)->pluck('tekst')->toArray();
    }

    private function resolveAfslutningNames($ids): array
    {
        if (empty($ids)) return [];

        $ids = is_array($ids) ? $ids : [$ids];

        return Afslutning::whereIn('id', $ids)
            ->pluck('tekst')
            ->toArray();
    }

    private function resolveKreditorName($id): ?string
    {
        return $id ? Kreditorer::find($id)?->navn : null;
    }

    private function normalizeAllFilters(array $filters): array
    {
        return [
            'search' => (string) ($filters['search'] ?? ''),
            'sagsnr' => (string) ($filters['sagsnr'] ?? ''),
            'stelnr' => (string) ($filters['stelnr'] ?? ''),

            'kreditor_id' => $filters['kreditor_id'] ?? null,
            'debitor' => (string) ($filters['debitor'] ?? ''),
            'sagsbehandler_id' => $filters['sagsbehandler_id'] ?? null,

            // ALWAYS ARRAY
            'status_ids' => is_array($filters['status_ids'] ?? null)
                ? array_values($filters['status_ids'])
                : [],

            // IMPORTANT FIX (this was your bug source)
            'afslutning_id' => is_array($filters['afslutning_id'] ?? null)
                ? array_values($filters['afslutning_id'])
                : ($filters['afslutning_id'] ?? null),

            'betalt' => $filters['betalt'] ?? null,
            'restgaeld_min' => $filters['restgaeld_min'] ?? null,
            'restgaeld_max' => $filters['restgaeld_max'] ?? null,

            'status' => $filters['status'] ?? 'all',

            'modtaget_from' => $filters['modtaget_from'] ?? null,
            'modtaget_to' => $filters['modtaget_to'] ?? null,
            'afsluttet_from' => $filters['afsluttet_from'] ?? null,
            'afsluttet_to' => $filters['afsluttet_to'] ?? null,
        ];
    }

    public function getAutoSearchNameProperty(): string
    {
        $s = $this->searchSummary;

        $parts = [];

        // Status
        $parts[] = match ($s['status'] ?? 'all') {

            'active' => 'Aktive',

            'closed' => 'Lukkede',

            default => 'Alle',

        };

        // Kreditor (truncate long names)
        if (!empty($s['kreditor'])) {
            $parts[] = \Illuminate\Support\Str::limit($s['kreditor'], 18, '…');
        }

        // Status
        if (!empty($s['statuses'])) {

            $count = count($s['statuses']);

            if ($count === 1) {
                $parts[] = \Illuminate\Support\Str::limit($s['statuses'][0], 15, '…');
            } else {
                $parts[] = "{$count} statuser";
            }
        }

        // Afslutning
        if (!empty($s['afslutning'])) {

            $values = is_array($s['afslutning'])
                ? $s['afslutning']
                : [$s['afslutning']];

            $parts[] = count($values) === 1
                ? \Illuminate\Support\Str::limit($values[0], 15, '…')
                : count($values) . ' afsl.';
        }

        if (!empty($s['debitor'])) {
            $parts[] = 'Debitor';
        }

        if (!empty($s['sagsnr'])) {
            $parts[] = 'Sag ' . $s['sagsnr'];
        }

        if (!empty($s['stelnr'])) {
            $parts[] = 'Stel';
        }

        return implode(' • ', array_filter($parts));
    }

    public function render()
    {
        return view('livewire.sager.universal-search', [
            'results' => $this->results,
            'total' => $this->total,
            'savedSearches' => SavedSearch::where('user_id', auth()->id())->get(),
        ]);
    }
}