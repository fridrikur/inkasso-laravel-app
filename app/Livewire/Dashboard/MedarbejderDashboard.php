<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Sager;

class MedarbejderDashboard extends Component
{
    public $latestSager = [];
    public $sagerWithNewMessages = [];
    public $unreadSagerCount = 0;

    protected $listeners = [
        'klientinformationUpdated' => 'loadData',
        'sagCreated' => 'loadData',
    ];

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $user = auth()->user();

        // 📄 Seneste sager (WITH relations)
        $this->latestSager = Sager::with([
                'sagerdebitor',
                'sagerkreditor',
                'sagersagsbehandler'
            ])
            ->latest()
            ->take(5)
            ->get();

        // 💬 Sager med nye beskeder
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
            ->take(5)
            ->get();

        // 🔴 Ubehandlede sager
        $this->unreadSagerCount = Sager::unreadForUser($user)->count();
    }

    public function render()
    {
        return view('livewire.dashboard.medarbejder-dashboard');
    }
}