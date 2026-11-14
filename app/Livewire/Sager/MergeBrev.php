<?php

namespace App\Livewire\Sager;

use Livewire\Component;
use App\Models\Brev;
use App\Models\Sager;
use App\Services\BrevMergeService;

class MergeBrev extends Component
{
    public Sager $sag;

    public ?int $brevId = null;
    public string $emne = '';
    public string $tekst = '';
    public array $breveList = [];
    
    // Admin-lignende felter til den lækre editor
    public string $previewHtml = '';
    public ?string $flettetEmne = '';
    public array $missingTokens = [];
    public array $availableTokens = [];

    public function mount(Sager $sag)
    {
        $this->sag = $sag;
        $this->availableTokens = $this->buildAvailableTokens();

        $this->loadBreveList();

        if (!empty($this->breveList)) {
            $this->loadBrev($this->breveList[0]['id']);
        }
    }

    private function loadBreveList(): void
    {
        $this->breveList = Brev::orderBy('brevpos')->get()->toArray();
    }

    public function loadBrev(int $id): void
    {
        $brev = Brev::findOrFail($id);

        $this->brevId = $brev->id;
        $this->emne = $brev->emne ?? '';

        $service = app(BrevMergeService::class);
        
        $result = $service->mergeWithMeta($brev->tekst ?? '', $this->sag);
        $this->tekst = $result['text'] ?? $result['html'] ?? '';

        $emneResult = $service->mergeWithMeta($this->emne, $this->sag);
        $this->flettetEmne = strip_tags($emneResult['text'] ?? $this->emne);

        // 🟢 Send et signal til browseren om, at et nyt brev er klar
        $this->dispatch('brev-loaded', tekst: $this->tekst, emne: $this->flettetEmne);
    }

    public function updatedTekst(): void
    {
        $this->generatePreview();
    }

    public function updatedEmne(): void
    {
        $this->generatePreview();
    }

    public function generatePreview(): void
    {
        if (!$this->brevId) return;

        $service = app(BrevMergeService::class);

        // 🟢 Flet brevet, så ALLE klammer ({ktr}, {aktiv}, {firmanavn} osv.) omdannes til rigtig data
        $result = $service->mergeWithMeta($this->tekst, $this->sag);
        $this->previewHtml = $result['text'] ?? $result['html'] ?? '';
        $this->missingTokens = $result['missing'] ?? [];

        // Flet emnet
        $emneResult = $service->mergeWithMeta($this->emne, $this->sag);
        $this->flettetEmne = strip_tags($emneResult['text'] ?? $this->emne);
    }

    private function buildAvailableTokens(): array
    {
        $tokens = [];
        foreach ($this->sag->getFillable() as $field) {
            $tokens[] = $field;
        }

        $tokens[] = 'id';
        $tokens[] = 'sagsnr';
        $tokens[] = 'today';
        $tokens[] = 'aktiv';
        $tokens[] = 'firmanavn';
        $tokens[] = 'debitor_navn';
        $tokens[] = 'debitor_email';
        $tokens[] = 'ktr';

        if ($this->sag->debitor()->getRelated()->getFillable()) {
            foreach ($this->sag->debitor()->getRelated()->getFillable() as $field) {
                $tokens[] = 'debitor_' . $field;
            }
        }

        return array_unique($tokens);
    }
    
    public function render()
    {
        return view('livewire.sager.brev-flet', [
            'breve' => Brev::orderBy('brevpos')->get(),
            'breveList' => $this->breveList,
        ]);
    }
}