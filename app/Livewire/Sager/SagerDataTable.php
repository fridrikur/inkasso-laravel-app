<?php

namespace App\Livewire\Sager;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On; 
use App\Models\Sager;
use App\Models\Kreditorer;
use App\Services\Search\SagerSearchService;
use App\Traits\BuildsSagerQuery;

class SagerDataTable extends Component
{
    use WithPagination, BuildsSagerQuery;

    public string $sortField = 'sagers.id';
    public string $sortDirection = 'desc';

    public bool $showDeleteModal = false;
    public $search = '';

    // 🔥 Fjernet #[Reactive] så den kan bruges både standalone og i Dashboard
    public $selectedKreditor = null;

    public $recordsByKreditor = [];
    public $mode = 'all';
    public $uiMode = 'full';
    public $modeCount = 0;
    public int $trashCount = 0;
    public $kreditor = null;
    public $kreditors = [];

    protected $paginationTheme = 'tailwind';
    public array $filters = [];
    public ?int $deleteId = null;

    public function mount($mode = 'all', $uiMode = 'table', $selectedKreditor = null)
    {
        $this->mode = $mode;
        $this->uiMode = $uiMode;
        
        if ($selectedKreditor) {
            $this->selectedKreditor = $selectedKreditor;
        }

        if ($uiMode === 'full') {
            $this->trashCount = Sager::onlyTrashed()->count();
            $this->loadKreditorStats();
        }
    }

    #[On('kreditor-selected')]
    #[On('kreditor-filter-changed')]
    public function handleKreditorSelected($kreditor = null)
    {
        $this->selectedKreditor = $kreditor;
        $this->resetPage();
    }

    public function filterByKreditor($kreditor = null)
    {
        // Skift filter eller nulstil hvis der trykkes på den samme
        $this->selectedKreditor = ($this->selectedKreditor === $kreditor) ? null : $kreditor;

        // Informér eventuelle overordnede komponenter (f.eks. AdminDashboard)
        $this->dispatch('kreditor-filter-changed', kreditor: $this->selectedKreditor);

        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    protected function baseQuery()
    {
        return $this->baseSagerQuery();
    }

    protected function applyMode($query)
    {
        switch ($this->mode) {
            case 'trash':
                $query->onlyTrashed()->latest('deleted_at');
                break;
            
            case 'unhandled':
                $query->where(function ($q) {
                    $q->whereDoesntHave('activities')
                      ->orWhereHas('activities', function ($sub) {
                          $sub->whereNull('last_viewed_at')
                              ->whereNull('last_edited_at')
                              ->whereNull('heartbeat_at')
                              ->where(function ($x) {
                                  $x->whereNull('is_editing')
                                    ->orWhere('is_editing', false);
                              });
                      });
                });
                break;

            case 'incoming':
                $query->where('sagers.created_at', '>=', now()->subDays(10))
                    ->orderByDesc('sagers.created_at');
                break;

            case 'active':
                $query->whereHas('activities', function ($q) {
                    $q->where(function ($sub) {
                        $sub->where('last_viewed_at', '>=', now()->subDays(3))
                            ->orWhere('last_edited_at', '>=', now()->subDays(3))
                            ->orWhere('heartbeat_at', '>=', now()->subMinutes(5));
                    });
                });
                break;

            case 'live_editing':
                $query->whereHas('activities', function ($q) {
                    $q->where('is_editing', true)
                      ->where('heartbeat_at', '>=', now()->subMinutes(2));
                });
                break;

            case 'unread_messages':
                $query->whereHas('dialogs.messages', function ($q) {
                    $q->whereNull('read_at')
                      ->whereHas('sender.roles', function ($q2) {
                          $q2->where('name', 'Kreditor');
                      });
                });
                break;

            case 'kreditor':
                if ($this->kreditor) {
                    $query->whereHas('sagerkreditor', fn ($q) => $q->whereKey($this->kreditor->id));
                }
                break;
        }

        return $query;
    }

    /**
     * 🔍 SEARCH + FILTERS (Ren Eloquent - UDEN rå SQL kolonne-fejl)
     */
    protected function applyFilters($query)
    {
        $query->when($this->search, function ($q) {
            $q->where(function ($sub) {
                $sub->where('sagers.sagsnr', 'like', "%{$this->search}%")
                    ->orWhereHas('sagerdebitor', fn($d) => $d->where('debitors.navn', 'like', "%{$this->search}%"))
                    ->orWhereHas('sagerkreditor', fn($k) => $k->where('kreditors.navn', 'like', "%{$this->search}%"));
            });
        });

        // Kreditor filter via relation
        $query->when($this->selectedKreditor, function ($q) {
            $q->whereHas('sagerkreditor', function ($k) {
                $k->where('kreditors.navn', $this->selectedKreditor);
            });
        });

        return $query;
    }

    public function render()
    {
        // 1. Grundlæggende query for den valgte mode/tilstand
        $baseModeQuery = $this->applyMode($this->baseQuery());

        // 🟢 Totalt antal sager i denne tilstand (FØR søgning og ekstra filtre)
        $totalInMode = (clone $baseModeQuery)->count();

        // 2. Påfør ekstra brugerfiltre og frisøgning
        $query = $this->applyFilters($baseModeQuery);
        $query = app(SagerSearchService::class)->apply($query, $this->filters);

        $sagers = $query
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.sager.sager-data-table', [
            'sagers' => $sagers,
            'modeCount' => $totalInMode, // 🟢 Vis det reelle samlede antal i denne mode
            'totalRecords' => $sagers->total(), // Antallet der matcher den aktuelle søgning
            'trashCount' => Sager::onlyTrashed()->count(),
        ]);
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function opretnysag()
    {
        return redirect()->route('sager.create');
    }
    
    public function loadKreditorStats()
    {
        if ($this->uiMode === 'table') {
            return;
        }

        $this->kreditors = Kreditorer::all();

        $this->recordsByKreditor = $this->kreditors->mapWithKeys(function ($kreditor) {
            return [
                $kreditor->navn => $this->applyMode(
                    Sager::whereHas('sagerkreditor', function ($q) use ($kreditor) {
                        $q->where('kreditors.id', $kreditor->id);
                    })
                )->count()
            ];
        })->toArray();

        $this->modeCount = $this->applyMode(Sager::query())->count();
    } 

    public function placeholder()
    {
        return <<<'HTML'
            <x-ui-loader type="sager" />
        HTML;
    }

    // 🟢 Håndterer både det første klik på skraldespanden ($id medsendes) 
    // OG bekræftelsen i <x-confirm-delete-modal> (køres uden parametre)
    public function confirmDelete($id = null)
    {
        // 1. Hvis et $id sendes med fra tabellen, gemmer vi det og åbner modalen
        if ($id) {
            $sag = Sager::find($id);

            if ($sag && $sag->isEligibleForGdprDeletion()) {
                $this->dispatch('toast', [
                    'message' => 'Udløbne GDPR-sager må ikke sendes i papirkurven. De skal behandles via GDPR Retention.',
                    'type' => 'error',
                ]);
                return;
            }

            $this->deleteId = $id;
            $this->showDeleteModal = true;
            return;
        }

        // 2. Hvis metoden kaldes uden parametre (fra <x-confirm-delete-modal> Ja-knappen)
        if ($this->deleteId) {
            $this->deleteSag();
        }
    }

    public function cancelDelete()
    {
        $this->showDeleteModal = false;
        $this->deleteId = null;
    }

    public function deleteSag()
    {
        if (!$this->deleteId) return;

        $sag = Sager::findOrFail($this->deleteId);

        if ($sag->isEligibleForGdprDeletion()) {
            $this->showDeleteModal = false;
            $this->dispatch('toast', [
                'message' => 'Handling afvist: Sagen har overskredet GDPR 5-års grænsen.',
                'type' => 'error',
            ]);
            return;
        }

        $this->showDeleteModal = false;
        $this->dispatch('row-deleted', id: $sag->id);
        $sag->delete();
        $this->deleteId = null;
    }
}