<?php

namespace App\Livewire\Admin\Breve;

use Livewire\Component;
use App\Models\Brev;
use App\Models\Sager;
use App\Services\BrevMergeService;

class OpretBrev extends Component
{
    public ?int $brevId = null;

    public string $titel = '';
    public string $emne = '';
    public string $tekst = '';

    public array $breveList = [];

    public string $newBrevTitle = '';

    public string $previewHtml = '';
    public ?Sager $previewSag = null;

    public bool $previewExpanded = false;

    // -----------------------------------------
    // INIT
    // -----------------------------------------
    public function mount(): void
    {
        $this->loadBreveList();

        if (!empty($this->breveList)) {
            $this->loadBrev($this->breveList[0]['id']);
        }
    }

    // -----------------------------------------
    // LIST
    // -----------------------------------------
    public function loadBreveList(): void
    {
        $this->breveList = Brev::orderBy('brevpos')->get()->toArray();
    }

    // -----------------------------------------
    // LOAD BREV
    // -----------------------------------------
    public function loadBrev(int $id): void
    {
        $brev = Brev::findOrFail($id);

        $this->brevId = $brev->id;
        $this->titel  = $brev->titel;
        $this->emne   = $brev->emne ?? '';
        $this->tekst  = $brev->tekst ?? '';

        $this->generatePreview();
    }

    // -----------------------------------------
    // CREATE
    // -----------------------------------------
    public function createNewBrev(): void
    {
        if (!$this->newBrevTitle) return;

        $brev = Brev::create([
            'titel'   => $this->newBrevTitle,
            'emne'    => '',
            'tekst'   => '',
            'brevpos' => (Brev::max('brevpos') ?? 0) + 1,
        ]);

        $this->newBrevTitle = '';

        $this->loadBreveList();
        $this->loadBrev($brev->id);

        $this->dispatch('toast', [
            'message' => 'Nyt brev oprettet',
            'type' => 'success'
        ]);
    }

    // -----------------------------------------
    // SAVE
    // -----------------------------------------
    public function saveTemplate(): void
    {
        if (!$this->brevId) return;

        Brev::where('id', $this->brevId)->update([
            'titel' => $this->titel,
            'emne'  => $this->emne,
            'tekst' => $this->tekst,
        ]);

        $this->loadBreveList();

        $this->dispatch('toast', [
            'message' => 'Brev gemt',
            'type' => 'success'
        ]);
    }

    // -----------------------------------------
    // DELETE
    // -----------------------------------------
    public function deleteBrev(int $id): void
    {
        Brev::where('id', $id)->delete();

        $this->loadBreveList();

        if ($first = Brev::orderBy('brevpos')->first()) {
            $this->loadBrev($first->id);
        } else {
            $this->brevId = null;
            $this->titel = '';
            $this->emne = '';
            $this->tekst = '';
            $this->previewHtml = '';
        }

        $this->dispatch('toast', [
            'message' => 'Brev slettet',
            'type' => 'success'
        ]);
    }

    // -----------------------------------------
    // PREVIEW
    // -----------------------------------------
    public function generatePreview(): void
    {
        if (!$this->brevId) return;

        if (!$this->previewSag) {
            $this->previewSag = Sager::inRandomOrder()->first();
        }

        if (!$this->previewSag) {
            $this->previewHtml = '<em>Ingen sager tilgængelige</em>';
            return;
        }

        $service = app(BrevMergeService::class);

        $result = $service->mergeWithMeta(
            $this->tekst,
            $this->previewSag
        );

        $this->previewHtml =
            $result['text']
            ?? $result['html']
            ?? '';
    }

    // -----------------------------------------
    // RANDOM SAG
    // -----------------------------------------
    public function loadRandomSag(): void
    {
        $this->previewSag = Sager::inRandomOrder()->first();

        if (!$this->previewSag) return;

        $this->generatePreview();

        $this->dispatch('toast', [
            'message' => 'Random sag loaded',
            'type' => 'success'
        ]);
    }

    // -----------------------------------------
    // LIVE UPDATE
    // -----------------------------------------
    public function updatedTekst(): void
    {
        $this->generatePreview();
    }

    public function updatedTitel(): void
    {
        $this->generatePreview();
    }

    public function updatedEmne(): void
    {
        $this->generatePreview();
    }

    // -----------------------------------------
    // MODAL TOGGLE
    // -----------------------------------------
    public function togglePreview(): void
    {
        $this->previewExpanded = !$this->previewExpanded;
    }

    // -----------------------------------------
    // VIEW
    // -----------------------------------------
    public function render()
    {
        return view('livewire.admin.breve.opret-brev');
    }
}