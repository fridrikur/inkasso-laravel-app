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

        session()->flash(
            'success',
            'Sag gendannet fra papirkurv.'
        );
    }

    public function forceDeleteSag($id)
    {
        $sag = Sager::onlyTrashed()->findOrFail($id);

        if (!$sag->isEligibleForGdprDeletion()) {

            session()->flash(
                'error',
                'Kun GDPR-udløbne sager kan slettes permanent.'
            );

            return;
        }

        DB::transaction(function () use ($sag) {

            /*
            |--------------------------------------------------------------------------
            | Remove pivot relations
            |--------------------------------------------------------------------------
            */

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
            
            /*
            |--------------------------------------------------------------------------
            | Delete child records
            |--------------------------------------------------------------------------
            */

            $sag->dialogs()->delete();
            $sag->dokumenter()->delete();
            $sag->activities()->delete();

            /*
            |--------------------------------------------------------------------------
            | Permanent delete
            |--------------------------------------------------------------------------
            */

            $sag->forceDelete();
        });

        session()->flash(
            'success',
            'GDPR-sag permanent slettet.'
        );
    }
}