<?php

namespace App\Livewire\Debitorer;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Debitorer;
use Illuminate\Support\Facades\DB;

class ManageDebitorer extends Component
{
    use WithPagination;

    public $activeTab = 'active';
    public $search = ''; // Søgefelt

    public ?Debitorer $selectedDebitor = null;
    public bool $showModal = false;

    // Nulstil pagination når der søges eller skiftes fane
    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedActiveTab()
    {
        $this->resetPage();
    }

    public function openDebitorModal($id)
    {
        $this->selectedDebitor = Debitorer::with('sager')->find($id);
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedDebitor = null;
    }

    public function deleteDebitor($id)
    {
        $debitor = Debitorer::find($id);
        if ($debitor) {
            $debitor->delete();
            session()->flash('message', 'Debitor blev slettet.');
        }
    }

    public function render()
    {
        // 1. Base Query med søgning (filtrerer på navn, email eller pnr/cpr)
        $query = Debitorer::with('sager')
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('navn', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('pnr', 'like', '%' . $this->search . '%');
                });
            });

        // Hent alle til optælling af faner (eller brug ufiltrerede til counts hvis du vil vise totaler)
        $allDebitorer = (clone $query)->get();

        // 2. Aktive / med sager (pagineret)
        $activeDebitorer = (clone $query)
            ->whereHas('sager')
            ->orderBy('navn')
            ->paginate(15);

        // 3. Forældreløse / uden sager (pagineret)
        $orphans = (clone $query)
            ->whereDoesntHave('sager')
            ->orderBy('navn')
            ->paginate(15);

        // 4. Dubletter: Samme navn
        $sameNameNames = Debitorer::select('navn')
            ->whereNotNull('navn')
            ->where('navn', '!=', '')
            ->groupBy('navn')
            ->having(DB::raw('count(*)'), '>', 1)
            ->pluck('navn');
        
        $sameNameDebitorer = Debitorer::whereIn('navn', $sameNameNames)->orderBy('navn')->paginate(15);

        // 5. Dubletter: Samme CPR/PNR
        $cprColumn = \Schema::hasColumn('debitors', 'cpr') ? 'cpr' : 'pnr';
        
        $sameCprValues = Debitorer::select($cprColumn)
            ->whereNotNull($cprColumn)
            ->where($cprColumn, '!=', '')
            ->groupBy($cprColumn)
            ->having(DB::raw('count(*)'), '>', 1)
            ->pluck($cprColumn);

        $sameCprDebitorer = Debitorer::whereIn($cprColumn, $sameCprValues)->orderBy($cprColumn)->paginate(15);

        // Optællinger til fanerne (totalt uden for søgning, eller tilpas efter behov)
        $totalActive = Debitorer::has('sager')->count();
        $totalOrphans = Debitorer::doesntHave('sager')->count();
        $totalSameName = Debitorer::whereIn('navn', $sameNameNames)->count();
        $totalSameCpr = Debitorer::whereIn($cprColumn, $sameCprValues)->count();

        return view('livewire.debitorer.manage-debitorer', [
            'activeDebitorer'     => $activeDebitorer,
            'orphans'             => $orphans,
            'sameNameDebitorer'   => $sameNameDebitorer,
            'sameCprDebitorer'    => $sameCprDebitorer,
            
            'activeCount'         => $totalActive,
            'orphansCount'        => $totalOrphans,
            'sameNameCount'       => $totalSameName,
            'sameCprCount'        => $totalSameCpr,
        ]);
    }
}