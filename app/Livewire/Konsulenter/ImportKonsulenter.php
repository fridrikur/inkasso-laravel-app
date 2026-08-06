<?php

namespace App\Livewire\Konsulenter;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use App\Models\Konsulenter;

class ImportKonsulenter extends Component
{
    use WithFileUploads;

    public $file;
    public $rows = [];
    public $headers = [];
    public $preview = false;

    // Mapping old file → konsulenter table
    public $mapping = [
        'navn'       => 'sagsbehandler', // old column name
        'email'      => 'email',
        'tlf'        => 'tlf',
        'kreditorID' => 'kreditorID',
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

    /**
     * Parse file and ONLY keep rows where kreditorID = 0
     */
    protected function parseFile()
    {
        $content = file_get_contents($this->file->getRealPath());

        if ($this->file->getClientOriginalExtension() === 'json') {
            $json = json_decode($content, true);

            $this->rows = collect($json)
                ->filter(fn ($r) => (int) ($r['kreditorID'] ?? -1) === 0)
                ->values()
                ->toArray();
        } else {
            $lines = array_map('str_getcsv', explode("\n", trim($content)));
            $this->headers = array_map('trim', array_shift($lines));

            $this->rows = collect($lines)
                ->map(fn ($row) => array_combine($this->headers, $row))
                ->filter(fn ($r) => (int) ($r['kreditorID'] ?? -1) === 0)
                ->values()
                ->toArray();
        }

        $this->headers = array_keys($this->rows[0] ?? []);
        $this->preview = true;
    }

    /**
     * Import konsulenter
     */
    public function import()
    {
        $this->reset(['importedRows', 'skipped', 'importedCount']);

        DB::transaction(function () {

            foreach ($this->rows as $index => $row) {

                $sbID = (int) ($row[$this->mapping['sbID']] ?? 0);

                // Safety: only import rows where kreditorID = 0
                if ((int) ($row[$this->mapping['kreditorID']] ?? -1) !== 0) {
                    $this->skipped[] = [
                        'index'  => $index,
                        'reason' => 'kreditorID is not 0',
                    ];
                    continue;
                }

                // Prepare navn
                $navn = trim($row[$this->mapping['navn']] ?? 'Ukendt');

                // Prepare email, ensure uniqueness
                $rawEmail = trim($row[$this->mapping['email']] ?? '');
                $email = $rawEmail !== ''
                    ? $rawEmail
                    : 'ukendt+' . $sbID . '+' . $index . '@konsulent.local';

                // Prepare tlf, ensure uniqueness
                $rawTlf = trim($row[$this->mapping['tlf']] ?? '');
                $tlf = $rawTlf !== '' ? $rawTlf : 1000000000 + $sbID + $index; // numeric fallback

                $data = compact('navn', 'email', 'tlf');

                // Append sbID if duplicates exist in DB
                foreach (['navn', 'email', 'tlf'] as $field) {
                    if (Konsulenter::where($field, $data[$field])->exists()) {
                        $data[$field] .= ' (' . $sbID . ')';
                    }
                }

                // Insert konsulent
                Konsulenter::create($data);

                $this->importedRows[] = $index;
                $this->importedCount++;
            }
        });

        $skippedCount = count($this->skipped);

        $this->dispatch(
            'toast',
            message: "Importerede {$this->importedCount} konsulenter, {$skippedCount} ignoreret",
            type: 'success'
        );
    }



    /**
     * Export CSV
     */
    public function exportCsv()
    {
        return response()->streamDownload(function () {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['id', 'navn', 'email', 'tlf']);

            Konsulenter::each(function ($k) use ($out) {
                fputcsv($out, [$k->id, $k->navn, $k->email, $k->tlf]);
            });

            fclose($out);
        }, 'konsulenter.csv');
    }

    /**
     * Export JSON
     */
    public function exportJson()
    {
        return response()->streamDownload(function () {
            echo Konsulenter::all()->toJson(JSON_PRETTY_PRINT);
        }, 'konsulenter.json');
    }

    public function render()
    {
        return view('liveWire.konsulenter.import-konsulenter');
    }
}
