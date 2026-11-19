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
        
        $result = $service->mergeWithMeta((string) ($brev->tekst ?? ''), $this->sag);
        $this->flettetTekst = $result['text'] ?? $result['html'] ?? '';

        $emneResult = $service->mergeWithMeta((string) ($brev->emne ?? ''), $this->sag);
        $this->flettetEmne = $emneResult['text'] ?? $emneResult['html'] ?? (string) ($brev->emne ?? '');
    }

    // Helper til at hente det aktuelt valgte brev-objekt
    public function getAktueltBrevProperty()
    {
        return $this->selectedBrevId ? Brev::find($this->selectedBrevId) : null;
    }

    // Computed Properties til visningstilstand (helt uden PHP i view)
    public function getSkjulAltProperty(): bool
    {
        return $this->filter->skjulalle ?? false;
    }

    public function getVisAltProperty(): bool
    {
        return $this->filter->visalle ?? false;
    }

    public function getVisNavnProperty(): bool
    {
        return $this->visAlt || (!$this->skjulAlt && ($this->filter->navn ?? true));
    }

    public function getVisAdresseProperty(): bool
    {
        return $this->visAlt || (!$this->skjulAlt && ($this->filter->adresse ?? true));
    }

    public function getVisDatoProperty(): bool
    {
        return $this->visAlt || (!$this->skjulAlt && ($this->filter->dato ?? true));
    }

    public function getVisSagsnrProperty(): bool
    {
        return $this->visAlt || (!$this->skjulAlt && ($this->filter->sagsnr ?? true));
    }

    public function getVisEmneProperty(): bool
    {
        return $this->visAlt || (!$this->skjulAlt && ($this->filter->emne ?? true));
    }

    public function render()
    {
        return view('livewire.sager.vis-brev', [
            'brev' => $this->aktueltBrev,
        ]);
    }
}