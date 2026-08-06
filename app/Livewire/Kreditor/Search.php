<?php

namespace App\Livewire\Kreditor;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Sager;
use App\Services\Search\SagerSearchService;
use App\Models\Afslutning;

class Search extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public string $search = '';

    public string $filter = 'all';

    public ?int $afslutningId = null;

    public $afslutninger;

    public ?string $modtagetFrom = null;
    public ?string $modtagetTo = null;

    public ?string $afsluttetFrom = null;
    public ?string $afsluttetTo = null;

    public function mount()
    {
        abort_unless(auth()->user()->hasRole('Kreditor'), 403);

        $this->filter = request('filter', 'all');
        $this->afslutningId = request('afslutning_id');

        $this->afslutninger = Afslutning::orderBy('tekst')->get();

        $this->search = request()->string('search')->toString();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFilter()
    {
        $this->resetPage();
    }

    public function updatedAfslutningId()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset([
            'search',
            'afslutningId',

            'modtagetFrom',
            'modtagetTo',

            'afsluttetFrom',
            'afsluttetTo',
        ]);
    }

    public function updatedDateType()
    {
        $this->resetPage();
    }

    public function updatedDateFrom()
    {
        $this->resetPage();
    }

    public function updatedDateTo()
    {
        $this->resetPage();
    }

    public function updatedModtagetFrom()
    {
        $this->resetPage();
    }

    public function updatedModtagetTo()
    {
        $this->resetPage();
    }

    public function updatedAfsluttetFrom()
    {
        $this->resetPage();
    }

    public function updatedAfsluttetTo()
    {
        $this->resetPage();
    }
    public function render(
    SagerSearchService $service
    )
    {
        return view(
            'livewire.kreditor.search',
            [
                'sager' => $service->paginate([

                'search' => $this->search,

                'status' => $this->filter,

                'afslutning_id' => $this->afslutningId,


                'modtaget_from' => $this->modtagetFrom,
                'modtaget_to' => $this->modtagetTo,

                'afsluttet_from' => $this->afsluttetFrom,
                'afsluttet_to' => $this->afsluttetTo,

                ]),
            ]
        );
    }
}