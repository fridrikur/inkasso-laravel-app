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
use App\Traits\HasCrudModal; // 🟢 1. Tilføj trait
use Livewire\Attributes\Url; 

class SagerDataTable extends Component
{
    use WithPagination, BuildsSagerQuery, HasCrudModal; // 🟢 2. Indsæt trait

    public string $sortField = 'sagers.id';
    public string $sortDirection = 'desc';

    public $search = '';
    public $selectedKreditor = null;
    public $recordsByKreditor = [];
    public $mode = 'all';
    public $uiMode = 'full';
    public $modeCount = 0;
    public $kreditor = null;
    public $kreditors = [];

    public ?int $statusId = null;

    public int $perPage = 10; // 🟢 Tilføj denne for at undgå fejl med paginatoren

    protected $paginationTheme = 'tailwind';
    public array $filters = [];

    /**
     * 🟢 Dynamisk computed property til papirkurv-tælleren
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

    #[Url(as: 'papirkurv')]
    public bool $sag_smidt_i_papirkurven = false;

    #[Url(as: 'slettet')]
    public bool $sag_permanent_slettet = false;

    

    public function mount()
    {
        if (request()->query('papirkurv') == 1) {
            $this->sag_smidt_i_papirkurven = true;
        }

        // 🟢 Læs direkte fra URL'en i stedet for sessionen
        if (request()->query('slettet') == 1 || request()->query('deleted') == 1) {
            $this->sag_permanent_slettet = true;
        }

        if ($this->uiMode === 'full') {
            $this->loadKreditorStats();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | HasCrudModal påkrævede metoder (selvom Sager ikke har form-modal her)
    |--------------------------------------------------------------------------
    */
    public function resetForm(): void
    {
        $this->editingId = null;
    }

    public function loadItemData($id): void
    {
        // Ikke relevant for sager her, da redigering sker via sags-redigeringssiden
    }

    /*
    |--------------------------------------------------------------------------
    | Sletning tilpasset HasCrudModal ($deletingId / $showDeleteModal)
    |--------------------------------------------------------------------------
    */
    public function confirmDeleteModal($id = null): void
    {
        $this->resetValidation();

        $sag = Sager::withTrashed()->find($id);

        if ($sag && $sag->isEligibleForGdprDeletion()) {
            $this->dispatch('toast', [
                'message' => 'Udløbne GDPR-sager må ikke sendes i papirkurven. De skal behandles via GDPR Retention.',
                'type' => 'error',
            ]);
            return;
        }

        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->deletingId = null;
        $this->resetValidation();
    }

    public function deleteSag()
    {
        if (!$this->deletingId) return;

        $sag = Sager::withTrashed()->findOrFail($this->deletingId);

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

        $this->dispatch('row-deleted', id: $this->deletingId);
        $this->dispatch('toast', [
            'message' => $toastMsg,
            'type' => 'success',
        ]);

        $this->deletingId = null;

        if ($this->uiMode === 'full') {
            $this->loadKreditorStats();
        }
    }

    // (Resten af dine eksisterende metoder som mount, baseQuery, render, restoreSag osv. bevares uændret herunder...)
    protected function query()
    {
        return Sager::query()
            ->with([
                'sagerdebitor',
                'sagerkreditor',
            ])
            ->when(
                trim($this->search) !== '',
                function ($query) {
                    $search = trim($this->search);

                    $query->where(function ($q) use ($search) {
                        $q->where('sagsnr', 'like', "%{$search}%")

                            ->orWhereHas(
                                'sagerdebitor',
                                function ($q) use ($search) {
                                    $q->where(
                                        'navn',
                                        'like',
                                        "%{$search}%"
                                    );
                                }
                            )

                            ->orWhereHas(
                                'sagerkreditor',
                                function ($q) use ($search) {
                                    $q->where(
                                        'navn',
                                        'like',
                                        "%{$search}%"
                                    );
                                }
                            );
                    });
                }
            )
            ->orderBy(
                $this->sortField,
                $this->sortDirection
            )
            ->orderByDesc('id');
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

        $sagers = $query
            ->withExists(['dialogs as has_unread_messages' => function ($q) {
                $q->whereHas('messages', function ($m) {
                    $m->whereNull('read_at')
                      ->whereHas('sender.roles', fn($r) => $r->where('name', 'Kreditor'));
                });
            }])
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view(
            'livewire.sager.sager-data-table',
            [
                'sagers' => $sagers,
                'modeCount' => $totalInMode,
                'totalRecords' => $sagers->total(),
            ]
        );
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

    public function setMode(string $mode): void
    {
        $this->mode = $mode;
        $this->resetPage(); // Nulstiller paginering ved fane-skift
        
        if ($this->uiMode === 'full') {
            $this->loadKreditorStats();
        }
    }

    public function filterByKreditor($kreditorNavn): void
    {
        $this->selectedKreditor = $kreditorNavn;
        $this->resetPage(); // Nulstil paginering ved filterskift
    }

    
}