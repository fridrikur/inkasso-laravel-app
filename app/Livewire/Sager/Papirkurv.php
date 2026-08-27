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

    // 🟢 Tilføj denne state til at styre modalen
    public bool $showEmptyTrashModal = false;

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
        $sag = Sager::onlyTrashed()->findOrFail($id);
        $sag->restore();

        session()->flash('success', 'Sag gendannet fra papirkurv.');
    }

    public function forceDeleteSag($id)
    {
        $sag = Sager::onlyTrashed()->findOrFail($id);

        DB::transaction(function () use ($sag) {
            $this->performPermanentDelete($sag);
        });

        session()->flash('success', 'Sag permanent slettet.');
    }

    // 🟢 Åbn bekræftelses-modalen
    public function confirmEmptyTrash()
    {
        $this->showEmptyTrashModal = true;
    }

    // 🟢 Luk modalen igen uden at slette
    public function cancelEmptyTrash()
    {
        $this->showEmptyTrashModal = false;
    }

    // 🟢 Slet ALT i papirkurven (nu udløst fra modalen)
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
            session()->flash('success', "Papirkurven blev tømt: {$deletedCount} sager blev slettet permanent.");
        } else {
            session()->flash('error', 'Papirkurven er allerede tom.');
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