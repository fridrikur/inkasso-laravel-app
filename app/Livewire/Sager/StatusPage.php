<?php

namespace App\Livewire\Sager;

use App\Models\Sager;
use App\Models\Status;
use App\Models\Kreditorer;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StatusPage extends Component
{
    use WithPagination;

    public Status $status;

    #[Url]
    public ?int $kreditor_id = null;

    public bool $showExportModal = false;

    // Alle tilgængelige felt-kolonner til CSV-eksport (Inkl. Status)
    public array $availableColumns = [
        'sagsnr'           => 'Sagsnummer',
        'status'           => 'Sagsstatus',
        'kreditor'         => 'Kreditor Navn',
        'debitor'          => 'Debitor Navn',
        'modtaget'         => 'Modtaget Dato',
        'afsluttet'        => 'Afsluttet Dato',
        'hovedstol'        => 'Hovedstol',
        'rente'            => 'Rente',
        'gebyr'            => 'Gebyr',
        'saldo'            => 'Saldo',
        'sagsbehandler'    => 'Sagsbehandler',
        'oprettet'         => 'Oprettet Dato',
    ];

    public array $selectedColumns = [
        'sagsnr',
        'status',
        'kreditor',
        'debitor',
        'modtaget',
        'afsluttet',
        'hovedstol',
    ];

    public function mount(Status $status)
    {
        $this->status = $status;
    }

    public function setStatus(int $statusId)
    {
        $this->status = Status::findOrFail($statusId);
        $this->dispatch('status-changed', statusId: $this->status->id);
        $this->resetPage();
    }

    public function setKreditor(?int $kreditorId = null)
    {
        $this->kreditor_id = ($this->kreditor_id === $kreditorId) ? null : $kreditorId;
        $selectedKreditorObj = $this->kreditor_id ? Kreditorer::find($this->kreditor_id) : null;
        
        $this->dispatch('kreditor-filter-changed', kreditor: $selectedKreditorObj?->navn);
        $this->resetPage();
    }

    protected function getSagerQuery()
    {
        $query = Sager::query()
            ->with([
                'sagerkreditor',
                'sagerdebitor',
                'sagersagsbehandler',
                'sagerStatus',
            ])
            ->whereHas('sagerStatus', function ($q) {
                $q->where('status.id', $this->status->id);
            });

        if ($this->kreditor_id) {
            $query->whereHas('sagerkreditor', function ($q) {
                $q->where('kreditors.id', $this->kreditor_id);
            });
        }

        return $query->orderByDesc('modtaget');
    }

    /**
     * Eksporterer sager til CSV med status i filnavn og kolonner
     */
    public function exportCsv(): StreamedResponse
    {
        $statusTekst = \Str::slug($this->status->tekst ?? $this->status->navn ?? 'status');

        if ($this->kreditor_id) {
            $kreditor = Kreditorer::find($this->kreditor_id);
            $kreditorNavn = \Str::slug($kreditor?->navn ?? "kreditor-{$this->kreditor_id}");
            $filename = "sager-{$kreditorNavn}-status-{$statusTekst}-" . now()->format('Y-m-d') . ".csv";
        } else {
            $filename = "sager-alle-kreditorer-status-{$statusTekst}-" . now()->format('Y-m-d') . ".csv";
        }

        $columnsToExport = $this->selectedColumns;

        // Skjul kreditor i CSV, hvis en kreditor allerede er valgt
        if ($this->kreditor_id) {
            $columnsToExport = array_values(array_filter($columnsToExport, fn($col) => $col !== 'kreditor'));
        }

        if (empty($columnsToExport)) {
            $this->dispatch('toast', [
                'message' => 'Vælg venligst mindst én kolonne til eksport.',
                'type' => 'warning',
            ]);
            return response()->stream(fn() => null, 200);
        }

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $allAvailableColumns = $this->availableColumns;

        return response()->stream(function () use ($columnsToExport, $allAvailableColumns) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // Excel UTF-8 BOM

            $csvHeaders = array_map(fn($col) => $allAvailableColumns[$col] ?? $col, $columnsToExport);
            fputcsv($file, $csvHeaders, ';');

            $this->getSagerQuery()->chunk(200, function ($sagerBatch) use ($file, $columnsToExport) {
                foreach ($sagerBatch as $sag) {
                    $row = [];
                    foreach ($columnsToExport as $col) {
                        $row[] = $this->formatColumnValue($sag, $col);
                    }
                    fputcsv($file, $row, ';');
                }
            });

            fclose($file);
        }, 200, $headers);
    }

    protected function formatColumnValue(Sager $sag, string $column): string
    {
        return match ($column) {
            'sagsnr'        => $sag->sagsnr ?? '',
            'status'        => $sag->sagerStatus->first()?->tekst ?? $this->status->tekst ?? '',
            'kreditor'      => $sag->sagerkreditor->first()?->navn ?? '',
            'debitor'       => $sag->sagerdebitor->first()?->navn ?? '',
            'modtaget'      => $sag->modtaget ? $sag->modtaget->format('d-m-Y') : '',
            'afsluttet'     => $sag->afsluttet ? $sag->afsluttet->format('d-m-Y') : '',
            'hovedstol'     => number_format((float) str_replace(',', '.', $sag->hovedstol ?? 0), 2, ',', '.'),
            'rente'         => number_format((float) str_replace(',', '.', $sag->rente ?? 0), 2, ',', '.'),
            'gebyr'         => number_format((float) str_replace(',', '.', $sag->gebyr ?? 0), 2, ',', '.'),
            'saldo'         => number_format((float) str_replace(',', '.', $sag->saldo ?? 0), 2, ',', '.'),
            'sagsbehandler' => $sag->sagersagsbehandler->first()?->navn ?? $sag->sagersagsbehandler->first()?->name ?? '',
            'oprettet'      => $sag->created_at ? $sag->created_at->format('d-m-Y H:i') : '',
            default         => $sag->{$column} ?? '',
        };
    }

    public function render()
    {
        $allStatuses = Status::all();
        $allKreditors = Kreditorer::withCount('sager')->get();

        return view('livewire.status.status-page', [
            'allStatuses' => $allStatuses,
            'allKreditors' => $allKreditors,
            'selectedKreditor' => $this->kreditor_id ? Kreditorer::find($this->kreditor_id) : null,
            'totalCount' => $this->getSagerQuery()->count(),
        ]);
    }
}