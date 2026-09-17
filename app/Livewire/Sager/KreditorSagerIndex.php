<?php

namespace App\Livewire\Sager;

use Livewire\Component;
use App\Models\Sager;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

class KreditorSagerIndex extends Component
{
    use WithPagination;

    public $search = '';

    #[Url(keep: true)] // 🟢 Kun én attribut her
    public $statusFilter = 'all'; 

    public function mount($status = null, $filter = null)
    {
        // Fanger værdien uanset om URL'en hedder ?status= eller ?filter=
        $param = $status ?? $filter ?? request('status') ?? request('filter');

        if ($param) {
            if ($param === 'afsluttede' || $param === 'closed') {
                $this->statusFilter = 'closed';
            } elseif ($param === 'aktive' || $param === 'active') {
                $this->statusFilter = 'active';
            } else {
                $this->statusFilter = 'all';
            }
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $user = auth()->user();
        $kreditor = $user->kreditorer()->firstOrFail();

        $search = trim($this->search ?? '');

        $query = Sager::query()
            // Kun sager der tilhører denne kreditor
            ->whereHas('kreditor', function ($q) use ($kreditor) {
                $q->where('kreditor_id', $kreditor->id);
            })
            // Filtrer efter status-faner (Tragt / Funnel)
            ->when($this->statusFilter === 'active', function ($q) {
                $q->whereNull('afsluttet');
            })
            ->when($this->statusFilter === 'closed', function ($q) {
                $q->whereNotNull('afsluttet');
            })
            // Søgefunktion
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('sagsnr', 'like', '%' . $search . '%')
                      ->orWhere('hovedstol', 'like', '%' . $search . '%')
                      ->orWhereHas('debitor', function ($q2) use ($search) {
                          $q2->where('navn', 'like', '%' . $search . '%')
                             ->orWhere('adresse', 'like', '%' . $search . '%')
                             ->orWhereHas('postnummer', function ($q3) use ($search) {
                                 $q3->where('by', 'like', '%' . $search . '%');
                             })
                             ->orWhere('postnr', 'like', '%' . $search . '%');
                      });
                });
            });

        $sager = $query
            ->with([
                'debitor',
                'debitor.postnummer',
            ])
            ->latest()
            ->paginate(15);

        // Fuzzy suggestion ved ingen resultater
        $suggestion = null;

        if ($search !== '' && $sager->isEmpty()) {
            $names = Sager::whereHas('kreditor', function ($q) use ($kreditor) {
                    $q->where('kreditor_id', $kreditor->id);
                })
                ->with('debitor.postnummer')
                ->limit(100)
                ->get()
                ->flatMap(function ($sag) {
                    return $sag->debitor->flatMap(function ($debitor) {
                        return array_filter([
                            $debitor->navn,
                            $debitor->adresse,
                            $debitor->postnr,
                            optional($debitor->postnummer)->by,
                        ]);
                    });
                })
                ->unique()
                ->values();

            $closest = null;
            $shortest = -1;

            foreach ($names as $name) {
                $distance = levenshtein(mb_strtolower($search), mb_strtolower($name));

                if ($distance === 0) {
                    $closest = $name;
                    break;
                }

                if ($shortest < 0 || $distance < $shortest) {
                    $closest = $name;
                    $shortest = $distance;
                }
            }

            if ($closest !== null && $shortest <= 5) {
                $suggestion = $closest;
            }
        }

        return view('livewire.sager.kreditor-sager-index', [
            'sager' => $sager,
            'suggestion' => $suggestion,
        ]);
    }
}