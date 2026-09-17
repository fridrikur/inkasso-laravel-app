<?php

namespace App\Livewire\Kreditor;

use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use App\Models\Sager;
use App\Services\Search\SagerSearchService;
use App\Models\afslutning;

class Search extends Component
{
    use WithPagination;

    #[Url(keep: true)] // 🟢 Kun én Url-attribut pr. property
    public $search = '';

    protected $paginationTheme = 'tailwind';

    #[Url(keep: true)]
    public string $filter = 'all';

    #[Url(as: 'afslutning_id', keep: true)]
    public ?int $afslutningId = null;

    public $afslutninger;

    #[Url(keep: true)]
    public ?string $modtagetFrom = null;
    
    #[Url(as: 'modtaget_to', keep: true)]
    public ?string $modtagetTo = null;

    #[Url(as: 'afsluttet_from', keep: true)]
    public ?string $afsluttetFrom = null;
    
    #[Url(as: 'afsluttet_to', keep: true)]
    public ?string $afsluttetTo = null;

    public function mount()
    {
        abort_unless(auth()->user()->hasRole('Kreditor'), 403);

        $this->afslutninger = afslutning::orderBy('tekst')->get();

        // Fanger også hvis 'q' bruges i stedet for 'search' i URL'en (f.eks. ?q=23)
        if (empty($this->search) && request()->has('q')) {
            $this->search = request()->string('q')->toString();
        }
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
        $this->filter = 'all';
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

    public function render(SagerSearchService $service)
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