<?php

namespace App\Livewire\Sager;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Sager;

class ShowSager extends Component
{
    use WithPagination;

    public function opretnysag()
    {
        return redirect()->to('/sager/create');
    }

    public function deleteSag($id)
    {
        $sag = Sager::find($id);
        $sag?->delete();
    }

    public function render()
    {
        return view('livewire.sager.show-sager', [
            // Fjerner withCount midlertidigt for at teste hastigheden, og bruger lazy loading af relationer
            'sagers' => Sager::with(['sagerdebitor', 'sagerkreditor'])
                ->latest()
                ->paginate(25), // Sæt evt. til 25 i stedet for 50 for at lette læsset yderligere
        ]);
    }
}