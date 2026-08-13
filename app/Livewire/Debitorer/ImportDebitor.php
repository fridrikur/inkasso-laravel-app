<?php


namespace App\Livewire\Debitorer;


use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use App\Models\Debitorer;
use App\Models\Kreditorer;


class ImportDebitor extends Component
{
use WithFileUploads;


public $file;
public $rows = [];
public $headers = [];
public $preview = false;


public $importedRows = [];
public $importedCount = 0;
public $skipped = [];


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
$this->rows = $json[2]['data'] ?? $json;
} else {
$lines = array_map('str_getcsv', explode("\n", trim($content)));
$this->headers = array_shift($lines);
$this->rows = collect($lines)
->map(fn ($r) => array_combine($this->headers, $r))
->toArray();
}


$this->headers = array_keys($this->rows[0] ?? []);
$this->preview = true;
}


protected function normalizeDate($value)
{
return in_array($value, ['', '0000-00-00']) ? null : $value;
}


public function import()
{
$this->importedRows = [];
$this->importedCount = 0;
$this->skipped = [];


$validLotusIds = Kreditorer::pluck('lotusID')->toArray();


DB::transaction(function () use ($validLotusIds) {
foreach ($this->rows as $i => $row) {


$kreditorID = (int) ($row['kreditorID'] ?? 0);
$debitorID = (int) ($row['debitorid'] ?? 0);


if (!$debitorID || !in_array($kreditorID, $validLotusIds)) {
$this->skipped[] = $row;
continue;
}



Debitorer::updateOrCreate(
['debitorid' => $debitorID],
[
'navn' => $row['navn'] ?? null,
'co' => $row['co'] ?? null,
'adresse' => $row['adresse'] ?? null,
'postnr' => $row['postnr'] ?? null,
'email' => $row['email'] ?? null,
'tlf' => $row['tlf'] ?? null,
'mobil' => $row['mobil'] ?? null,
'adropl' => $this->normalizeDate($row['adropl'] ?? null),
'pnr' => $row['pnr'] ?? null,
'kontakt_bemaerkning' => $row['kontakt_bemaerkning'] ?? null,
]
);


$this->importedRows[] = $i;
$this->importedCount++;
}
});


$this->dispatch('toast', message: "Importerede {$this->importedCount} debitorer, " . count($this->skipped) . " ignoreret", type: 'success');
}


public function exportCsv()
{
return response()->streamDownload(function () {
$rows = Debitorer::all()->toArray();
$out = fopen('php://output', 'w');
fputcsv($out, array_keys($rows[0] ?? []));
foreach ($rows as $r) fputcsv($out, $r);
fclose($out);
}, 'debitorer.csv');
}


public function exportJson()
{
return response()->streamDownload(fn () => print Debitorer::all()->toJson(JSON_PRETTY_PRINT), 'debitorer.json');
}


public function render()
{
return view('livewire.debitorer.import-debitor');
}
}