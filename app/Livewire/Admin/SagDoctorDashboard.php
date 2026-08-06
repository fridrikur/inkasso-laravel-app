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
        $this->calculateStats($doctor);
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function calculateStats(SagDiagnosisService $doctor): void
    {
        $allSager = Sager::with([
            'sagerdebitor',
            'sagerkreditor',
            'sagersagsbehandler',
            'sagerkonsulent',
            'sagerStatus',
            'sagerAfslutning',
        ])->get();

        $stats = [
            'critical' => 0,
            'missing_handler' => 0,
            'missing_status' => 0,
            'invalid_closure' => 0,
            'healthy' => 0,
        ];

        foreach ($allSager as $sag) {
            // Tving opfriskning af relationer i hukommelsen
            $sag->unsetRelations();
            $sag->load([
                'sagerdebitor',
                'sagerkreditor',
                'sagersagsbehandler',
                'sagerkonsulent',
                'sagerStatus',
                'sagerAfslutning',
            ]);

            $diag = $doctor->diagnose($sag);

            if ($diag['healthy']) {
                $stats['healthy']++;
            }

            if (empty($sag->sagsnr) || $sag->sagerdebitor->isEmpty() || $sag->sagerkreditor->isEmpty()) {
                $stats['critical']++;
            }

            if ($sag->sagersagsbehandler->isEmpty() || $sag->sagerkonsulent->isEmpty()) {
                $stats['missing_handler']++;
            }

            if ($sag->sagerStatus->isEmpty() || $sag->sagerStatus->count() > 1) {
                $stats['missing_status']++;
            }

            if ($sag->afsluttet && $sag->sagerAfslutning->isEmpty()) {
                $stats['invalid_closure']++;
            }
        }

        $this->stats = $stats;
    }

    public function repairSag(int $sagId, SagRepairService $repairService, SagDiagnosisService $doctor): void
    {
        $sag = Sager::find($sagId);

        if ($sag) {
            $this->activeRepairLog = $repairService->repair($sag);
            $this->repairedSagId = $sag->id;
            $this->showRepairModal = true;

            $this->calculateStats($doctor);
        }
    }

    public function closeRepairModal(): void
    {
        $this->showRepairModal = false;
        $this->activeRepairLog = [];
        $this->repairedSagId = null;

        // 🟢 NULSTIL CACHE OG PAGINERING
        $doctor = app(SagDiagnosisService::class);
        $this->calculateStats($doctor);
        $this->resetPage();
        unset($this->results);
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

        $allSager = $query->get();
        
        $diagnosed = $allSager->map(function ($sag) use ($doctor) {
            // Tving genindlæsning af relationer så ændringer i databasen slår igennem med det samme
            $sag->unsetRelations();
            $sag->load([
                'sagerdebitor',
                'sagerkreditor',
                'sagersagsbehandler',
                'sagerkonsulent',
                'sagerStatus',
                'sagerAfslutning',
            ]);

            $diag = $doctor->diagnose($sag);
            return (object) [
                'sag' => $sag,
                'score' => $diag['score'] ?? 0,
                'healthy' => $diag['healthy'] ?? false,
                'issues' => collect($diag['issues'] ?? [])->map(fn($i) => (object) $i),
            ];
        });

        if ($this->activeTab === 'healthy') {
            $diagnosed = $diagnosed->filter(fn($item) => $item->healthy)->values();
        }

        $page = $this->getPage();
        $perPage = 10;
        $paginatedItems = $diagnosed->slice(($page - 1) * $perPage, $perPage)->values();

        return new LengthAwarePaginator(
            $paginatedItems,
            $diagnosed->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }

    public function render()
    {
        return view('livewire.admin.sag-doctor-dashboard', [
            'results' => $this->results,
            'orphanCount' => Debitorer::doesntHave('sager')->count(),
            'duplicateCount' => Sager::query()->select('sagsnr')->whereNotNull('sagsnr')->where('sagsnr', '!=', '')->groupBy('sagsnr')->havingRaw('COUNT(*) > 1')->get()->count(),
        ]);
    }
}