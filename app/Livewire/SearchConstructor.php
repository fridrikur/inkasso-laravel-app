<?php

namespace App\Livewire;

use App\Models\Kreditorer;
use App\Models\Postnr;
use App\Models\SavedSearch;
use App\Models\Sager;
use App\Models\Status;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class SearchConstructor extends Component
{
    use WithPagination;
    public string $searchName = '';
    public bool $isSearchMode = true;
    public bool $showKreditorDropdown = false;
    

    #[Url]
    public ?int $saved = null;
    public ?int $selectedSavedSearch = null;

    public array $filters = [
        'sagsnr' => '',
        'kreditor_id' => null,
        'debitor_navn' => '',
        'status_id' => null,
        'postnr' => '',
    ];

    public bool $hasPreviewed = false;
    public int $previewCount = 0;
    public string $sqlPreview = '';
    public bool $showSqlPreview = false;

    public function mount(): void
    {
        if ($this->saved) {
            $this->loadSearch($this->saved, false);
        }
    }

    public function rules(): array
    {
        return [
            'searchName' => ['required', 'string', 'max:255'],
            'filters.sagsnr' => ['required', 'string'],
            'filters.kreditor_id' => ['required', 'integer'],
            'filters.debitor_navn' => ['nullable', 'string'],
            'filters.status_id' => ['nullable', 'integer'],
            'filters.postnr' => ['nullable', 'string'],
        ];
    }

    protected function buildQuery()
    {
        return Sager::query()
            ->with([
                'sagerkreditor',
                'sagerdebitor',
                'sagerStatus',
            ])
            ->when($this->filters['sagsnr'], function ($query) {
                $query->where('sagsnr', 'like', '%' . $this->filters['sagsnr'] . '%');
            })
            ->whereHas('sagerkreditor', function ($query) {
                $query->where('kreditors.id', $this->filters['kreditor_id']);
            })
            ->when($this->filters['debitor_navn'], function ($query) {
                $query->whereHas('sagerdebitor', function ($q) {
                    $q->where('navn', 'like', '%' . $this->filters['debitor_navn'] . '%');
                });
            })
            ->when($this->filters['status_id'], function ($query) {
                $query->whereHas('sagerStatus', function ($q) {
                    $q->where('status.id', $this->filters['status_id']);
                });
            })
            ->when($this->filters['postnr'], function ($query) {
                $query->whereHas('sagerdebitor', function ($q) {
                    $q->where('postnr', $this->filters['postnr']);
                });
            });
    }

    public function saveSearch(): void
    {
        $this->validate();

        if ($this->previewCount < 1) {
            $this->addError('searchName', 'Søgningen skal returnere mindst ét resultat.');
            return;
        }

        $saved = SavedSearch::create([
            'user_id' => auth()->id(),
            'name' => $this->searchName,
            'filters' => $this->filters,
        ]);

        $this->selectedSavedSearch = $saved->id;
        session()->flash('success', 'Søgningen blev gemt.');
        $this->searchName = '';
    }

    public function loadSearch(int $id, bool $redirect = true)
    {
        if ($redirect) {
            return redirect()->route('search-constructor', ['saved' => $id]);
        }

        $search = SavedSearch::where('user_id', auth()->id())
            ->findOrFail($id);

        $this->searchName = $search->name;
        $this->selectedSavedSearch = $search->id;
        $this->filters = array_merge($this->filters, $search->filters);

        $this->refreshPreview();
    }

    public function deleteSearch(int $id): void
    {
        SavedSearch::where('user_id', auth()->id())
            ->where('id', $id)
            ->delete();
    }

    public function updatedFilters(): void
    {
        $this->resetPage();
        $this->refreshPreview();
    }

    protected function refreshPreview(): void
    {
        // Reset when required fields are missing
        if (blank($this->filters['sagsnr']) || blank($this->filters['kreditor_id'])) {
            $this->hasPreviewed = false;
            $this->previewCount = 0;
            $this->sqlPreview = '';

            return;
        }

        $query = $this->buildQuery();

        $this->previewCount = (clone $query)->count();
        $this->hasPreviewed = true;

        $sql = $query->toSql();
        $bindings = $query->getBindings();

        foreach ($bindings as $binding) {
            $value = is_numeric($binding)
                ? $binding
                : "'" . addslashes($binding) . "'";

            $sql = preg_replace('/\?/', $value, $sql, 1);
        }

        $this->sqlPreview = $sql;
    }

    public function getResultsProperty()
    {
        if (! $this->hasPreviewed) {
            return collect();
        }

         return $this->buildQuery()
        ->latest()
        ->limit(5)
        ->get();
    }

    public function toggleSqlPreview(): void
    {
        $this->showSqlPreview = ! $this->showSqlPreview;
    }

    public function render()
    {
        $savedSearches = SavedSearch::where('user_id', auth()->id())
            ->latest()
            ->get()
            ->map(function ($search) {
                $filters = array_merge([
                    'sagsnr' => '',
                    'kreditor_id' => null,
                    'debitor_navn' => '',
                    'status_id' => null,
                    'postnr' => '',
                ], $search->filters ?? []);

                $search->result_count = Sager::query()
                    ->when($filters['sagsnr'], fn ($q) => $q->where('sagsnr', 'like', '%' . $filters['sagsnr'] . '%'))
                    ->whereHas('sagerkreditor', fn ($q) => $q->where('kreditors.id', $filters['kreditor_id']))
                    ->when($filters['debitor_navn'], fn ($q) => $q->whereHas('sagerdebitor', fn ($d) => $d->where('navn', 'like', '%' . $filters['debitor_navn'] . '%')))
                    ->when($filters['status_id'], fn ($q) => $q->whereHas('sagerStatus', fn ($s) => $s->where('status.id', $filters['status_id'])))
                    ->when($filters['postnr'], fn ($q) => $q->whereHas('sagerdebitor', fn ($d) => $d->where('postnr', $filters['postnr'])))
                    ->count();

                return $search;
            });

        return view('livewire.search-constructor', [
            'kreditorer' => Kreditorer::orderBy('navn')->get(),
            'statuses' => Status::orderBy('tekst')->get(),
            'postnumre' => Postnr::orderBy('postnr')->get(),
            'savedSearches' => $savedSearches,
        ]);
    }
}