<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Sager;
use Livewire\Attributes\Url;

class MedarbejderDashboard extends Component
{
    #[Url(keep: true)]
    public string $search = '';

    public $latestSager = [];
    public $sagerWithNewMessages = [];
    public $unreadSager = [];
    public $unreadSagerCount = 0;
    public $myActiveSagerCount = 0;
    public $activeTab = 'overview'; // 'overview', 'unread', 'messages'

    protected $listeners = [
        'klientinformationUpdated' => 'loadData',
        'sagCreated' => 'loadData',
    ];

    public function mount()
    {
        $this->loadData();
    }

    public function setTab(string $tab)
    {
        $this->activeTab = $tab;
    }

    // 🔍 Beregnede søgeresultater baseret på $search inputfeltet
    public function getSearchResultsProperty()
    {
        if (empty(trim($this->search))) {
            return collect();
        }

        $searchTerm = trim($this->search);

        return Sager::query()
            ->with(['sagerdebitor', 'sagerkreditor'])
            ->where(function ($query) use ($searchTerm) {
                $query->where('sagsnr', 'like', "%{$searchTerm}%")
                      ->orWhere('id', 'like', "%{$searchTerm}%")
                      ->orWhereHas('sagerdebitor', fn($q) => $q->where('navn', 'like', "%{$searchTerm}%"))
                      ->orWhereHas('sagerkreditor', fn($q) => $q->where('navn', 'like', "%{$searchTerm}%"));
            })
            ->latest()
            ->take(10)
            ->get();
    }

    public function loadData()
    {
        $user = auth()->user();

        // 📄 Seneste sager (med relationer)
        $this->latestSager = Sager::with([
                'sagerdebitor',
                'sagerkreditor',
                'sagersagsbehandler'
            ])
            ->latest()
            ->take(8)
            ->get();

        // 💬 Sager med ulæste beskeder
        $this->sagerWithNewMessages = Sager::with([
                'sagerdebitor',
                'sagerkreditor',
                'sagersagsbehandler'
            ])
            ->whereHas('dialogs', function ($q) use ($user) {
                $q->where('type', 'klientinformation')
                  ->whereHas('messages', function ($mq) use ($user) {
                      $mq->whereNull('read_at')
                         ->where('sender_id', '!=', $user->id);
                  });
            })
            ->withCount(['dialogs as unread_messages_count' => function ($q) use ($user) {
                $q->where('type', 'klientinformation')
                  ->whereHas('messages', function ($mq) use ($user) {
                      $mq->whereNull('read_at')
                         ->where('sender_id', '!=', $user->id);
                  });
            }])
            ->latest()
            ->take(6)
            ->get();

        // 🔴 Ubehandlede sager
        $unreadQuery = Sager::unreadForUser($user)
            ->with(['sagerdebitor', 'sagerkreditor']);
            
        $this->unreadSagerCount = $unreadQuery->count();
        $this->unreadSager = $unreadQuery->latest()->take(5)->get();

        // 💼 Mine aktive sager (hvis tilknyttet sagsbehandler/bruger)
        $this->myActiveSagerCount = Sager::whereNull('afsluttet')->count();
    }

    public function render()
    {
        return view('livewire.dashboard.medarbejder-dashboard');
    }
}