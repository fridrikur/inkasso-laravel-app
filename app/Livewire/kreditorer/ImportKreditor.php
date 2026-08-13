<?php

namespace App\Livewire\Kreditorer;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Models\Kreditorer;

class ImportKreditor extends Component
{
    use WithFileUploads;

    public $file;
    public $rows = [];
    public $mapping = [
        'lotusID' => 'kreditorID',
        'navn'    => 'firmanavn',
    ];

    public $headers = [];
    public $preview = false;
    public $importedRows = [];
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

            // phpMyAdmin JSON support
            if (isset($json[0]['type'])) {
                $table = collect($json)->firstWhere('type', 'table');
                $this->rows = $table['data'] ?? [];
            } else {
                $this->rows = $json;
            }
        } else {
            // CSV
            $lines = array_map('str_getcsv', explode("\n", trim($content)));
            $this->headers = array_shift($lines);

            $this->rows = collect($lines)
                ->map(fn ($row) => array_combine($this->headers, $row))
                ->toArray();
        }

        $this->headers = array_keys($this->rows[0] ?? []);
        $this->preview = true;
    }

    public function import()
    {
        $this->importedRows = [];
        $this->importedCount = 0;

        DB::transaction(function () {

            foreach ($this->rows as $index => $row) {

                $lotusID = (int) ($row[$this->mapping['lotusID']] ?? null);
                $navn    = trim($row[$this->mapping['navn']] ?? '');

                if (!$lotusID || !$navn) {
                    continue;
                }

                // Prevent duplicate-name collision
                $exists = Kreditorer::where('navn', $navn)
                    ->where('lotusID', '!=', $lotusID)
                    ->exists();

                if ($exists) {
                    $navn .= ' (' . $lotusID . ')';
                }

                Kreditorer::updateOrCreate(
                    ['lotusID' => $lotusID],
                    ['navn' => $navn]
                );

                // ✅ Track successful import
                $this->importedRows[] = $index;
                $this->importedCount++;
            }
        });

        $this->dispatch(
            'toast',
            message: "Importerede {$this->importedCount} kreditorer",
            type: 'success'
        );
    }


    public function exportCsv()
    {
        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');

            // CSV header
            fputcsv($handle, ['lotusID', 'navn']);

            Kreditorer::orderBy('lotusID')->chunk(500, function ($kreditors) use ($handle) {
                foreach ($kreditors as $kreditor) {
                    fputcsv($handle, [
                        $kreditor->lotusID,
                        $kreditor->navn,
                    ]);
                }
            });

            fclose($handle);
        }, 'kreditorer_' . now()->format('Ymd_His') . '.csv');
    }

    public function exportJson()
    {
        $data = Kreditorer::orderBy('lotusID')
            ->get()
            ->map(fn ($k) => [
                'kreditorID' => $k->lotusID,
                'firmanavn'  => $k->navn,
            ]);

        return response()->streamDownload(function () use ($data) {
            echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }, 'kreditorer_' . now()->format('Ymd_His') . '.json');
    }


    public function render()
    {
        return view('livewire.kreditorer.import-kreditor');
    }
}
