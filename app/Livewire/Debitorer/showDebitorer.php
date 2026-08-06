<?php

namespace App\Livewire\Debitorer;

use Livewire\Component;
use App\models\Debitorer;

class ShowDebitorer extends Component
{
    public function canDeleteDebitor(Debitorer $debitor): bool
    {
        return $debitor->sager()->count() === 0;
    }

    public function deleteDebitor($id)
    {
        $debitor = Debitorer::findOrFail($id);

        if ($debitor->sager()->exists()) {
            session()->flash(
                'error',
                'Debitor kan ikke slettes, da der stadig er sager tilknyttet.'
            );

            return;
        }

        $debitor->delete();
    }
    public function render()
    {
        return view('liveWire.debitorer.show-debitorer', [
            'debitorer' => Debitorer::with('sager')
                ->withCount('sager')
                ->having('sager_count', '>', 0)
                ->orderBy('navn')
                ->get(),

            'orphans' => Debitorer::doesntHave('sager')
                ->orderBy('navn')
                ->get(),
        ]);
    }
}