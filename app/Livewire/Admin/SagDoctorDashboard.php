<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Sager;
use App\Models\Debitorer;
use App\Services\Sager\SagDiagnosisService;
use App\Services\Sager\SagRepairService;
use Illuminate\Pagination\LengthAwarePaginator;

class SagDoctorDashboard extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public string $search = '';
    public string $activeTab = 'critical'; 

    public bool $showRepairModal = false;
    public array $activeRepairLog = [];
    public ?int $repairedSagId = null;

    public array $stats = [
        'critical' => 0,
        'missing_handler' => 0,
        'missing_status' => 0,
        'invalid_closure' => 0,
        'healthy' => 0,
    ];

    public function mount(SagDiagnosisService $doctor)
    {
        $this->calculateStats();
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function calculateStats(): void
    {
        // 🟢 Hurtige SQL-tællinger i stedet for at loade alle modeller i hukommelsen
        $this->stats = [
            'critical' => Sager::query()->where(function ($q) {
                $q->whereNull('sagsnr')
                  ->orWhere('sagsnr', '')
                  ->orWhereDoesntHave('sagerdebitor')
                  ->orWhereDoesntHave('sagerkreditor');
            })->count(),

            'missing_handler' => Sager::query()->where(function ($q) {
                $q->whereDoesntHave('sagersagsbehandler')
                  ->orWhereDoesntHave('sagerkonsulent');
            })->count(),

            'missing_status' => Sager::query()->where(function ($q) {
                $q->whereDoesntHave('sagerStatus')
                  ->orWhereHas('sagerStatus', null, '>', 1);
            })->count(),

            'invalid_closure' => Sager::query()->whereNotNull('afsluttet')
                ->whereDoesntHave('sagerAfslutning')
                ->count(),

            'healthy' => Sager::query()
                ->whereNotNull('sagsnr')
                ->where('sagsnr', '!=', '')
                ->whereHas('sagerdebitor')
                ->whereHas('sagerkreditor')
                ->whereHas('sagersagsbehandler')
                ->whereHas('sagerkonsulent')
                ->whereHas('sagerStatus', null, '=', 1)
                ->where(fn($q) => $q->whereNull('afsluttet')->orWhereHas('sagerAfslutning'))
                ->count(),
        ];
    }

    public function repairSag(int $sagId, SagRepairService $repairService): void
    {
        $sag = Sager::find($sagId);

        if ($sag) {
            $this->activeRepairLog = $repairService->repair($sag);
            $this->repairedSagId = $sag->id;
            $this->showRepairModal = true;

            $this->calculateStats();
        }
    }

    public function closeRepairModal(): void
    {
        $this->showRepairModal = false;
        $this->activeRepairLog = [];
        $this->repairedSagId = null;

        $this->calculateStats();
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function getResultsProperty()
    {
        $doctor = app(SagDiagnosisService::class);

        // 👻 ORPHANS
        if ($this->activeTab === 'orphans') {
            return Debitorer::doesntHave('sager')
                ->when($this->search, fn($q) => $q->where('navn', 'like', "%{$this->search}%"))
                ->paginate(10, ['*'], 'orphansPage');
        }

        // 🧬 DUPLIKATER
        if ($this->activeTab === 'duplicates') {
            $duplicates = Sager::query()
                ->select('sagsnr')
                ->whereNotNull('sagsnr')
                ->where('sagsnr', '!=', '')
                ->groupBy('sagsnr')
                ->havingRaw('COUNT(*) > 1')
                ->pluck('sagsnr');

            return Sager::query()
                ->whereIn('sagsnr', $duplicates)
                ->when($this->search, fn($q) => $q->where('sagsnr', 'like', "%{$this->search}%"))
                ->with(['sagerdebitor', 'sagerkreditor'])
                ->orderBy('sagsnr')
                ->paginate(10, ['*'], 'duplicatesPage');
        }

        // 🩺 SAGER DIAGNOSE
        $query = Sager::query()->with([
            'sagerdebitor',
            'sagerkreditor',
            'sagersagsbehandler',
            'sagerkonsulent',
            'sagerStatus',
            'sagerAfslutning',
        ]);

        if (!empty(trim($this->search))) {
            $searchTerm = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('sagsnr', 'like', $searchTerm)
                  ->orWhere('id', 'like', $searchTerm)
                  ->orWhereHas('sagerdebitor', fn($d) => $d->where('navn', 'like', $searchTerm))
                  ->orWhereHas('sagerkreditor', fn($k) => $k->where('navn', 'like', $searchTerm));
            });
        }

        $query->when($this->activeTab === 'missing_handler', function ($q) {
            $q->where(function ($sub) {
                $sub->whereDoesntHave('sagersagsbehandler')
                    ->orWhereDoesntHave('sagerkonsulent');
            });
        });

        $query->when($this->activeTab === 'missing_status', function ($q) {
            $q->where(function ($sub) {
                $sub->whereDoesntHave('sagerStatus')
                    ->orWhereHas('sagerStatus', null, '>', 1);
            });
        });

        $query->when($this->activeTab === 'invalid_closure', function ($q) {
            $q->whereNotNull('afsluttet')
              ->whereDoesntHave('sagerAfslutning');
        });

        $query->when($this->activeTab === 'critical', function ($q) {
            $q->where(function ($sub) {
                $sub->whereNull('sagsnr')
                    ->orWhere('sagsnr', '')
                    ->orWhereDoesntHave('sagerdebitor')
                    ->orWhereDoesntHave('sagerkreditor');
            });
        });

        // 🟢 Paginér FØR diagnosekørslen, så vi KUN analyserer de 10 sager der vises på siden!
        $paginatedSager = $query->paginate(10);

        $diagnosedItems = $paginatedSager->getCollection()->map(function ($sag) use ($doctor) {
            $diag = $doctor->diagnose($sag);
            return (object) [
                'sag' => $sag,
                'score' => $diag['score'] ?? 0,
                'healthy' => $diag['healthy'] ?? false,
                'issues' => collect($diag['issues'] ?? [])->map(fn($i) => (object) $i),
            ];
        });

        if ($this->activeTab === 'healthy') {
            $diagnosedItems = $diagnosedItems->filter(fn($item) => $item->healthy)->values();
        }

        // Returnér en paginator med de diagnosticerede elementer for den aktuelle side
        return new LengthAwarePaginator(
            $diagnosedItems,
            $paginatedSager->total(),
            $paginatedSager->perPage(),
            $paginatedSager->currentPage(),
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }

    public function render()
    {
        return view('livewire.admin.sag-doctor-dashboard', [
            'results' => $this->results,
            'orphanCount' => Debitorer::doesntHave('sager')->count(),
            'duplicateCount' => Sager::query()
                ->select('sagsnr')
                ->whereNotNull('sagsnr')
                ->where('sagsnr', '!=', '')
                ->groupBy('sagsnr')
                ->havingRaw('COUNT(*) > 1')
                ->toBase()
                ->get()
                ->count(),
        ]);
    }
}