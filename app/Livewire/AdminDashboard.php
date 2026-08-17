<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use App\Models\Sager;
use App\Models\Kreditorer;
use App\Models\User;
use App\Models\Konsulenter;
use App\Models\Sagsbehandler;
use Spatie\Permission\Models\Role;
use App\Services\Gdpr\SagerGdprService;
use App\Services\Dashboard\SagerChartService;
use Spatie\Activitylog\Models\Activity;

class AdminDashboard extends Component
{
    use WithPagination;

    protected SagerChartService $chartService;

    public $search = '';
    public $sortField = 'sagers.id';
    public $sortAsc = true;
    public $selectedKreditor = null;

    public $activities = null;

    public array $recordsByKreditor = [];
    public array $userStats = [];
    public array $roleStats = [];
    public array $konsulentStats = [];
    public array $sagsbehandlerStats = [];
    public array $systemWarnings = [];

    public $gdprExpired = 0;
    public $gdprExpiring = 0;

    public $sessionSeconds = 0;
    public $loginAt;

    public $quickMenuScreen = 'main';

    public $activeTab = 'overview';
    public bool $showQuickMenu = false;
    public bool $readyToLoad = false;

    public bool $loadUnhandled = false;
    public bool $loadIncoming = false;
    public bool $loadUnread = false;
    public bool $loadEditing = false;
    public bool $showWelcomeModal = false;

    public int $loadedSections = 0;
    public int $totalSections = 4;


    /**
     * 🔥 Livewire 3 Event Listeners
     */
    #[On('kreditor-filter-changed')]
    public function handleKreditorFilterChanged($kreditor = null)
    {
        $this->selectedKreditor = $kreditor;
        $this->resetPage();
    }

    #[On('refreshDashboard')]
    public function refreshDashboard()
    {
        $this->resetPage();
    }

    public function mount()
    {
        $this->loginAt = auth()->user()->last_login_at ?? now();
        $this->activities = collect();
    }

    public function boot(SagerChartService $chartService)
    {
        $this->chartService = $chartService;
    }

    public function toggleQuickMenu()
    {
        $this->showQuickMenu = !$this->showQuickMenu;

        if ($this->showQuickMenu) {
            $this->quickMenuScreen = 'main';
        }
    }

    public function openImportSagerMenu()
    {
        $this->quickMenuScreen = 'import-sager';
    }

    public function closeQuickMenu()
    {
        $this->showQuickMenu = false;
    }

    public function goToImportSager($lotusID)
    {
        return redirect()->route('sager.import.form', [
            'kreditor' => $lotusID
        ]);
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;

        match ($tab) {
            'unhandled'       => $this->loadUnhandled  ?: ($this->loadUnhandled = true)  && $this->loadedSections++,
            'incoming'        => $this->loadIncoming   ?: ($this->loadIncoming = true)   && $this->loadedSections++,
            'unread_messages' => $this->loadUnread     ?: ($this->loadUnread = true)     && $this->loadedSections++,
            'live_editing'    => $this->loadEditing    ?: ($this->loadEditing = true)    && $this->loadedSections++,
            default           => null,
        };

        if ($tab === 'overview') {
            $this->dispatch('refresh-sager-chart');
        }
    }

    /**
     * Klik på kreditor fra oversigtskortene i AdminDashboard
     */
    public function filterByKreditor($kreditor = null)
    {
        $this->selectedKreditor = ($this->selectedKreditor === $kreditor) ? null : $kreditor;
        $this->resetPage();
    }

    private function loadStats(SagerGdprService $gdpr)
    {
        $this->recordsByKreditor = \DB::table('kreditors')
            ->leftJoin(
                'sager_kreditor',
                'kreditors.id',
                '=',
                'sager_kreditor.kreditor_id'
            )
            ->select(
                'kreditors.navn',
                \DB::raw('COUNT(sager_kreditor.sag_id) as count')
            )
            ->groupBy('kreditors.id', 'kreditors.navn')
            ->orderByDesc('count')
            ->limit(10)
            ->pluck('count', 'navn')
            ->toArray();

        $this->gdprExpired = Sager::gdprExpired()->count();
        $this->gdprExpiring = Sager::gdprExpiringSoon()->count();
    }

    #[Computed]
    public function gdprExpiredCount(): int
    {
        return Sager::gdprExpired()->count();
    }

    public function render()
    {
        if (! $this->readyToLoad) {
            return view('livewire.admin.dashboard', [
                'sagers' => collect(),
                'kreditors' => collect(),
                'totalSager' => 0,
                'totalKreditorer' => 0,
                'activities' => collect(),
                'recordsByKreditor' => [],
                'userStats' => [],
                'roleStats' => [],
                'konsulentStats' => [],
                'sagsbehandlerStats' => [],
                'systemWarnings' => [],
                'gdprExpiredCount' => 0,
            ]);
        }

        $sagers = Sager::join('sager_debitor', 'sagers.id', '=', 'sager_debitor.sag_id')
            ->join('debitors', 'sager_debitor.debitor_id', '=', 'debitors.id')
            ->join('sager_kreditor', 'sagers.id', '=', 'sager_kreditor.sag_id')
            ->join('kreditors', 'sager_kreditor.kreditor_id', '=', 'kreditors.id')
            ->when($this->search, function ($query) {
                $query->where('sagers.sagsnr', 'like', '%' . $this->search . '%')
                    ->orWhere('debitors.navn', 'like', '%' . $this->search . '%')
                    ->orWhere('kreditors.navn', 'like', '%' . $this->search . '%');
            })
            ->when($this->selectedKreditor, function ($query) {
                return $query->where('kreditors.navn', $this->selectedKreditor);
            })
            ->select(
                'sagers.*',
                'debitors.navn as debitor_navn',
                'kreditors.navn as kreditor_navn'
            )
            ->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc')
            ->paginate(10);

        return view('livewire.admin.dashboard', [
            'sagers' => $sagers,
            'kreditors' => Kreditorer::withCount('sager')->get(),
            'totalSager' => Sager::count(),
            'totalKreditorer' => Kreditorer::count(),
            'userStats' => $this->userStats,
            'roleStats' => $this->roleStats,
            'konsulentStats' => $this->konsulentStats,
            'sagsbehandlerStats' => $this->sagsbehandlerStats,
            'systemWarnings' => $this->systemWarnings,
            'gdprExpiredCount' => $this->gdprExpiredCount,
        ]);
    }

    public function tickSessionTime()
    {
        $this->sessionSeconds = now()->diffInSeconds($this->loginAt);
    }

    private function loadUserStats()
    {
        $this->userStats = [
            'total' => User::count(),
            'active_today' => User::whereDate('last_login_at', today())->count(),
        ];

        $this->roleStats = Role::withCount('users')
            ->get()
            ->pluck('users_count', 'name')
            ->toArray();
    }

    private function loadRelationStats()
    {
        $this->konsulentStats = Konsulenter::withCount('sager')
            ->orderByDesc('sager_count')
            ->take(5)
            ->pluck('sager_count', 'navn')
            ->toArray();

        $this->sagsbehandlerStats = Sagsbehandler::withCount('sagersagsbehandler')
            ->orderByDesc('sagersagsbehandler_count')
            ->take(5)
            ->pluck('sagersagsbehandler_count', 'navn')
            ->toArray();
    }

    private function loadSystemWarnings()
    {
        $warnings = [];

        if (Sager::count() === 0) {
            $warnings[] = "Ingen sager fundet";
        }

        if (Kreditorer::count() === 0) {
            $warnings[] = "Ingen kreditorer fundet";
        }

        if (User::count() === 0) {
            $warnings[] = "Ingen brugere fundet";
        }

        if (Sager::whereNull('sagsnr')->count() > 0) {
            $warnings[] = "Sager uden sagsnummer fundet";
        }

        $this->systemWarnings = $warnings;
    }

    public function goToCreateKreditor()
    {
        return redirect()->route('kreditorer.create');
    }

    public function goToCreateBrev()
    {
        return redirect()->route('admin.breve.opret');
    }

    public function goToFindSag()
    {
        return redirect()->route('sager.search');
    }

    public function goToGdprScan()
    {
        return redirect()->route('gdpr.sager.retention');
    }

    public function goToCreateUser()
    {
        return redirect()->route('users.create');
    }

    public function getLoadingPercentProperty(): int
    {
        $loaded = collect([
            $this->loadUnhandled,
            $this->loadIncoming,
            $this->loadUnread,
            $this->loadEditing,
        ])->filter()->count();

        return (int) round($loaded / $this->totalSections * 100);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function loadDashboard()
    {
        $gdpr = app(SagerGdprService::class);

        $this->loadStats($gdpr);
        $this->loadUserStats();
        $this->loadRelationStats();
        $this->loadSystemWarnings();

        $this->activities = Activity::query()
            ->with(['causer', 'subject'])
            ->latest()
            ->take(20)
            ->get();

        $this->readyToLoad = true;
        $this->dispatch('dashboard-loaded');
    }

    public function getSagerChartDataProperty()
    {
        return $this->readyToLoad
            ? $this->chartService->getSagerUdvikling()
            : [
                'labels' => [],
                'datasets' => []
            ];
    }

    public function dismissWelcomeModal(): void
    {
        session()->forget('show_welcome_modal');
    }
}