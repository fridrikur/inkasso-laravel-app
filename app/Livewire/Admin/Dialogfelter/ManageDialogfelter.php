<?php

namespace App\Livewire\Admin\Dialogfelter;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Sager;

class ManageDialogfelter extends Component
{
    use WithPagination;

    public $activeTab = 'sager';
    public $search = '';

    public ?Sager $selectedSag = null;
    public bool $showTreeModal = false;

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedActiveTab()
    {
        $this->resetPage();
    }

    public function openTreeModal($id)
    {
        // Returret til sagerdebitor
        $this->selectedSag = Sager::with(['sagerdebitor'])->find($id);
        $this->showTreeModal = true;
    }

    public function closeTreeModal()
    {
        $this->showTreeModal = false;
        $this->selectedSag = null;
    }

    public function render()
    {
        // 1. Base Query med søgning på sager og relateret sagerdebitor
        $query = Sager::with(['sagerdebitor'])
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('id', 'like', '%' . $this->search . '%')
                        ->orWhereHas('sagerdebitor', function ($deb) {
                            $deb->where('navn', 'like', '%' . $this->search . '%')
                                ->orWhere('email', 'like', '%' . $this->search . '%');
                        });
                });
            });

        // 2. Data for de enkelte faner
        $paginatedSager = ($this->activeTab === 'sager') 
            ? (clone $query)->orderBy('id', 'desc')->paginate(15) 
            : collect();

        $paginatedBogholderi = ($this->activeTab === 'bogholderi') 
            ? (clone $query)->orderBy('id', 'desc')->paginate(15) 
            : collect();

        $paginatedHistorik = ($this->activeTab === 'historik') 
            ? (clone $query)->orderBy('updated_at', 'desc')->paginate(15) 
            : collect();

        $paginatedKlienter = ($this->activeTab === 'klienter') 
            ? (clone $query)->has('sagerdebitor')->orderBy('id', 'desc')->paginate(15) 
            : collect();

        // 3. Optællinger til fanerne
        $totalSager = Sager::count();
        $totalBogholderi = Sager::count();
        $totalHistorik = Sager::count();
        $totalKlienter = Sager::has('sagerdebitor')->count();

        return view('livewire.admin.dialogfelter.manage-dialogfelter', [
            'paginatedSager'      => $paginatedSager,
            'paginatedBogholderi' => $paginatedBogholderi,
            'paginatedHistorik'   => $paginatedHistorik,
            'paginatedKlienter'   => $paginatedKlienter,
            
            'sagerCount'          => $totalSager,
            'bogholderiCount'     => $totalBogholderi,
            'historikCount'       => $totalHistorik,
            'klienterCount'       => $totalKlienter,
        ]);
    }
}