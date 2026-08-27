<?php

namespace App\Livewire\Sager;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Sager;
use Illuminate\Support\Facades\DB;
use App\Traits\BuildsSagerQuery;

class Papirkurv extends Component
{
    use WithPagination, BuildsSagerQuery;

    public $mode = 'trash';
    public int $trashCount = 0;

    public bool $showEmptyTrashModal = false;
    public bool $showRestoreModal = false;
    public ?int $sagToRestoreId = null;

    public function render()
    {
        $query = $this->baseSagerQuery()
            ->onlyTrashed()
            ->latest('deleted_at');

        return view('livewire.sager.papirkurv', [
            'sagers' => $query->paginate(25),
        ]);
    }

    public function restoreSag($id)
    {
        $this->sagToRestoreId = $id;
        $this->showRestoreModal = true;
    }

    public function cancelRestoreSag()
    {
        $this->sagToRestoreId = null;
        $this->showRestoreModal = false;
    }

    public function executeRestore()
    {
        if (!$this->sagToRestoreId) return;

        $sag = Sager::onlyTrashed()->findOrFail($this->sagToRestoreId);
        $sag->restore();

        $this->showRestoreModal = false;
        $this->sagToRestoreId = null;

        $this->dispatch('toast', 
            message: 'Sag blev succesfuldt gendannet fra papirkurv.', 
            type: 'success', 
            icon: 'check'
        );
    }

    public function forceDeleteSag($id)
    {
        $sag = Sager::onlyTrashed()->findOrFail($id);

        DB::transaction(function () use ($sag) {
            $this->performPermanentDelete($sag);
        });

        $this->dispatch('toast', 
            message: 'Sag permanent slettet.', 
            type: 'success', 
            icon: 'check'
        );
    }

    public function confirmEmptyTrash()
    {
        $this->showEmptyTrashModal = true;
    }

    public function cancelEmptyTrash()
    {
        $this->showEmptyTrashModal = false;
    }

    public function emptyTrash()
    {
        $trashedSager = Sager::onlyTrashed()->get();
        $deletedCount = 0;

        foreach ($trashedSager as $sag) {
            DB::transaction(function () use ($sag) {
                $this->performPermanentDelete($sag);
            });
            $deletedCount++;
        }

        $this->showEmptyTrashModal = false;

        if ($deletedCount > 0) {
            $this->dispatch('toast', 
                message: "Papirkurven blev tømt: {$deletedCount} sager blev slettet permanent.", 
                type: 'success', 
                icon: 'check'
            );
        } else {
            $this->dispatch('toast', 
                message: 'Papirkurven er allerede tom.', 
                type: 'warning', 
                icon: 'warning'
            );
        }
    }

    protected function performPermanentDelete(Sager $sag)
    {
        \App\Models\SagLock::where('sag_id', $sag->id)->delete();
        
        if (class_exists(\App\Models\SagEditRequest::class)) {
            \App\Models\SagEditRequest::where('sag_id', $sag->id)->delete();
        }

        $sag->sagerdebitor()->detach();
        $sag->sagerkreditor()->detach();
        $sag->sagersagsbehandler()->detach();
        $sag->sagerkonsulent()->detach();
        $sag->sagertokens()->detach();

        $sag->sagerStatus()->detach();
        $sag->sagerKtr()->detach();
        $sag->sagerBemaerkning()->detach();
        $sag->sagerAfslutning()->detach();
        $sag->sagerUdlaeg()->detach();

        $sag->dialogs()->delete();
        $sag->dokumenter()->delete();
        $sag->activities()->delete();

        $sag->forceDelete();
    }
}