<?php

namespace App\Livewire\Gdpr;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Sager;
use App\Services\Gdpr\SagerGdprService;

class SagerRetentionDashboard extends Component
{
    use WithPagination;

    public array $selected = [];
    public bool $confirming = false;
    public string $actionType = 'anonymize'; // 'anonymize' eller 'force_delete'
    public ?int $singleId = null;
    public string $tab = 'expired';

    protected $paginationTheme = 'tailwind';

    public function setTab(string $tab): void
    {
        $this->tab = $tab;
        $this->selected = [];
        $this->resetPage();
    }

    public function toggleSelectAll(array $currentIds): void
    {
        if (count(array_intersect($this->selected, $currentIds)) === count($currentIds)) {
            $this->selected = array_diff($this->selected, $currentIds);
        } else {
            $this->selected = array_unique(array_merge($this->selected, $currentIds));
        }
    }

    public function toggleSelect(int $id): void
    {
        if (in_array($id, $this->selected)) {
            $this->selected = array_values(array_diff($this->selected, [$id]));
        } else {
            $this->selected[] = $id;
        }
    }

    // Modal åbner for Anonymisering
    public function confirmAnonymize(?int $id = null): void
    {
        $this->singleId = $id;
        $this->actionType = 'anonymize';
        $this->confirming = true;
    }

    // Modal åbner for Permanent Sletning
    public function confirmForceDelete(?int $id = null): void
    {
        $this->singleId = $id;
        $this->actionType = 'force_delete';
        $this->confirming = true;
    }

    public function cancel(): void
    {
        $this->confirming = false;
        $this->singleId = null;
    }

    // Eksekver valgt handling
    public function executeAction(SagerGdprService $service): void
    {
        $ids = $this->singleId ? [$this->singleId] : $this->selected;

        if (empty($ids)) {
            return;
        }

        if ($this->actionType === 'anonymize') {
            $count = $service->anonymizeMany($ids);
            $message = "{$count} " . ($count === 1 ? 'sag blev anonymiseret.' : 'sager blev anonymiseret.');
        } else {
            // Permanent sletning udnytter forceDelete() som udløser booted() i Sager model
            $sager = Sager::whereIn('id', $ids)->get();
            $count = 0;
            foreach ($sager as $sag) {
                $sag->forceDelete();
                $count++;
            }
            $message = "{$count} " . ($count === 1 ? 'sag blev permanent slettet.' : 'sager blev permanent slettet.');
        }

        $this->selected = [];
        $this->singleId = null;
        $this->confirming = false;

        $this->dispatch('toast', [
            'message' => $message,
            'type' => 'success',
        ]);
    }

    public function render(SagerGdprService $service)
    {
        $stats = $service->getSummaryStats();

        $query = ($this->tab === 'expired')
            ? Sager::gdprExpired()->with('sagerdebitor')
            : Sager::gdprExpiringSoon()->with('sagerdebitor');

        $sager = $query->orderBy('afsluttet', 'asc')->paginate(15);

        return view('livewire.gdpr.sager-retention-dashboard', [
            'sager' => $sager,
            'stats' => $stats,
        ]);
    }
}