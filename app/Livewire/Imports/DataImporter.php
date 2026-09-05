<?php

namespace App\Livewire\Imports;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\ImportTemplate;
use App\Models\Sager;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File; 
use Throwable;
use Illuminate\Support\Facades\Artisan;
use Database\Seeders\DropdownDataSeeder;

class DataImporter extends Component
{
    use WithFileUploads;

    public string $userFile = 'users.sql';
    public string $debitorFile = 'debitors.sql';
    public string $sagerFile = 'dkgsager.sql';
    public string $kreditorFile = 'kreditorer.sql';
    public string $konsulentFile = 'konsulenter.sql';
    public string $sagsbehandlerFile = 'sagsbehandlere.sql';    
    public string $dialogFile = 'dialoger.sql';
    public string $tokenFile = 'token.sql';
    
    public string $importType = 'sager';
    public $file;
    public array $sourceColumns = [];
    public array $previewRows = [];
    public array $targetFields = [];
    public $mapping = [
        'sagsnr'             => 'sagsnr',
        'afsluttet'          => 'afsluttet',
        'faktureret'         => 'faktureret',
        'betalt'             => 'betalt',
        'fakturadato'        => 'fakturadato',
        'modtaget'           => 'modtaget',
        'senesterapport'     => 'senesterapport',
        'opgivet'            => 'opgivet',
        'fakturanr'          => 'fakturanr',
        'hovedstol'          => 'hovedstol',
        'renter'             => 'renter',
        'gebyr'              => 'gebyr',
        'ialt'               => 'ialt',
        'startgebyr'         => 'startgebyr',
        'restgaeld_dkg'      => 'statistik',
        'indbetalt'          => 'indbetalt',
        'n_mdlydelse'        => 'n_mdlydelse',
        'stelnr'             => 'stelnr',
        'aktiv'              => 'aktiv',
        'kode'               => 'kode',
        'restgaeld_kreditor' => 'restgaeld',
        'kreditor_id'        => 'kreditorID',
        'debitor_id'         => 'debitorid',
        'token_id'           => 'pnummer',
        'status_id'          => 'status',
        'sagsbehandler_id'   => 'sagsbehandler',
        'konsulent_id'       => 'konsulentid',
        'ktr_id'             => 'ktr',
        'afslutning_id'      => 'afleveret',
        'udlaeg_id'          => 'finanseringstypeID',
        'bemaerkning_id'     => 'bemaerkning',
    ];
    
    public ?int $selectedTemplateId = null;
    public string $templateName = '';
    public int $step = 1;
    public $importTemplateFile;
    public string $importNotes = '';

    public bool $isImportingDialogs = false;
    public string $dialogImportMessage = '';
    public int $dialogImportProgress = 0;

    public bool $mappingApproved = false;

    public bool $isImportingSystem = false;
    public string $systemImportMessage = '';
    public int $systemImportProgress = 0;

    public string $systemFlashMessage = '';
    public string $systemFlashType = 'success';

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

            $targetArray['kreditor_id']      = 'Kreditor (Lotus ID)';
            $targetArray['debitor_id']       = 'Debitor (ID)';
            $targetArray['token_id']         = 'Tokens (ID)';
            $targetArray['status_id']        = 'Status (Forkortelse, f.eks. I, D, S)';
            $targetArray['sagsbehandler_id'] = 'Sagsbehandler (ID)';
            $targetArray['konsulent_id']     = 'Konsulent (ID)';
            $targetArray['ktr_id']           = 'KTR (Forkortelse, f.eks. K, E, L)';
            $targetArray['afslutning_id']    = 'Afslutning (Forkortelse, f.eks. b, a, o)';
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

        $this->sourceColumns = [
            'id', 'sagsnr', 'kreditorID', 'debitorid', 'afsluttet', 'faktureret', 
            'betalt', 'sagsbehandler', 'hovedstol', 'renter', 'gebyr', 'ialt', 
            'fakturadato', 'fakturanr', 'modtaget', 'startgebyr', 'restgaeld', 
            'afdragsordning', 'boligkode', 'lejemaal', 'kode', 'konsulentid', 
            'statistik', 'senesterapport', 'indbetalt', 'finanseringstypeID', 
            'aktiv', 'afleveret', 'pnummer', 'mdlydelse', 'ktr', 'stelnr', 
            'bogholderi', 'historik', 'klient_info', 'restance_info', 'opgivet', 
            'n_mdlydelse', 'fuldmagt', 'aktivt', 'lukket', 'status', 'bemaerkning'
        ];

        if (empty($this->mapping)) {
            foreach ($this->targetFields as $targetKey => $label) {
                if (in_array($targetKey, $this->sourceColumns)) {
                    $this->mapping[$targetKey] = $targetKey;
                    continue;
                }

                $cleanTarget = str_replace('_id', 'id', $targetKey);
                foreach ($this->sourceColumns as $sourceCol) {
                    if (strtolower($cleanTarget) === strtolower($sourceCol)) {
                        $this->mapping[$targetKey] = $sourceCol;
                        break;
                    }
                }
            }
        }
        
        $this->mappingApproved = false;
    }

    public function updatedFile()
    {
        $this->validate([
            'file' => 'required|mimes:csv,txt|max:10240',
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
            session()->flash('error', 'Kunne ikke læse kolonner fra filen.');
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
            $this->mappingApproved = true;
            
            session()->flash('success', "Skabelon '{$template->name}' blev indlæst og godkendt.");
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
                    if (empty(array_filter($row))) continue;

                    $dataToInsert = [];
                    $resolvedRelations = [];
                    $pnummerValue = null; 
                    $rowHasError = false;
                    $rowErrors = [];

                    foreach ($this->mapping as $targetField => $sourceColumnName) {
                        if (empty($sourceColumnName)) continue;

                        $sourceIndex = array_search($sourceColumnName, $header);
                        if ($sourceIndex !== false && isset($row[$sourceIndex])) {
                            $value = trim($row[$sourceIndex]);
                            if ($value === '') continue;

                            if ($targetField === 'token_id') {
                                $pnummerValue = $value;
                                continue; 
                            }

                            if (isset($relationRules[$targetField])) {
                                $rule = $relationRules[$targetField];
                                $cacheKey = "{$rule['lookup_table']}_{$value}";

                                if (!isset($lookupCache[$cacheKey])) {
                                    $lookupCache[$cacheKey] = DB::table($rule['lookup_table'])
                                        ->where($rule['lookup_col'], $value)
                                        ->first();
                                }

                                $relatedRecord = $lookupCache[$cacheKey];

                                if (!$relatedRecord) {
                                    $rowErrors[] = "Række $rowNumber: Kunne ikke finde match i '{$rule['lookup_table']}' for værdien '$value'.";
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

                    if ($this->importType === 'sager' && !empty($pnummerValue)) {
                        $tokenRecord = DB::table('tokens')->where('pnummer', $pnummerValue)->first();
                        if ($tokenRecord) {
                            DB::table('sager_tokens')->insertIgnore([
                                'sag_id' => $sagId,
                                'token_id' => $tokenRecord->id,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }

                    $importedCount++;
                }
            });

            fclose($stream);

            if (!empty($globalErrors)) {
                session()->flash('error', 'Import gennemført med fejl:<br>' . implode('<br>', $globalErrors));
                if ($importedCount > 0) $this->step = 3;
                return;
            }

            if ($importedCount > 0) {
                $this->step = 3;
                session()->flash('success', "Succes! $importedCount sager blev importeret.");
            } else {
                session()->flash('error', 'Ingen data blev importeret.');
            }

        } catch (Throwable $e) {
            session()->flash('error', 'Databasefejl under import: ' . $e->getMessage());
        }
    }

    public function exportTemplate($id)
    {
        $template = ImportTemplate::where('id', $id)->where('user_id', auth()->id())->first();
        if (!$template) return;

        $data = ['name' => $template->name, 'import_type' => $template->import_type, 'mapping' => $template->mapping];
        $filename = \Illuminate\Support\Str::slug($template->name) . '-template.json';

        return response()->streamDownload(fn() => print(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)), $filename);
    }

    public function importTemplate()
    {
        $this->validate(['importTemplateFile' => 'required|file|mimes:json,txt|max:2048']);

        try {
            $content = file_get_contents($this->importTemplateFile->getRealPath());
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
            session()->flash('success', 'Skabelon importeret fra JSON!');
        } catch (\Exception $e) {
            session()->flash('error', 'Fejl: ' . $e->getMessage());
        }
    }

    public function generateNotes()
    {
        $activeMapping = array_filter($this->mapping);
        $this->importNotes = "Import-konfiguration for '{$this->importType}':\n- Parrede kolonner: " . count($activeMapping);
    }
    
    public function runSystemImport()
    {
        $statusFile = storage_path('app/system_import_status.json');
        File::put($statusFile, json_encode(['status' => 'running', 'progress' => 5, 'message' => 'Starter system-import...']));

        $this->isImportingSystem = true;
        $this->systemImportMessage = 'Starter system-import...';
        $this->systemImportProgress = 5;

        $artisanPath = base_path('artisan');
        
        $cmd = sprintf(
            'php %s import:system --user=%s --kreditor=%s --konsulent=%s --sagsbehandler=%s --debitor=%s --sager=%s > /dev/null 2>&1 &',
            escapeshellarg($artisanPath),
            escapeshellarg($this->userFile),
            escapeshellarg($this->kreditorFile),
            escapeshellarg($this->konsulentFile),
            escapeshellarg($this->sagsbehandlerFile),
            escapeshellarg($this->debitorFile),
            escapeshellarg($this->sagerFile)
        );

        exec($cmd);
    }

    public function startBackgroundDialogImport()
    {
        $filePath = storage_path($this->dialogFile);
        $tokenPath = storage_path($this->tokenFile);
        $statusFile = storage_path('app/import_status.json');

        if (!file_exists(dirname($statusFile))) {
            mkdir(dirname($statusFile), 0755, true);
        }

        file_put_contents($statusFile, json_encode([
            'status' => 'running', 
            'progress' => 5, 
            'message' => 'Starter baggrundsimport...'
        ]));

        $this->isImportingDialogs = true;
        $this->dialogImportMessage = 'Starter import af dialoger i baggrunden...';
        $this->dialogImportProgress = 5;

        $artisanPath = base_path('artisan');
        
        $cmd = sprintf(
            'php %s import:dialoger --file=%s --token-file=%s > /dev/null 2>&1 &', 
            escapeshellarg($artisanPath), 
            escapeshellarg($filePath), 
            escapeshellarg($tokenPath)
        );
        
        exec($cmd);
    }

    public function approveMapping()
    {
        $this->validate([
            'mapping' => 'required|array',
        ]);

        if (empty(array_filter($this->mapping))) {
            session()->flash('error', 'Du skal mindst parre én kolonne, før du kan godkende mappingen.');
            return;
        }

        $this->mappingApproved = true;
        session()->flash('success', 'Mapping er godkendt! Du kan nu fortsætte til import.');
    }

    public function resetMappingApproval()
    {
        $this->mappingApproved = false;
    }

    public function checkDialogImportStatus()
    {
        $statusFile = storage_path('app/import_status.json');
        if (!file_exists($statusFile)) return;

        $data = json_decode(file_get_contents($statusFile), true);
        $this->dialogImportMessage = $data['message'] ?? '';
        $this->dialogImportProgress = $data['progress'] ?? 0;

        if (($data['status'] ?? '') === 'completed') {
            $this->isImportingDialogs = false;
            session()->flash('success', '🎉 Dialoger og tokens blev importeret succesfuldt!');
        } elseif (($data['status'] ?? '') === 'error') {
            $this->isImportingDialogs = false;
            session()->flash('error', 'Fejl ved baggrundsimport: ' . $this->dialogImportMessage);
        }
    }

    public function checkSystemImportStatus()
    {
        $statusFile = storage_path('app/system_import_status.json');
        if (!file_exists($statusFile)) return;

        $data = json_decode(file_get_contents($statusFile), true);
        $this->systemImportMessage = $data['message'] ?? '';
        $this->systemImportProgress = $data['progress'] ?? 0;

        if (($data['status'] ?? '') === 'completed') {
            $this->isImportingSystem = false;
            $this->systemFlashMessage = '🎉 Komplet system-import fuldført!';
            $this->systemFlashType = 'success';
            $this->dispatch('$refresh');
        } elseif (($data['status'] ?? '') === 'error') {
            $this->isImportingSystem = false;
            $this->systemFlashMessage = 'Fejl ved system-import: ' . $this->systemImportMessage;
            $this->systemFlashType = 'error';
            $this->dispatch('$refresh');
        }
    }
    
    public function render()
    {
        return view('imports.data-importer', [
            'templates' => ImportTemplate::where('import_type', $this->importType)->get()
        ]);
    }
}