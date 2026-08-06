<?php

namespace App\Livewire\Konsulenter;

use Livewire\Component;
use App\Models\Konsulenter;

class ShowKonsulenter extends Component
{
    public function opretnykonsulent()
    {
        return redirect()->to('/konsulenter/create');
    }
    public function deleteKonsulent($id)
    {
        $konsulent = Konsulenter::find($id);
 
        $konsulent->delete();
    }
    public function render()
    {
        $konsulenter = Konsulenter::with(['hovedkonsulent','skjultkonsulent','notifikationskonsulent'])->get();
        

        $hovedkonsulent = $konsulenter->filter(fn($k) => $k->hovedkonsulent->isNotEmpty());
        $skjultekonsulenter = $konsulenter->filter(fn($k) => $k->skjultkonsulent->isNotEmpty());
        $notifikationskonsulenter = $konsulenter->filter(fn($k) => $k->notifikationskonsulent->isNotEmpty());
        $skCount = $konsulenter->sum(fn($k) => $k->skjultkonsulent->count());
        $nkCount = $konsulenter->sum(fn($k) => $k->notifikationskonsulent->count());

        return view('liveWire.konsulenter.show-konsulenter', compact('konsulenter','hovedkonsulent','skjultekonsulenter','notifikationskonsulenter','skCount','nkCount'));
    }
}
