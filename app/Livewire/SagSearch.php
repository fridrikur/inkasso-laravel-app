<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Sager;
use App\Livewire\Forms\SagForm;
use Livewire\WithPagination;

class SagSearch extends Component
{
    public SagForm $form;

    use WithPagination;

    public int $total = 0;

    public array $tabs = []; // each tab = [id => sagId]
    public ?int $activeTabId = null;

    public $results = [];

    public $activeSagId = null;

    public bool $showResults = false;
    
    public bool $showByDropdown = false;

    public bool $isSearchMode = true;

    public function search()
    {
        $filters = $this->form->toFilterArray();

        if (empty(array_filter($filters))) {
            $this->results = collect();
            $this->total = 0;
            $this->showResults = false;
            return;
        }

        $this->results = Sager::query()
            ->with([
                'sagerdebitor',
                'sagerkreditor',
                'sagersagsbehandler'
            ])
            ->filter($filters)
            ->limit(50)
            ->get();

        $this->total = $this->results->count();

        $first = $this->results->first();

        $this->activeSagId = $first?->id;

        if ($first) {
            $this->form->fill($this->mapSagToForm($first));
        }
    }

    public function updated()
    {
        $filters = $this->form->toFilterArray();

        $this->showResults = false;
        $this->resetPage();

        if (empty(array_filter($filters))) {
            $this->results = [];
            $this->total = 0;
            return;
        }

        $this->total = Sager::query()
            ->filter($filters)
            ->count();
    }

    public function loadResults()
    {
        $this->showResults = true;

        $this->results = Sager::query()
            ->with(['sagerdebitor','sagerkreditor','sagersagsbehandler'])
            ->filter($this->form->toFilterArray())
            ->limit(50)
            ->get();

        $first = $this->results->first();

        $this->activeSagId = $first?->id;

        if ($first) {
            $this->form->fill($this->mapSagToForm($first));
        }

        $this->resetPage();
    }

 
    public function openTab($sagId)
    {
        $sagId = (int) $sagId;

        // already open?
        $existingTabKey = collect($this->tabs)->search(fn ($t) => $t['id'] === $sagId);

        if ($existingTabKey === false) {
            $sag = $this->results->firstWhere('id', $sagId);

            $this->tabs[] = [
                'id' => $sagId,
                'label' => $sag?->sagsnr ?? 'Unknown',
            ];
        }

        $this->activeTabId = $sagId;
    }

    public function closeTab($sagId)
    {
        $this->tabs = array_values(array_filter(
            $this->tabs,
            fn ($t) => $t['id'] !== $sagId
        ));

        if ($this->activeTabId === $sagId) {
            $this->activeTabId = $this->tabs[0]['id'] ?? null;
        }
    }

    public function mapSagToForm($sag): array
    {
        $debitor = $sag->sagerdebitor->first();
        $handler = $sag->sagersagsbehandler->first();    
        return [
            'sagsnr' => $sag->sagsnr,
            'navn' => $debitor?->navn,
            'co' => $debitor?->co,
            'adresse' => $debitor?->adresse,
            'postnr' => $debitor?->postnr,
            'by' => $debitor?->by,

            'hovedstol' => $sag->hovedstol,
            'renter' => $sag->renter,
            'gebyr' => $sag->gebyr,
            'indbetalt' => $sag->indbetalt,

            'status' => $sag->status,
            'sagsbehandler' => $handler?->id,
        ];
    }
    
    public function setActiveSag($sagId)
    {
        $sag = $this->results->firstWhere('id', $sagId);

        if (!$sag) return;

        $this->activeSagId = $sagId;

        // 🔥 THIS IS THE MISSING PIECE
        $this->form->fill($this->mapSagToForm($sag));
    }

    public function openSag($sagId)
    {
        $sag = $this->results->firstWhere('id', $sagId);

        if (!$sag) return;

        $this->activeSagId = $sagId;

        // 🔥 hydrate form for editing
        $this->form->fill($this->mapSagToForm($sag));

        $this->showResults = true;
    }

    public function render()
    {
        return view('livewire.sag-search');
    }
}