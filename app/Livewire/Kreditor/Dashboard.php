<?php

namespace App\Livewire\Kreditor;

use Livewire\Component;
use App\Models\Sager;
use Illuminate\Support\Facades\Auth;
use App\Models\Afslutning;

class Dashboard extends Component
{
    public $kreditor;
    
    public $afslutninger;

    public string $search = '';

    public function search()
    {
        $search = trim($this->search);

        if ($search === '') {
            return redirect()->route('kreditor.search');
        }

        return redirect()->route(
            'kreditor.search',
            [
                'search' => $search,
            ]
        );
    }

    public function createSag()
    {
        return redirect()->route(
            'kreditor.sag.create'
        );
    }
    /**
     * Base query:
     * Only cases belonging to the logged-in creditor
     */
    private function baseQuery()
    {
        return Sager::query()
            ->whereHas('sagerkreditor', function ($query) {
                $query->where(
                    'kreditor_id',
                    $this->kreditor->id
                );
            });
    }


    /**
     * Closed cases last 30 days
     */
    
    /**
     * Data formatted for Chart.js
     */
    public function getChartDataProperty()
    {
        return [
            'labels' => array_keys($this->closedStats),

            'values' => array_values($this->closedStats),
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | Quick navigation
    |--------------------------------------------------------------------------
    */

    public function showActive()
    {
        return redirect()->route(
            'kreditor.search',
            [
                'filter' => 'active'
            ]
        );
    }


    public function showClosed()
    {
        return redirect()->route(
            'kreditor.search',
            [
                'filter' => 'closed'
            ]
        );
    }


    public function showAll()
    {
        return redirect()->route(
            'kreditor.search'
        );
    }


    public function searchClosed($type)
    {
        return redirect()->route(
            'kreditor.search',
            [
                'filter' => 'closed',
                'type'   => $type
            ]
        );
    }


    /**
     * Total closed cases last 30 days
     */
    public function getClosedStatsProperty(): array
    {
        $base = $this->baseQuery()
            ->whereNotNull('afsluttet')
            ->whereDate('afsluttet', '>=', now()->subDays(30));

        return $this->afslutninger
        ->mapWithKeys(function ($afslutning) use ($base) {
            $count = (clone $base)
                ->whereHas('sagerAfslutning', function ($q) use ($afslutning) {
                    $q->whereKey($afslutning->id);
                })
                ->count();

            return [$afslutning->tekst => $count];
        })
        ->toArray();
    }
    
    public function getClosedPercentagesProperty()
    {
        $total = $this->closedTotal;

        if ($total === 0) {
            return collect($this->closedStats)
                ->map(fn () => 0)
                ->toArray();
        }


        return collect($this->closedStats)
            ->map(function ($value) use ($total) {

                return round(
                    ($value / $total) * 100,
                    1
                );

            })
            ->toArray();
    }

    public function mount()
    {
        abort_unless(auth()->user()->hasRole('Kreditor'), 403);

        $this->afslutninger = Afslutning::orderBy('tekst')->get();

                $this->kreditor = Auth::user()
            ->kreditorer()
            ->first();

        abort_unless($this->kreditor, 403);

        $this->search = request('search', '');

    }

    public function render()
    {
        return view(
            'livewire.kreditor.dashboard'
        );
    }
}