<?php

namespace App\Livewire\Sagsbehandlere;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Sagsbehandler;
use App\Models\Kreditorer;

class ImportSagsbehandler extends Component
{
    use WithFileUploads;

    public $file;
    public $rows = [];
    public $headers = [];
    public $preview = false;

    // Mapping old file -> db fields
    public $mapping = [
        'navn'       => 'sagsbehandler', // old name → new navn
        'email'      => 'email',
        'tlf'        => 'tlf',
        'mobil'      => 'mobil',
        'kreditorID' => 'kreditorID',
        'HSB'        => 'HSB',
        'sbID'       => 'sbID',
    ];

    public $importedRows = [];
    public $skipped = [];
    public $importedCount = 0;

    protected $rules = [
        'file' => 'required|file|mimes:csv,json,txt|max:10240',
    ];

    public function updatedFile()
    {
        $this->validate();
        $this->parseFile();
    }

    protected function parseFile()
    {
        $content = file_get_contents($this->file->getRealPath());

        if ($this->file->getClientOriginalExtension() === 'json') {
            $json = json_decode($content, true);
            $this->rows = collect($json)->filter(fn ($r) => ($r['kreditorID'] ?? 0) != 0)->values()->toArray();
        } else {
            $lines = array_map('str_getcsv', explode("\n", trim($content)));
            $this->headers = array_map('trim', array_shift($lines));

            $this->rows = collect($lines)
                ->map(fn ($row) => array_combine($this->headers, $row))
                ->filter(fn ($r) => ($r['kreditorID'] ?? 0) != 0)
                ->values()
                ->toArray();
        }

        $this->headers = array_keys($this->rows[0] ?? []);
        $this->preview = true;
    }

    public function import()
    {
        $this->reset(['importedRows', 'skipped', 'importedCount']);

        $validKreditorer = Kreditorer::pluck('id', 'lotusID')->toArray();

        DB::transaction(function () use ($validKreditorer) {
            foreach ($this->rows as $index => $row) {

                $kreditorLotus = (int) ($row[$this->mapping['kreditorID']] ?? 0);
                $hsb           = (int) ($row[$this->mapping['HSB']] ?? 0);
                $sbID          = (int) ($row[$this->mapping['sbID']] ?? 0);

                if (!$kreditorLotus || !isset($validKreditorer[$kreditorLotus])) {
                    $this->skipped[] = ['index' => $index, 'reason' => 'Invalid kreditorID'];
                    continue;
                }

                $data = [
                    'navn'  => trim($row[$this->mapping['navn']] ?? 'Ukendt'),
                    'email' => trim($row[$this->mapping['email']] ?? 'ukendt@ukendt.dk'),
                    'tlf'   => trim($row[$this->mapping['tlf']] ?? '0'),
                    'mobil' => trim($row[$this->mapping['mobil']] ?? '0'),
                ];

                // Handle duplicates (unique fields)
                foreach (['navn', 'email', 'tlf', 'mobil'] as $field) {
                    if (Sagsbehandler::where($field, $data[$field])->exists()) {
                        $data[$field] .= ' (' . $sbID . ')';
                    }
                }

                $sb = Sagsbehandler::create($data);

                $kreditorId = $validKreditorer[$kreditorLotus];

                if ($hsb === -1) {
                    $sb->hovedsagsbehandler()->syncWithoutDetaching([$kreditorId]);
                } elseif ($hsb === 0) {
                    $sb->kreditor()->syncWithoutDetaching([$kreditorId]);
                }

                $this->importedRows[] = $index;
                $this->importedCount++;
            }
        });

        $this->dispatch('toast', message: "Importerede {$this->importedCount} sagsbehandlere", type: 'success');
    }

    public function exportCsv()
    {
        return response()->streamDownload(function () {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['id', 'navn', 'email', 'tlf', 'mobil']);

            Sagsbehandler::each(function ($sb) use ($out) {
                fputcsv($out, [$sb->id, $sb->navn, $sb->email, $sb->tlf, $sb->mobil]);
            });

            fclose($out);
        }, 'sagsbehandlere.csv');
    }

    public function exportJson()
    {
        return response()->streamDownload(function () {
            echo Sagsbehandler::all()->toJson(JSON_PRETTY_PRINT);
        }, 'sagsbehandlere.json');
    }

    public function render()
    {
        return view('liveWire.sagsbehandlere.import-sagsbehandler');
    }
}
