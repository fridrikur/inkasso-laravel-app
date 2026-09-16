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
        $sag = Sager::with(['debitor', 'dialogs.messages'])->find($id);

        if ($sag) {
            // Fjern eventuelle dubletter fra dialoger, hvis relationen skulle returnere duplikater
            $sag->setRelation('dialogs', $sag->dialogs->unique('id'));
        }

        $this->selectedSag = $sag;
        $this->showTreeModal = true;
    }

    public function closeTreeModal()
    {
        $this->showTreeModal = false;
        $this->selectedSag = null;
    }

    public function render()
    {
        // 1. Base Query: Sager der har mindst én dialog
        $query = Sager::has('dialogs')
            ->with(['debitor', 'dialogs'])
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('id', 'like', '%' . $this->search . '%')
                        ->orWhereHas('debitor', function ($deb) {
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
            ? (clone $query)->has('debitor')->orderBy('id', 'desc')->paginate(15) 
            : collect();

        // 3. Korrekte optællinger til fanerne
        $totalSager = Sager::has('dialogs')->count();
        $totalBogholderi = Sager::has('dialogs')->where('hovedstol', '>', 0)->count(); // Eller Sager::has('dialogs')->count();
        $totalHistorik = Sager::has('dialogs')->count();
        $totalKlienter = Sager::has('dialogs')->has('debitor')->count();

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