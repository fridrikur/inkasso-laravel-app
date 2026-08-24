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
    public $search = '';

    public ?Debitorer $selectedDebitor = null;
    public bool $showModal = false;

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
            $this->dispatch('toast', message: 'Debitor blev slettet.', type: 'success');
        }
    }

    public function render()
    {
        // 1. Base Query med søgning
        $query = Debitorer::with('sager')
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('navn', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('pnr', 'like', '%' . $this->search . '%')
                        ->orWhere('pnr', 'like', '%' . $this->search . '%');
                });
            });

        // 2. Aktiv fane (Pagineret baseret på aktivt tab)
        $activeDebitorer = ($this->activeTab === 'active') 
            ? (clone $query)->whereHas('sager')->orderBy('navn')->paginate(15) 
            : collect();

        // 3. Forældreløse fane
        $orphans = ($this->activeTab === 'orphans') 
            ? (clone $query)->whereDoesntHave('sager')->orderBy('navn')->paginate(15) 
            : collect();

        // 4. Dubletter: Samme navn
        $sameNameNames = Debitorer::select('navn')
            ->whereNotNull('navn')
            ->where('navn', '!=', '')
            ->groupBy('navn')
            ->having(DB::raw('count(*)'), '>', 1)
            ->pluck('navn');

        $sameNameDebitorer = ($this->activeTab === 'same_name') 
            ? (clone $query)->whereIn('navn', $sameNameNames)->orderBy('navn')->paginate(15) 
            : collect();

        // 5. Dubletter: Samme pnr/PNR
        $pnrColumn = \Schema::hasColumn('debitors', 'pnr') ? 'pnr' : 'pnr';
        
        $samepnrValues = Debitorer::select($pnrColumn)
            ->whereNotNull($pnrColumn)
            ->where($pnrColumn, '!=', '')
            ->groupBy($pnrColumn)
            ->having(DB::raw('count(*)'), '>', 1)
            ->pluck($pnrColumn);

        $samepnrDebitorer = ($this->activeTab === 'same_pnr') 
            ? (clone $query)->whereIn($pnrColumn, $samepnrValues)->orderBy($pnrColumn)->paginate(15) 
            : collect();

        // Optællinger til fanerne (hurtige database counts)
        $totalActive = Debitorer::has('sager')->count();
        $totalOrphans = Debitorer::doesntHave('sager')->count();
        $totalSameName = Debitorer::whereIn('navn', $sameNameNames)->count();
        $totalSamepnr = Debitorer::whereIn($pnrColumn, $samepnrValues)->count();

        return view('livewire.debitorer.manage-debitorer', [
            'activeDebitorer'     => $activeDebitorer,
            'orphans'             => $orphans,
            'sameNameDebitorer'   => $sameNameDebitorer,
            'samepnrDebitorer'    => $samepnrDebitorer,
            
            'activeCount'         => $totalActive,
            'orphansCount'        => $totalOrphans,
            'sameNameCount'       => $totalSameName,
            'samepnrCount'        => $totalSamepnr,
        ]);
    }
}