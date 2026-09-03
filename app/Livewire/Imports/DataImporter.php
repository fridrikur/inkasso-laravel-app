<?php

namespace App\Livewire\Imports;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\ImportTemplate;
use App\Models\Sager;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Throwable;
use Illuminate\Support\Facades\Artisan; // Husk denne import i toppen
use Database\Seeders\DropdownDataSeeder;

class DataImporter extends Component
{
    use WithFileUploads;

    public string $userFile = 'users.sql';
    public string $debitorFile = 'debitors.sql';
    public string $sagerFile = 'dkgsager.sql';
    public string $kreditorFile = 'kreditorer.sql';
    public string $konsulentFile = 'konsulenter.sql';         // 🟢 Ny property til konsulenter
    public string $sagsbehandlerFile = 'sagsbehandlere.sql';
    public string $dialogFile = 'dialoger.sql';

    public string $importType = 'sager'; // Standard valg ('sager', 'kreditorer', 'debitorer')
    public $file;
    public array $sourceColumns = [];
    public array $previewRows = []; // Til live eksempelskema (første 3 rækker)
    public array $targetFields = [];
    public array $mapping = [];
    
    public ?int $selectedTemplateId = null;
    public string $templateName = '';
    public int $step = 1; // 1: Vælg type/fil, 2: Mapping/Templates/Preview, 3: Færdig
    public $importTemplateFile;
    public string $importNotes = '';

    public function mount()
    {
        $this->loadTargetFields();
    }

    public function updatedImportType()
    {
        $this->loadTargetFields();
        $this->mapping = [];
    }

    public function loadTargetFields()
    {
        $exclude = ['id', 'created_at', 'updated_at', 'deleted_at', 'user_id'];

        if ($this->importType === 'sager') {
            try {
                $fields = Sager::visibleFields();
            } catch (\Exception $e) {
                $fields = [];
            }

            if (empty($fields)) {
                $fields = Schema::getColumnListing('sagers');
            }

            $targetArray = collect($fields)
                ->reject(fn($field) => in_array($field, $exclude))
                ->mapWithKeys(fn($field) => [
                    $field => method_exists(Sager::class, 'alias') 
                        ? Sager::alias($field) 
                        : ucfirst(str_replace('_', ' ', $field))
                ])
                ->toArray();

            // Tilføj alle relationer / pivot-felter til sager (med gode beskrivelser)
            $targetArray['kreditor_id']      = 'Kreditor (Lotus ID)';
            $targetArray['debitor_id']       = 'Debitor (ID)';
            $targetArray['token_id']      = 'Tokens (ID)';
            $targetArray['status_id'] = 'Status (Forkortelse, f.eks. I, D, S)';
            $targetArray['sagsbehandler_id'] = 'Sagsbehandler (ID)';
            $targetArray['konsulent_id']     = 'Konsulent (ID)';
            $targetArray['ktr_id']         = 'KTR (Forkortelse, f.eks. K, E, L)';
            $targetArray['afslutning_id']  = 'Afslutning (Forkortelse, f.eks. b, a, o)';
            $targetArray['udlaeg_id']        = 'Udlæg (ID)';
            $targetArray['bemaerkning_id']   = 'Bemærkning (Forkortelse, f.eks. I, M, D)';

            $this->targetFields = $targetArray;

        } else {
            $table = match($this->importType) {
                'kreditorer' => 'kreditors',
                'debitorer' => 'debitors',
                default => 'kreditors',
            };

            $columns = Schema::hasTable($table) ? Schema::getColumnListing($table) : [];

            $this->targetFields = collect($columns)
                ->reject(fn($field) => in_array($field, $exclude))
                ->mapWithKeys(fn($field) => [$field => ucfirst(str_replace('_', ' ', $field))])
                ->toArray();
        }
    }

    public function updatedFile()
    {
        $this->validate([
            'file' => 'required|mimes:csv,txt|max:10240', // Fjern eventuelt xlsx medmindre du har PhpSpreadsheet installeret til at konvertere det
        ]);

        $path = $this->file->getRealPath();
        $stream = fopen($path, 'r');
        
        if (!$stream) {
            session()->flash('error', 'Kunne ikke åbne filen.');
            return;
        }

        $firstLine = fgets($stream);
        rewind($stream);
        
        $delimiter = (str_contains($firstLine, ';')) ? ';' : ',';
        $header = fgetcsv($stream, 0, $delimiter);

        // Læs op til de første 3 rækker til forhåndsvisning
        $preview = [];
        $count = 0;
        while (($row = fgetcsv($stream, 0, $delimiter)) !== false && $count < 3) {
            if (!empty(array_filter($row))) {
                $preview[] = $row;
                $count++;
            }
        }
        fclose($stream);

        $this->sourceColumns = $header ? array_filter(array_map('trim', $header)) : [];
        $this->previewRows = $preview;
        
        if (!empty($this->sourceColumns)) {
            if (empty($this->mapping)) {
                foreach ($this->targetFields as $fieldKey => $fieldLabel) {
                    foreach ($this->sourceColumns as $sourceCol) {
                        if (mb_strtolower(trim($sourceCol)) === mb_strtolower(trim($fieldKey))) {
                            $this->mapping[$fieldKey] = $sourceCol;
                        }
                    }
                }
            }
            $this->step = 2;
        } else {
            session()->flash('error', 'Kunne ikke læse kolonner fra filen. Tjek at filen er adskilt af komma eller semikolon.');
        }
    }

    public function loadTemplate()
    {
        if (!$this->selectedTemplateId) return;

        $template = ImportTemplate::find($this->selectedTemplateId);
        if ($template) {
            $this->mapping = $template->mapping;
            $this->importType = $template->import_type;
            $this->loadTargetFields();
        }
    }

    public function saveTemplate()
    {
        $this->validate([
            'templateName' => 'required|string|max:255',
        ]);

        ImportTemplate::create([
            'user_id' => auth()->id(),
            'name' => $this->templateName,
            'import_type' => $this->importType,
            'mapping' => $this->mapping,
        ]);

        session()->flash('success', 'Import-skabelon blev gemt!');
        $this->templateName = '';
    }

    public function deleteTemplate($id)
    {
        ImportTemplate::where('id', $id)->where('user_id', auth()->id())->delete();
        session()->flash('success', 'Skabelonen blev slettet.');
    }

    public function updateTemplate($id, $newName)
    {
        $template = ImportTemplate::where('id', $id)->where('user_id', auth()->id())->first();
        if ($template) {
            $template->update(['name' => $newName]);
            session()->flash('success', 'Skabelonen blev opdateret.');
        }
    }

    public function executeImport()
    {
        if (!$this->file || empty($this->mapping)) {
            session()->flash('error', 'Du skal vælge en fil og parre mindst ét felt før du kan importere.');
            return;
        }

        try {
            // Brug direkte den midlertidige filsti for at undgå problemer med storage-diske
            $absolutePath = $this->file->getRealPath();
            
            $stream = fopen($absolutePath, 'r');
            if (!$stream) {
                session()->flash('error', 'Kunne ikke åbne den uploadede fil.');
                return;
            }

            $firstLine = fgets($stream);
            rewind($stream);
            $delimiter = (str_contains($firstLine, ';')) ? ';' : ',';
            
            $header = fgetcsv($stream, 0, $delimiter);
            $header = $header ? array_map('trim', $header) : [];
            
            $table = match($this->importType) {
                'sager' => 'sagers',
                'kreditorer' => 'kreditors',
                'debitorer' => 'debitors',
                default => 'sagers',
            };

            $relationRules = [
                'kreditor_id'      => ['table' => 'sager_kreditor',      'lookup_table' => 'kreditors',     'lookup_col' => 'lotusID',     'foreign_key' => 'kreditor_id'],
                'debitor_id'       => ['table' => 'sager_debitor',       'lookup_table' => 'debitors',      'lookup_col' => 'id',          'foreign_key' => 'debitor_id'],
                'status_id'        => ['table' => 'sager_status',        'lookup_table' => 'status',        'lookup_col' => 'forkortelse', 'foreign_key' => 'status_id'],
                'bemaerkning_id'   => ['table' => 'sager_bemaerkning',   'lookup_table' => 'bemaerkning',   'lookup_col' => 'forkortelse', 'foreign_key' => 'bemaerkning_id'],
                'afslutning_id'    => ['table' => 'sager_afslutning',    'lookup_table' => 'afslutning',    'lookup_col' => 'forkortelse', 'foreign_key' => 'afslutning_id'],
                'ktr_id'           => ['table' => 'sager_ktr',           'lookup_table' => 'ktr',           'lookup_col' => 'forkortelse', 'foreign_key' => 'ktr_id'],
                'sagsbehandler_id' => ['table' => 'sager_sagsbehandler', 'lookup_table' => 'sagsbehandlers', 'lookup_col' => 'id',          'foreign_key' => 'sagsbehandler_id'],
                'konsulent_id'     => ['table' => 'sager_konsulent',     'lookup_table' => 'konsulenters',  'lookup_col' => 'id',          'foreign_key' => 'konsulent_id'],
            ];

            $importedCount = 0;
            $globalErrors = [];
            $rowNumber = 1;
            $lookupCache = []; 

            DB::transaction(function () use ($stream, $delimiter, $header, $table, $relationRules, &$importedCount, &$globalErrors, &$rowNumber, &$lookupCache) {
                while (($row = fgetcsv($stream, 0, $delimiter)) !== false) {
                    $rowNumber++;
                    
                    // Spring tomme rækker over
                    if (empty(array_filter($row))) {
                        continue;
                    }

                    $dataToInsert = [];
                    $resolvedRelations = [];
                    $rowHasError = false;
                    $rowErrors = [];

                    foreach ($this->mapping as $targetField => $sourceColumnName) {
                        if (empty($sourceColumnName)) {
                            continue;
                        }

                        $sourceIndex = array_search($sourceColumnName, $header);
                        
                        if ($sourceIndex !== false && isset($row[$sourceIndex])) {
                            $value = trim($row[$sourceIndex]);
                            if ($value === '') continue;

                            if (isset($relationRules[$targetField])) {
                                $rule = $relationRules[$targetField];
                                $cacheKey = "{$rule['lookup_table']}_{$value}";

                                if (!isset($lookupCache[$cacheKey])) {
                                    $lookupCache[$cacheKey] = DB::table($rule['lookup_table']);
                                        $lookupCache[$cacheKey] = DB::table($rule['lookup_table'])
                                            ->where($rule['lookup_col'], $value)
                                            ->first();
                                }

                                $relatedRecord = $lookupCache[$cacheKey];

                                if (!$relatedRecord) {
                                    $rowErrors[] = "Række $rowNumber: Kunne ikke finde match i '{$rule['lookup_table']}' for værdien '$value' i feltet $targetField.";
                                    $rowHasError = true;
                                    continue;
                                }

                                $resolvedRelations[$targetField] = [
                                    'table' => $rule['table'],
                                    'foreign_key' => $rule['foreign_key'],
                                    'id' => $relatedRecord->id,
                                ];
                            } else {
                                $dataToInsert[$targetField] = $value;
                            }
                        }
                    }

                    if ($rowHasError) {
                        if (count($globalErrors) < 10) {
                            $globalErrors = array_merge($globalErrors, $rowErrors);
                        }
                        continue; 
                    }

                    // Tillad indsættelse selvom $dataToInsert er tom (hvis der f.eks. kun importeres relationer eller timestamps)
                    $sagId = DB::table($table)->insertGetId(array_merge($dataToInsert, [
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]));

                    foreach ($resolvedRelations as $rel) {
                        DB::table($rel['table'])->insert([
                            'sag_id' => $sagId,
                            $rel['foreign_key'] => $rel['id'],
                            'created_at' => now(),
                        ]);
                    }

                    $importedCount++;
                }
            });

            fclose($stream);

            if (!empty($globalErrors)) {
                session()->flash('error', 'Import gennemført med fejl på enkelte rækker:<br>' . implode('<br>', $globalErrors));
                if ($importedCount > 0) {
                    $this->step = 3;
                }
                return;
            }

            if ($importedCount > 0) {
                $this->step = 3;
                session()->flash('success', "Succes! $importedCount sager blev succesfuldt importeret.");
            } else {
                session()->flash('error', 'Ingen data blev importeret. Kontrollér at din fil indeholder rækker under overskrifterne.');
            }

        } catch (Throwable $e) {
            session()->flash('error', 'Databasefejl under import: ' . $e->getMessage());
        }
    }

    public function exportTemplate($id)
    {
        $template = ImportTemplate::where('id', $id)->where('user_id', auth()->id())->first();
        
        if (!$template) {
            session()->flash('error', 'Skabelonen blev ikke fundet.');
            return;
        }

        $data = [
            'name' => $template->name,
            'import_type' => $template->import_type,
            'mapping' => $template->mapping,
        ];

        $filename = \Illuminate\Support\Str::slug($template->name) . '-template.json';

        return response()->streamDownload(function () use ($data) {
            echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }, $filename);
    }

    public function importTemplate()
    {
        $this->validate([
            'importTemplateFile' => 'required|file|mimes:json,txt|max:2048',
        ]);

        try {
            $path = $this->importTemplateFile->getRealPath();
            $content = file_get_contents($path);
            $data = json_decode($content, true);

            if (!isset($data['name'], $data['import_type'], $data['mapping'])) {
                session()->flash('error', 'Ugyldig skabelon-fil format.');
                return;
            }

            ImportTemplate::create([
                'user_id' => auth()->id(),
                'name' => $data['name'] . ' (Importeret)',
                'import_type' => $data['import_type'],
                'mapping' => $data['mapping'],
            ]);

            $this->importTemplateFile = null;
            session()->flash('success', 'Skabelon blev succesfuldt importeret fra JSON!');
        } catch (\Exception $e) {
            session()->flash('error', 'Fejl ved indlæsning af skabelon: ' . $e->getMessage());
        }
    }

    // Tilføj denne metode til at generere teksten baseret på mapping:
    public function generateNotes()
    {
        $activeMapping = array_filter($this->mapping);
        $mappedCount = count($activeMapping);
        $mappedFields = implode(', ', array_keys($activeMapping));
        
        $this->importNotes = "Import-konfiguration for type '{$this->importType}':\n" .
                            "- Antal parrede kolonner: {$mappedCount}\n" .
                            "- Parrede felter: {$mappedFields}\n" .
                            "- Genereret den: " . now()->format('d-m-Y kl. H:i');
    }
    
    public function runSystemImport()
    {
        \Illuminate\Support\Facades\Log::info('Import-knappen blev klikket!');

        try {
            $outputLog = [];
            $filesFound = false;

            set_time_limit(300);
            memory_get_usage(true);

            $tasks = [
                'Kreditorer'     => ['cmd' => 'import:kreditorer',     'path' => storage_path($this->kreditorFile)],
                'Brugere'        => ['cmd' => 'import:users',         'path' => storage_path($this->userFile)],
                'Sagsbehandlere' => ['cmd' => 'import:sagsbehandlere', 'path' => storage_path($this->sagsbehandlerFile)],
                'Debitorer'      => ['cmd' => 'import:debitorer',      'path' => storage_path($this->debitorFile)],
                'Sager'          => ['cmd' => 'import:sager',          'path' => storage_path($this->sagerFile)],
                'Dialoger'       => ['cmd' => 'import:dialoger',       'path' => storage_path($this->dialogFile)],
            ];

            foreach ($tasks as $name => $task) {
                if (file_exists($task['path'])) {
                    $filesFound = true;
                    \Illuminate\Support\Facades\Log::info("Starter task: {$name} med fil {$task['path']}");

                    try {
                        $exitCode = Artisan::call($task['cmd'], ['file' => $task['path']]);
                        $output = trim(Artisan::output());

                        if ($exitCode === 0) {
                            $outputLog[] = "✅ {$name}: Fuldført";
                            \Illuminate\Support\Facades\Log::info("Task {$name} fuldført med succes.");
                        } else {
                            $outputLog[] = "❌ {$name}: Fejlede (Kode: {$exitCode}) - {$output}";
                            \Illuminate\Support\Facades\Log::error("Task {$name} fejlede med kode {$exitCode}. Output: {$output}");
                        }
                    } catch (\Throwable $subEx) {
                        $outputLog[] = "❌ {$name}: Fejl - " . $subEx->getMessage();
                        \Illuminate\Support\Facades\Log::error("Task {$name} kaste exception: " . $subEx->getMessage());
                    }
                } else {
                    $outputLog[] = "⚠️ {$name}: Fil ikke fundet ({$task['path']})";
                    \Illuminate\Support\Facades\Log::warning("Task {$name} - fil ikke fundet: {$task['path']}");
                }
            }

            if (!$filesFound) {
                session()->flash('error', 'Ingen af de angivne SQL-filer blev fundet i storage-mappen.');
                return;
            }

            session()->flash('success', 'Importstatus:<br>' . implode('<br>', $outputLog));
            
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Kritisk fejl i runSystemImport: ' . $e->getMessage() . ' på linje ' . $e->getLine());
            session()->flash('error', 'Kritisk fejl: ' . $e->getMessage());
        }
        (new DropdownDataSeeder())->run();
$outputLog[] = "✅ Dropdown data: Fuldført via Seeder";
    }

    public function render()
    {
        return view('imports.data-importer', [
            'templates' => ImportTemplate::where('import_type', $this->importType)->get()
        ]); // eller ->layout('components.layouts.app') afhængigt af din struktur
    }
}