<?php

namespace App\Livewire;

use App\Models\SavedSearch;
use App\Models\Sager;
use Livewire\Component;
use Livewire\WithPagination;

class SavedSearchResults extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'tailwind';

    public SavedSearch $savedSearch;

    public function mount(SavedSearch $saved): void
    {
        abort_unless($saved->user_id === auth()->id(), 403);

        $this->savedSearch = $saved;
    }

    public function getResultsProperty()
    {
        $filters = $this->savedSearch->filters;

        return Sager::query()
            ->with([
                'sagerkreditor',
                'sagerdebitor',
                'sagerStatus',
            ])
            ->when($filters['sagsnr'] ?? null, function ($query) use ($filters) {
                $query->where('sagsnr', 'like', '%' . $filters['sagsnr'] . '%');
            })
            ->when($filters['kreditor_id'] ?? null, function ($query) use ($filters) {
                $query->whereHas('sagerkreditor', function ($q) use ($filters) {
                    $q->where('kreditors.id', $filters['kreditor_id']);
                });
            })
            ->when($filters['debitor_navn'] ?? null, function ($query) use ($filters) {
                $query->whereHas('sagerdebitor', function ($q) use ($filters) {
                    $q->where('navn', 'like', '%' . $filters['debitor_navn'] . '%');
                });
            })
            ->when($filters['status_id'] ?? null, function ($query) use ($filters) {
                $query->whereHas('sagerStatus', function ($q) use ($filters) {
                    $q->where('status.id', $filters['status_id']);
                });
            })
            ->when($filters['postnr'] ?? null, function ($query) use ($filters) {
                $query->whereHas('sagerdebitor', function ($q) use ($filters) {
                    $q->where('postnr', $filters['postnr']);
                });
            })
            ->latest()
            ->paginate(50);
    }

    public function render()
    {
        return view('livewire.saved-search-results');
    }
}