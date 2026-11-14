<?php

namespace App\Livewire\Sager;

use Livewire\Component;
use App\Models\Sager;
use App\Models\Brev;
use App\Models\BrevFilter;
use App\Services\BrevMergeService;

class VisBrev extends Component
{
    public Sager $sag;
    public array $breveList = [];
    public ?int $selectedBrevId = null;
    
    public string $flettetEmne = '';
    public string $flettetTekst = '';
    public ?BrevFilter $filter = null;

    public function mount(Sager $sag)
    {
        $this->sag = $sag;
        $this->breveList = Brev::orderBy('brevpos')->get()->toArray();

        if (!empty($this->breveList)) {
            $this->loadBrev($this->breveList[0]['id']);
        }
    }

    public function loadBrev(int $brevId)
    {
        $this->selectedBrevId = $brevId;
        $brev = Brev::findOrFail($brevId);
        
        // Hent filter-indstillinger for dette brev
        $this->filter = BrevFilter::where('brevID', $brevId)->first();

        // Flet data ind med BrevMergeService
        $service = app(BrevMergeService::class);
        
        $result = $service->mergeWithMeta($brev->tekst, $this->sag);
        $this->flettetTekst = $result['text'] ?? $result['html'] ?? '';

        $emneResult = $service->mergeWithMeta($brev->emne ?? '', $this->sag);
        $this->flettetEmne = $emneResult['text'] ?? $emneResult['html'] ?? ($brev->emne ?? '');
    }

    public function render()
    {
        return view('livewire.sager.vis-brev');
    }
}