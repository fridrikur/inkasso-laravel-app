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
    public $ubehandledeSager = [];
    public $ubehandledeSagerCount = 0;
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
            ->with(['debitor', 'kreditor'])
            ->where(function ($query) use ($searchTerm) {
                $query->where('sagsnr', 'like', "%{$searchTerm}%")
                      ->orWhere('id', 'like', "%{$searchTerm}%")
                      ->orWhereHas('debitor', fn($q) => $q->where('navn', 'like', "%{$searchTerm}%"))
                      ->orWhereHas('kreditor', fn($q) => $q->where('navn', 'like', "%{$searchTerm}%"));
            })
            ->latest()
            ->take(10)
            ->get();
    }

    public function loadData()
    {
        $user = auth()->user();

        // 🟢 Tjekker via pivot-tabellen (notifybrugere) om brugeren har fået aktiveret notifikationer
        if ($user->receivesNotifications()) {
            // 📄 Vis nyligt ankomne sager
            $this->latestSager = Sager::with([
                    'debitor',
                    'kreditor',
                    'sagsbehandler'
                ])
                ->latest()
                ->take(8)
                ->get();
        } else {
            $this->latestSager = collect();
        }

        // --- Ulæste beskeder / dialoger (det som unreadForUser håndterer) ---
        $unreadQuery = Sager::unreadForUser($user)->with(['debitor', 'kreditor']);
        // Hvis du har en separat variabel til ulæste dialoger, f.eks. $this->unreadDialogsCount, 
        // kan du opdatere den her i stedet for at røre ved ubehandlede sager.

        // --- Ubehandlede sager (Sager oprettet af kreditorer, som mangler konsulent) ---
        $ubehandledeQuery = Sager::with(['debitor', 'kreditor'])
            ->has('kreditor')                      // Skal være oprettet/knyttet til en kreditor
            ->whereDoesntHave('konsulent');        // Mangler en konsulent (hvilket udelukker medarbejder-sager, da de nu automatisk får hovedkonsulenten)

        $this->ubehandledeSagerCount = $ubehandledeQuery->count();
        $this->ubehandledeSager = $ubehandledeQuery->latest()->take(5)->get();

        // --- Aktive sager ---
        $this->myActiveSagerCount = Sager::whereNull('afsluttet')->count();

        // --- Sager med nye beskeder ---
        $this->sagerWithNewMessages = Sager::with(['debitor', 'kreditor', 'sagsbehandler']) // 🟢 Sørg for 'sagsbehandler' er her
            ->whereHas('dialogs', function ($q) use ($user) {
                $q->where('type', 'klientinformation')
                ->whereHas('messages', function ($mq) use ($user) {
                    $mq->whereNull('read_at')->where('sender_id', '!=', $user->id);
                });
            })
            ->withCount(['dialogs as unread_messages_count' => function ($q) use ($user) {
                $q->where('type', 'klientinformation')
                ->whereHas('messages', function ($mq) use ($user) {
                    $mq->whereNull('read_at')->where('sender_id', '!=', $user->id);
                });
            }])
            ->latest()
            ->take(6)
            ->get();
    }

    public function render()
    {
        return view('livewire.dashboard.medarbejder-dashboard');
    }
}