<?php

namespace App\Livewire\Sager;

use Livewire\Component;
use App\Models\Sager;
use App\Models\Dialog;
use Livewire\Attributes\On;

class SagTabs extends Component
{
    public ?Sager $sag = null;
    public string $activeTab = 'stamdata';

    public int $unreadKlientinfo = 0;
    public int $unreadHistorik = 0;
    public int $unreadBogholderi = 0;

    #[On('dialogUpdated')]
    public function mount(?Sager $sag = null, string $activeTab = 'stamdata')
    {
        $this->sag = $sag;
        $this->activeTab = $activeTab;
        $this->refreshBadges();
    }

    public function selectTab(string $tab): void
    {
        $this->activeTab = $tab;
        // 🟢 Sender event til forældre-komponenten, så den opdaterer sin $activeTab
        $this->dispatch('change-tab', tab: $tab);
    }

    public function refreshBadges(): void
    {
        if (! $this->sag || ! $this->sag->exists) {
            $this->unreadKlientinfo = 0;
            $this->unreadBogholderi = 0;
            $this->unreadHistorik   = 0;
            return;
        }

        $user = auth()->user();
        if (! $user) return;

        $dialogs = Dialog::where('sag_id', $this->sag->id)->get();

        $this->unreadKlientinfo = $dialogs->firstWhere('type', 'klientinformation')?->unreadForUser($user) ?? 0;
        $this->unreadHistorik   = $dialogs->firstWhere('type', 'historik')?->unreadForUser($user) ?? 0;
        $this->unreadBogholderi = $dialogs->firstWhere('type', 'bogholderi')?->unreadForUser($user) ?? 0;
    }

    public function changeTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        return view('livewire.sager.sag-tabs');
    }
}