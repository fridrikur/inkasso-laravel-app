<?php

namespace App\Livewire\Sager;

use Livewire\Component;
use App\Models\Sager;

class ShowSager extends Component
{
 public $loading = true;
    public $progress = 0;

    public function loadSagers()
    {
        // Load sager in batches and update progress
        $totalSagers = Sager::count();
        $batchSize = 10;
        $batchCount = ceil($totalSagers / $batchSize);
        for ($i = 0; $i < $batchCount; $i++) {
            // Load batch of sager
            $sagers = Sager::offset($i * $batchSize)->limit($batchSize)->get();
            // Update progress
            $this->progress = ($i + 1) / $batchCount * 100;
        }
        $this->loading = false;
    }
    public function opretnysag()
    
    {
        return redirect()->to('/sager/create');
    }
    public function deleteSag($id)
    {
        $sag = Sager::find($id);
 
        $sag->delete();
    }

    public function render()
    {
        return view('livewire.sager.show-sager', [
            'sager' => Sager::withCount('sagertokens')->get(),
        ]);
    }
}