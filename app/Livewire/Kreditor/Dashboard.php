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

    public function performSearch()
    {
        $searchTerm = trim($this->search);

        if ($searchTerm === '') {
            return $this->redirectRoute('kreditor.search', navigate: true);
        }

        return $this->redirectRoute(
            'kreditor.search',
            ['search' => $searchTerm],
            navigate: true
        );
    }

    public function createSag()
    {
        return $this->redirectRoute('kreditor.sag.create', navigate: true);
    }

    private function baseQuery()
    {
        return Sager::query()
            ->whereHas('sagerkreditor', function ($query) {
                $query->where('kreditor_id', $this->kreditor->id);
            });
    }

    /**
     * Nøgletal til overblikskort
     */
    public function getActiveCountProperty(): int
    {
        return $this->baseQuery()->whereNull('afsluttet')->count();
    }

    public function getTotalHovedstolProperty(): float
    {
        return (float) $this->baseQuery()->whereNull('afsluttet')->sum('hovedstol');
    }

    public function getClosedCountProperty(): int
    {
        return $this->baseQuery()->whereNotNull('afsluttet')->count();
    }

    public function getRecentSagerProperty()
    {
        return $this->baseQuery()
            ->with(['sagerdebitor'])
            ->latest('created_at')
            ->take(5)
            ->get();
    }

    public function getChartDataProperty(): array
    {
        return [
            'labels' => array_keys($this->closedStats),
            'values' => array_values($this->closedStats),
        ];
    }

    public function showActive()
    {
        return $this->redirectRoute('kreditor.search', ['filter' => 'active'], navigate: true);
    }

    public function showClosed()
    {
        return $this->redirectRoute('kreditor.search', ['filter' => 'closed'], navigate: true);
    }

    public function showAll()
    {
        return $this->redirectRoute('kreditor.search', navigate: true);
    }

    public function getClosedStatsProperty(): array
    {
        $base = $this->baseQuery()
            ->whereNotNull('afsluttet')
            ->whereDate('afsluttet', '>=', now()->subDays(30));

        return $this->afslutninger
            ->mapWithKeys(function ($afslutning) use ($base) {
                $count = (clone $base)
                    ->whereHas('sagerAfslutning', function ($sub) use ($afslutning) {
                        $sub->whereKey($afslutning->id);
                    })
                    ->count();

                return [$afslutning->tekst => $count];
            })
            ->toArray();
    }

    public function mount()
    {
        abort_unless(auth()->user()->hasRole('Kreditor'), 403);

        $this->afslutninger = Afslutning::orderBy('tekst')->get();
        $this->kreditor = Auth::user()->kreditorer()->first();

        abort_unless($this->kreditor, 403);

        $this->search = request('search', '');
    }

    public function render()
    {
        return view('livewire.kreditor.dashboard');
    }
}