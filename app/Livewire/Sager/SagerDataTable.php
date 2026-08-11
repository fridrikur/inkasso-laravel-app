<?php

namespace App\Livewire\Sager;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On; 
use Livewire\Attributes\Computed;
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

    public $selectedKreditor = null;
    public $recordsByKreditor = [];
    public $mode = 'all';
    public $uiMode = 'full';
    public $modeCount = 0;
    public $kreditor = null;
    public $kreditors = [];

    public ?int $statusId = null;

    protected $paginationTheme = 'tailwind';
    public array $filters = [];
    public ?int $deleteId = null;

    #[On('status-changed')]
    public function handleStatusChanged(int $statusId)
    {
        $this->statusId = $statusId;
        $this->mode = 'status';
        $this->resetPage();
    }

    public function mount($mode = 'all', $uiMode = 'table', $selectedKreditor = null, $statusId = null)
    {
        $this->mode = $mode;
        $this->uiMode = $uiMode;
        $this->statusId = $statusId;
        
        if ($selectedKreditor) {
            $this->selectedKreditor = $selectedKreditor;
        }

        if ($uiMode === 'full') {
            $this->loadKreditorStats();
        }
    }

    /**
     * 🟢 Dynamisk computed property til papirkurv-tælleren
     * Bruger baseQuery() så tælleren altid matcher tabellens reelle indhold
     */
    #[Computed]
    public function trashCount(): int
    {
        $query = $this->baseQuery()->onlyTrashed();

        if ($this->selectedKreditor) {
            $query->whereHas('sagerkreditor', fn($k) => $k->where('kreditors.navn', $this->selectedKreditor));
        }

        return $query->count();
    }

    /**
     * 🟢 Dynamisk computed property til ulæste beskeder KUN fra Kreditorer
     * Bruger baseQuery() så tælleren altid matcher tabellens reelle indhold
     */
    #[Computed]
    public function unreadCount(): int
    {
        $query = $this->baseQuery()->whereHas('dialogs.messages', function ($q) {
            $q->whereNull('read_at')
              ->whereHas('sender.roles', fn($r) => $r->where('name', 'Kreditor'));
        });

        if ($this->selectedKreditor) {
            $query->whereHas('sagerkreditor', fn($k) => $k->where('kreditors.navn', $this->selectedKreditor));
        }

        return $query->count();
    }

    /**
     * Genberegner tællerne for kreditor-fanebrikkerne
     */
    public function loadKreditorStats()
    {
        if ($this->uiMode === 'table') {
            return;
        }

        $this->kreditors = Kreditorer::all();

        $this->recordsByKreditor = $this->kreditors->mapWithKeys(function ($kreditor) {
            return [
                $kreditor->navn => $this->applyMode(
                    $this->baseQuery()->whereHas('sagerkreditor', function ($q) use ($kreditor) {
                        $q->where('kreditors.id', $kreditor->id);
                    })
                )->count()
            ];
        })->toArray();

        $this->modeCount = $this->applyMode($this->baseQuery())->count();
    }

    /**
     * Skifter visningstilstand/fane og nulstiller pagineringen
     */
    public function setMode(string $mode): void
    {
        $this->mode = $mode;
        
        if ($this->uiMode === 'full') {
            $this->loadKreditorStats();
        }

        $this->resetPage();
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
        $this->selectedKreditor = ($this->selectedKreditor === $kreditor) ? null : $kreditor;
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

            case 'closed':
            case 'afsluttet':
                $query->whereNotNull('sagers.afsluttet');
                break;

            case 'active':
                $query->whereNull('sagers.afsluttet');
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

            case 'live_editing':
                $query->whereHas('activities', function ($q) {
                    $q->where('is_editing', true)
                      ->where('heartbeat_at', '>=', now()->subMinutes(2));
                });
                break;

            case 'unread_messages':
                $query->whereHas('dialogs.messages', function ($q) {
                    $q->whereNull('read_at')
                      ->whereHas('sender.roles', fn($r) => $r->where('name', 'Kreditor'));
                });
                break;

            case 'kreditor':
                if ($this->kreditor) {
                    $query->whereHas('sagerkreditor', fn ($q) => $q->whereKey($this->kreditor->id));
                }
                break;

            case 'status':
                if ($this->statusId) {
                    $query->whereHas('sagerStatus', fn($s) => $s->where('status.id', $this->statusId));
                }
                break;
        }

        return $query;
    }

    protected function applyFilters($query)
    {
        $query->when($this->search, function ($q) {
            $q->where(function ($sub) {
                $sub->where('sagers.sagsnr', 'like', "%{$this->search}%")
                    ->orWhereHas('sagerdebitor', fn($d) => $d->where('debitors.navn', 'like', "%{$this->search}%"))
                    ->orWhereHas('sagerkreditor', fn($k) => $k->where('kreditors.navn', 'like', "%{$this->search}%"));
            });
        });

        $query->when($this->selectedKreditor, function ($q) {
            $q->whereHas('sagerkreditor', function ($k) {
                $k->where('kreditors.navn', $this->selectedKreditor);
            });
        });

        return $query;
    }

    public function render()
    {
        $baseModeQuery = $this->applyMode($this->baseQuery());
        $totalInMode = (clone $baseModeQuery)->count();

        $query = $this->applyFilters($baseModeQuery);
        $query = app(SagerSearchService::class)->apply($query, $this->filters);

        // 🟢 Tjekker KUN for ulæste beskeder, hvor afsenderen har rollen 'Kreditor'
        $sagers = $query
            ->withExists(['dialogs as has_unread_messages' => function ($q) {
                $q->whereHas('messages', function ($m) {
                    $m->whereNull('read_at')
                      ->whereHas('sender.roles', fn($r) => $r->where('name', 'Kreditor'));
                });
            }])
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.sager.sager-data-table', [
            'sagers' => $sagers,
            'modeCount' => $totalInMode,
            'totalRecords' => $sagers->total(),
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

    public function placeholder()
    {
        return <<<'HTML'
            <x-ui-loader type="sager" />
        HTML;
    }

    /**
     * ♻️ Gendanner en sag fra papirkurven
     */
    public function restoreSag($id)
    {
        $sag = Sager::onlyTrashed()->find($id);

        if ($sag) {
            $sag->restore();

            $this->dispatch('toast', [
                'message' => 'Sagen er blevet gendannet.',
                'type' => 'success',
            ]);

            if ($this->uiMode === 'full') {
                $this->loadKreditorStats();
            }
        }
    }

    public function confirmDelete($id = null)
    {
        if ($id) {
            $sag = Sager::withTrashed()->find($id);

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

        $sag = Sager::withTrashed()->findOrFail($this->deleteId);

        if ($sag->isEligibleForGdprDeletion()) {
            $this->showDeleteModal = false;
            $this->dispatch('toast', [
                'message' => 'Handling afvist: Sagen har overskredet GDPR 5-års grænsen.',
                'type' => 'error',
            ]);
            return;
        }

        $this->showDeleteModal = false;

        if ($sag->trashed()) {
            $sag->forceDelete();
            $toastMsg = 'Sagen er slettet permanent.';
        } else {
            $sag->delete();
            $toastMsg = 'Sagen er lagt i papirkurven.';
        }

        $this->dispatch('row-deleted', id: $this->deleteId);
        $this->dispatch('toast', [
            'message' => $toastMsg,
            'type' => 'success',
        ]);

        $this->deleteId = null;

        if ($this->uiMode === 'full') {
            $this->loadKreditorStats();
        }
    }
}