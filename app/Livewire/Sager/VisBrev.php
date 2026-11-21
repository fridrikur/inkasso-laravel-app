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
    public string $flettetSubEmne = '';
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

        // Flet data ind med BrevMergeService (spontane ændringer gemmes ikke ved skift)
        $service = app(BrevMergeService::class);

        $result = $service->mergeWithMeta((string) ($brev->tekst ?? ''), $this->sag);
        $this->flettetTekst = $result['text'] ?? $result['html'] ?? '';

        $emneResult = $service->mergeWithMeta((string) ($brev->emne ?? ''), $this->sag);
        $this->flettetEmne = trim(strip_tags((string) ($emneResult['text'] ?? $emneResult['html'] ?? ($brev->emne ?? ''))));

        $subEmneResult = $service->mergeWithMeta((string) ($brev->sub_emne ?? ''), $this->sag);
        $rawSubEmne = $subEmneResult['text'] ?? $subEmneResult['html'] ?? ($brev->sub_emne ?? '');
        $this->flettetSubEmne = trim(strip_tags((string) $rawSubEmne));
    }

    // Helper til at hente det aktuelt valgte brev-objekt
    public function getAktueltBrevProperty(): ?Brev
    {
        return $this->selectedBrevId ? Brev::find($this->selectedBrevId) : null;
    }

    // Normaliserede margin- og typografi-værdier (undgår @php i Blade)
    public function getNavnTopProperty(): string
    {
        return str_replace(',', '.', (string) ($this->aktueltBrev?->navn_top_margin ?: 5.45));
    }

    public function getDatoTopProperty(): string
    {
        return str_replace(',', '.', (string) ($this->aktueltBrev?->dato_margin_top ?: 7.57));
    }

    public function getEmneTopProperty(): string
    {
        return str_replace(',', '.', (string) ($this->aktueltBrev?->emne_top_margin ?: 8.91));
    }

    public function getSubEmneTopProperty(): string
    {
        return str_replace(',', '.', (string) ($this->aktueltBrev?->sub_emne_top_margin ?? 0.40));
    }

    public function getBodyTopProperty(): string
    {
        return str_replace(',', '.', (string) ($this->aktueltBrev?->body_margin_top ?: 2));
    }

    public function getBrevhovedTopProperty(): string
    {
        return str_replace(',', '.', (string) ($this->aktueltBrev?->brevhoved_top_margin ?: 1.50));
    }

    public function getFirmaLeftProperty(): string
    {
        return str_replace(',', '.', (string) ($this->aktueltBrev?->firma_left_margin ?: 11.80));
    }

    public function getFirmaFontSizeProperty(): float
    {
        return (float) str_replace(',', '.', (string) ($this->aktueltBrev?->firma_font_size ?: 8.5));
    }

    public function getFirmaTitleSizeProperty(): float
    {
        return round($this->firmaFontSize * 1.55, 1);
    }

    public function getEmneFontSizeProperty(): string
    {
        return str_replace(',', '.', (string) ($this->aktueltBrev?->emne_font_size ?: 10.5));
    }

    public function getBodyFontSizeProperty(): string
    {
        return str_replace(',', '.', (string) ($this->aktueltBrev?->body_font_size ?: 9.5));
    }

    public function getLinjeafstandProperty(): string
    {
        return str_replace(',', '.', (string) ($this->aktueltBrev?->linjeafstand ?: 1.5));
    }

    public function getFontFamilyProperty(): string
    {
        return (string) ($this->aktueltBrev?->font_family ?: 'Century Schoolbook, Georgia, serif');
    }

    public function getFontColorProperty(): string
    {
        $rawColor = trim((string) ($this->aktueltBrev?->font_color ?? '#000000'));

        return preg_match('/^#[0-9a-fA-F]{6}$/', $rawColor) ? $rawColor : '#000000';
    }

    // Computed Properties til visningstilstand
    public function getSkjulAltProperty(): bool
    {
        return (bool) ($this->filter->skjulalle ?? false);
    }

    public function getVisAltProperty(): bool
    {
        return (bool) ($this->filter->visalle ?? false);
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