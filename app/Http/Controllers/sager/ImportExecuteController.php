<?php

namespace App\Http\Controllers\sager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kreditorer;
use App\Models\Sager;
use App\Models\Debitorer;
use App\Models\ImportSession;
use App\Models\ImportMappingTemplate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportExecuteController extends Controller
{
    /**
     * TRIN 1: Vis mapping-siden med total antal sager til loading-indikatoren
     */
    public function previewMapping(Kreditorer $kreditor, Request $request)
    {
        $filePath = $request->input('file_path');

        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('temp-imports');
        }

        if (!$filePath || !Storage::exists($filePath)) {
            return redirect()->route('sager.import.form', $kreditor)
                ->with('error', 'Filen til import kunne ikke findes.');
        }

        $duplicateAction = $request->input('duplicate_action', 'keep');
        $fullPath = Storage::path($filePath);
        
        $headers = $this->extractHeaders($fullPath);
        $dataRows = $this->extractDataRows($fullPath);
        $totalRows = count($dataRows);

        if (empty($headers) || $totalRows === 0) {
            Storage::delete($filePath);
            return redirect()->route('sager.import.form', $kreditor)
                ->with('error', 'Filen indeholder ingen overskrifter eller sagsrækker.');
        }

        $templates = ImportMappingTemplate::where('kreditor_id', $kreditor->id)
            ->orWhereNull('kreditor_id')
            ->get();

        $autoMapping = $this->mapHeadersToColumns($headers);

        return view('sager.import.mapping', [
            'kreditor'        => $kreditor,
            'filePath'        => $filePath,
            'duplicateAction' => $duplicateAction,
            'headers'         => $headers,
            'totalRows'       => $totalRows,
            'autoMapping'     => $autoMapping,
            'templates'       => $templates,
        ]);
    }

    /**
     * TRIN 2: Kør den faktiske import ud fra Admin's kolonne-parring
     */
    public function run(Kreditorer $kreditor, Request $request)
    {
        $filePath = $request->input('file_path');
        $duplicateAction = $request->input('duplicate_action', 'keep'); // keep, replace, skip
        $userMapping = $request->input('mapping', []); // Formats: [col_index => field_name]

        if (!$filePath || !Storage::exists($filePath)) {
            return redirect()->route('sager.import.form', $kreditor)
                ->with('error', 'Filen til import kunne ikke findes.');
        }

        // Tjek om sagsnr er mapped
        if (!in_array('sagsnr', $userMapping)) {
            return redirect()->back()
                ->with('error', 'Du skal vælge hvilken kolonne der indeholder Sagsnummer / Kontraktnr.');
        }

        // Gem skabelon hvis valgt
        if ($request->boolean('save_template') && $request->filled('template_name')) {
            $templateMapping = array_flip(array_filter($userMapping));

            ImportMappingTemplate::create([
                'kreditor_id' => $kreditor->id,
                'navn'        => $request->input('template_name'),
                'mapping'     => $templateMapping,
            ]);
        }

        $fullPath = Storage::path($filePath);
        $headers = $this->extractHeaders($fullPath);
        $dataRows = $this->extractDataRows($fullPath);

        // OPRET IMPORT SESSION RECORD
        $session = ImportSession::create([
            'kreditor_id' => $kreditor->id,
            'file_path'   => $filePath,
            'inserted'    => 0,
            'failed'      => 0,
        ]);

        $insertedCount = 0;
        $updatedCount = 0;
        $skippedCount = 0;
        $failedRows = [];

        DB::beginTransaction();

        try {
            foreach ($dataRows as $rowIndex => $row) {
                $rowNumber = $rowIndex + 2;

                // Byg $mappedData ud fra Admin's valgte parring samt opsaml ekstra (unmappede) kolonner
                $mappedData = [];
                $extraCsvData = [];

                foreach ($row as $colIndex => $cellValue) {
                    $cellValueTrimmed = trim((string)$cellValue);
                    $fieldName = $userMapping[$colIndex] ?? null;

                    if (!empty($fieldName)) {
                        $mappedData[$fieldName] = $cellValueTrimmed;
                    } else {
                        // Kolonne uden fast model-felt: Tilføjes til ekstra data, hvis der er indhold
                        $headerName = $headers[$colIndex] ?? null;
                        if ($headerName && $cellValueTrimmed !== '') {
                            $extraCsvData[$headerName] = $cellValueTrimmed;
                        }
                    }
                }

                $sagsnr = $mappedData['sagsnr'] ?? null;
                if ($sagsnr) {
                    $sagsnr = preg_replace('/\.0+$/', '', (string)$sagsnr);
                    $mappedData['sagsnr'] = $sagsnr;
                }

                if (empty($sagsnr)) {
                    $failedRows[] = [
                        'row'    => $rowNumber,
                        'sagsnr' => '-',
                        'reason' => 'Sagsnummer mangler i rækken',
                    ];
                    continue;
                }

                // 🟢 Sammenflet Aktiv (Bil Mærke) og Reg.nr hvis begge er angivet
                if (!empty($mappedData['reg_nr'])) {
                    $bilMaerke = $mappedData['aktiv'] ?? '';
                    $regNr = $mappedData['reg_nr'];

                    if (!empty($bilMaerke)) {
                        $mappedData['aktiv'] = "{$bilMaerke} (Reg.nr: {$regNr})";
                    } else {
                        $mappedData['aktiv'] = "Reg.nr: {$regNr}";
                    }
                }

                // Udskil felter til Sager-modellen (inkl. aktiv og kort_bemaerkning)
                $sagFields = [
                    'sagsnr', 'hovedstol', 'restgaeld_dkg', 'n_mdlydelse', 'renter',
                    'gebyr', 'indbetalt', 'stelnr', 'fakturanr', 'modtaget', 'kort_bemaerkning', 'aktiv'
                ];

                $sagData = array_intersect_key($mappedData, array_flip($sagFields));
                $this->formatNumericFields($sagData);
                $sagData['modtaget'] = $sagData['modtaget'] ?? now();

                // Tjek for eksisterende sag med samme sagsnummer
                $existingSag = Sager::where('sagsnr', $sagsnr)->first();
                $sag = null;

                if ($existingSag) {
                    if ($duplicateAction === 'skip') {
                        $skippedCount++;
                        continue;
                    }

                    if ($duplicateAction === 'replace') {
                        $existingSag->update($sagData);

                        if (method_exists($existingSag, 'importSessions')) {
                            $existingSag->importSessions()->syncWithoutDetaching([$session->id]);
                        }

                        $sag = $existingSag;
                        $updatedCount++;
                    }
                }

                // Hvis det er en ny sag eller duplicateAction = 'keep'
                if (!$sag) {
                    $sag = Sager::create($sagData);

                    if (method_exists($sag, 'sagerkreditor')) {
                        $sag->sagerkreditor()->syncWithoutDetaching([$kreditor->id]);
                    }

                    if (method_exists($sag, 'importSessions')) {
                        $sag->importSessions()->syncWithoutDetaching([$session->id]);
                    }

                    $insertedCount++;
                }

                // Opret/opdater debitorer
                $this->handleDebitorImport($sag, $mappedData, 'debitor', 'hoveddebitor');
                $this->handleDebitorImport($sag, $mappedData, 'meddebitor', 'meddebitor');

                // 🟢 Håndter KTR (Kontrakttype) via pivot sager_ktr
                if (!empty($mappedData['ktr'])) {
                    $this->handleKtrImport($sag, $mappedData['ktr']);
                }

                // 🟢 Opret den præsentable import-boble i Klientinformation
                $this->createKlientinfoImportBubble($sag, $extraCsvData);
            }

            $session->update([
                'inserted' => $insertedCount,
                'failed'   => count($failedRows),
                'status'   => 'completed',
                'meta'     => array_merge($session->meta ?? [], [
                    'failed_rows'   => $failedRows,
                    'skipped_count' => $skippedCount,
                    'updated_count' => $updatedCount,
                ]),
            ]);

            DB::commit();

            Storage::delete($filePath);

            $session->refresh();
            $session->load('kreditor');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Import execute error: " . $e->getMessage());

            $session->update([
                'status' => 'failed',
            ]);

            return redirect()->route('sager.import.form', $kreditor)
                ->with('error', 'Der opstod en fejl under importen: ' . $e->getMessage());
        }

        return view('sager.import.completed', [
            'kreditor'     => $kreditor,
            'session'      => $session,
            'inserted'     => $insertedCount,
            'updated'      => $updatedCount,
            'skippedCount' => $skippedCount,
            'failedRows'   => $failedRows,
        ]);
    }

    /**
     * 🟢 OPBYGGER OG INDSÆTTER EN PRÆSENTABEL IMPORT-BOBLE I KLIENTINFORMATION
     */
    private function createKlientinfoImportBubble(Sager $sag, array $extraCsvData): void
    {
        if (empty($extraCsvData)) {
            return;
        }

        $bubbleText = "📊 EKSTRA IMPORT-OPLYSNINGER FRA CSV:\n";
        $bubbleText .= "----------------------------------------\n";

        foreach ($extraCsvData as $columnHeader => $value) {
            $prettyLabel = Str::headline($columnHeader);
            $bubbleText .= "• {$prettyLabel}: {$value}\n";
        }

        try {
            // Opret eller hent Klientinformation-dialogen
            $dialog = $sag->dialogs()->firstOrCreate([
                'type' => 'klientinformation',
            ]);

            // Indsæt beskeden i dialogstrømmen med den aktuelle brugers ID
            $dialog->messages()->create([
                'sender_id' => auth()->id() ?? 1,
                'tekst'     => trim($bubbleText),
                'dato'      => now(),
            ]);
        } catch (\Exception $e) {
            Log::error("Kunne ikke oprette klientinformation import-boble for sag #{$sag->sagsnr}: " . $e->getMessage());
        }
    }

    /**
     * Opretter/opdaterer debitor i Debitorer-modellen ud fra 'pnr' (CPR)
     */
    private function handleDebitorImport(Sager $sag, array $mappedData, string $prefix, string $rolle): void
    {
        try {
            $cpr = $mappedData["{$prefix}_cpr"] ?? null;
            $navn = $mappedData["{$prefix}_navn"] ?? null;

            if (empty($cpr) && empty($navn)) {
                return;
            }

            if ($cpr) {
                $cpr = preg_replace('/\.0+$/', '', trim((string)$cpr));
                if (strlen($cpr) === 9) {
                    $cpr = '0' . $cpr;
                }
            }

            $debitorData = array_filter([
                'pnr'     => $cpr,
                'navn'    => $navn,
                'adresse' => $mappedData["{$prefix}_adresse"] ?? null,
                'postnr'  => !empty($mappedData["{$prefix}_postnr"]) ? preg_replace('/\.0+$/', '', trim((string)$mappedData["{$prefix}_postnr"])) : null,
                'email'   => $mappedData["{$prefix}_email"] ?? null,
                'tlf'     => !empty($mappedData["{$prefix}_tlf"]) ? preg_replace('/\.0+$/', '', trim((string)$mappedData["{$prefix}_tlf"])) : null,
                'mobil'   => !empty($mappedData["{$prefix}_mobil"]) ? preg_replace('/\.0+$/', '', trim((string)$mappedData["{$prefix}_mobil"])) : null,
            ], fn($val) => $val !== null && $val !== '');

            if (empty($debitorData) || !class_exists(Debitorer::class)) {
                return;
            }

            $debitor = null;
            if (!empty($cpr)) {
                $debitor = Debitorer::where('pnr', $cpr)->first();
            }
            if (!$debitor && !empty($navn)) {
                $debitor = Debitorer::where('navn', $navn)->first();
            }

            if ($debitor) {
                $debitor->update($debitorData);
            } else {
                $debitor = Debitorer::create($debitorData);
            }

            if ($debitor && method_exists($sag, 'sagerdebitor')) {
                $pivotData = [];
                if (Schema::hasColumn('sager_debitor', 'rolle')) {
                    $pivotData['rolle'] = $rolle;
                }
                $sag->sagerdebitor()->syncWithoutDetaching([$debitor->id => $pivotData]);
            }
        } catch (\Exception $e) {
            Log::error("Fejl i handleDebitorImport ({$prefix}) for sag #{$sag->sagsnr}: " . $e->getMessage());
        }
    }

    private function extractHeaders(string $fullPath): array
    {
        $handle = fopen($fullPath, 'rb');
        $fileHeader = fread($handle, 4);
        fclose($handle);

        if ($fileHeader === "PK\x03\x04") {
            $spreadsheet = IOFactory::load($fullPath);
            $worksheet = $spreadsheet->getActiveSheet();
            $allRows = $worksheet->toArray(null, true, true, false);
            return !empty($allRows) ? array_shift($allRows) : [];
        } else {
            $delimiter = $this->detectDelimiter($fullPath);
            $file = fopen($fullPath, 'r');
            $data = fgetcsv($file, 2048, $delimiter);
            fclose($file);
            return $data ?: [];
        }
    }

    private function extractDataRows(string $fullPath): array
    {
        $handle = fopen($fullPath, 'rb');
        $fileHeader = fread($handle, 4);
        fclose($handle);

        if ($fileHeader === "PK\x03\x04") {
            $spreadsheet = IOFactory::load($fullPath);
            $worksheet = $spreadsheet->getActiveSheet();
            $allRows = $worksheet->toArray(null, true, true, false);
            array_shift($allRows); // Fjern overskrifter
            return array_filter($allRows, fn($row) => !empty(array_filter($row, fn($cell) => $cell !== null && trim((string)$cell) !== '')));
        } else {
            $delimiter = $this->detectDelimiter($fullPath);
            $file = fopen($fullPath, 'r');
            fgetcsv($file, 2048, $delimiter); // Fjern overskrifter
            $rows = [];
            while (($data = fgetcsv($file, 2048, $delimiter)) !== false) {
                if (!empty(array_filter($data, fn($cell) => trim((string)$cell) !== ''))) {
                    $rows[] = $data;
                }
            }
            fclose($file);
            return $rows;
        }
    }

    private function detectDelimiter(string $filePath): string
    {
        $handle = fopen($filePath, 'r');
        $firstLine = fgets($handle);
        fclose($handle);
        return (!$firstLine || substr_count($firstLine, ';') >= substr_count($firstLine, ',')) ? ';' : ',';
    }

    private function mapHeadersToColumns(array $headers): array
    {
        $mapping = [];
        $knownAliases = [
            'sagsnr'          => ['sagsnr', 'sagsnummer', 'kontraktnr', 'kontraktnummer', 'sag_id', 'sags nr', 'sags nr.', 'kundenr'],
            'ktr'             => ['kontrakttype', 'kontrakt type', 'ktr', 'kontrakttype:', 'kontrakttype_navn'],
            'aktiv'           => ['aktiv', 'bil mærke', 'bil_mærke', 'genstand'],
            'reg_nr'          => ['reg nr.', 'reg. nr.', 'reg nr', 'reg.nr.', 'reg_nr', 'registreringsnummer', 'nummerplade'],
            'kort_bemaerkning'=> ['bemærkninger', 'bemaerkninger', 'kort_bemaerkning', 'bemærkning', 'bemaerkning', 'bemærkninger:'],
            'hovedstol'       => ['hovedstol', 'beløb', 'belob', 'krav', 'saldo', 'udestående_balance', 'udestående balance', 'udestående'],
            'restgaeld_dkg'   => ['total_restance', 'total restance', 'restance', 'restgaeld_dkg'],
            'n_mdlydelse'     => ['installment', 'ydelse', 'afdrag', 'n_mdlydelse'],
            'renter'          => ['renter', 'rente'],
            'gebyr'           => ['gebyr', 'gebyrer', 'omkostninger'],
            'indbetalt'       => ['indbetalt', 'afdraget'],
            'stelnr'          => ['stelnr', 'stelnummer', 'vin'],
            'fakturanr'       => ['fakturanr', 'fakturanummer'],
            'debitor_cpr'     => ['cpr_hoveddebitor', 'debitor_cpr', 'cpr', 'cvr', 'cpr/cvr'],
            'debitor_navn'    => ['navn_hoveddebitor', 'debitor_navn', 'debitor', 'navn'],
            'debitor_adresse' => ['adresse_hoveddebitor', 'debitor_adresse', 'adresse'],
            'debitor_by'      => ['by_hoveddebitor', 'debitor_by', 'by'],
            'debitor_postnr'  => ['postnummer_hoveddebitor', 'postnr_hoveddebitor', 'debitor_postnr', 'postnr', 'postnummer'],
            'debitor_tlf'     => ['tlf_hoveddebitor', 'debitor_tlf', 'telefon', 'tlf'],
            'debitor_mobil'   => ['mobil_hoveddebitor', 'debitor_mobil', 'mobil'],
            'debitor_email'   => ['mailadr_hoveddebitor', 'debitor_email', 'email', 'mail'],
            'meddebitor_cpr'  => ['cpr_meddebitor', 'meddebitor_cpr'],
            'meddebitor_navn' => ['navn_meddebitor', 'meddebitor_navn'],
        ];

        foreach ($headers as $index => $header) {
            $normalized = mb_strtolower(trim((string)$header));
            foreach ($knownAliases as $dbField => $aliases) {
                if (in_array($normalized, $aliases) && !isset($mapping[$dbField])) {
                    $mapping[$dbField] = $index;
                }
            }
        }
        return $mapping;
    }

    private function formatNumericFields(array &$data): void
    {
        foreach (['hovedstol', 'restgaeld_dkg', 'n_mdlydelse', 'renter', 'gebyr', 'indbetalt'] as $field) {
            if (isset($data[$field]) && is_string($data[$field])) {
                $val = str_replace('.', '', $data[$field]);
                $val = str_replace(',', '.', $val);
                $data[$field] = is_numeric($val) ? (float) $val : null;
            }
        }
    }

    public function rollback(ImportSession $session)
    {
        if ($session->status === 'rolled_back') {
            return redirect()->back()->with('error', 'Denne import er allerede rullet tilbage.');
        }

        DB::beginTransaction();

        try {
            if (method_exists($session, 'sager')) {
                $sager = $session->sager;

                foreach ($sager as $sag) {
                    if (method_exists($sag, 'sagerkreditor')) {
                        $sag->sagerkreditor()->detach();
                    }
                    if (method_exists($sag, 'importSessions')) {
                        $sag->importSessions()->detach();
                    }
                    if (method_exists($sag, 'sagerdebitor')) {
                        $sag->sagerdebitor()->detach();
                    }

                    $sag->delete();
                }
            }

            $session->update([
                'status' => 'rolled_back',
            ]);

            DB::commit();

            return redirect()->route('sager.import.log')
                ->with('success', "Import session #{$session->id} blev rullet tilbage, og de tilknyttede sager er blevet slettet.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Rollback error for session #{$session->id}: " . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Der opstod en fejl under tilbagerulning: ' . $e->getMessage());
        }
    }

    public function retrySession(ImportSession $session)
    {
        if (!Storage::exists($session->file_path)) {
            return redirect()->route('sager.import.session', $session)
                ->with('error', 'Den oprindelige importfil findes desværre ikke længere på serveren.');
        }

        $fullPath = Storage::path($session->file_path);
        $headers = $this->extractHeaders($fullPath);
        $dataRows = $this->extractDataRows($fullPath);
        $totalRows = count($dataRows);

        if (empty($headers) || $totalRows === 0) {
            return redirect()->route('sager.import.session', $session)
                ->with('error', 'Oprindelig fil indeholder ingen gyldige rækker.');
        }

        $templates = ImportMappingTemplate::where('kreditor_id', $session->kreditor_id)
            ->orWhereNull('kreditor_id')
            ->get();

        $autoMapping = $this->mapHeadersToColumns($headers);

        return view('sager.import.mapping', [
            'kreditor'        => $session->kreditor,
            'filePath'        => $session->file_path,
            'duplicateAction' => 'replace',
            'headers'         => $headers,
            'totalRows'       => $totalRows,
            'autoMapping'     => $autoMapping,
            'templates'       => $templates,
        ]);
    }

    public function updateTemplate(Request $request, ImportMappingTemplate $template)
    {
        $request->validate([
            'navn' => 'required|string|max:255',
            'mapping' => 'required|array',
        ]);

        $template->update([
            'navn' => $request->input('navn'),
            'mapping' => array_flip(array_filter($request->input('mapping'))),
        ]);

        return redirect()->back()->with('success', 'Skabelonen blev opdateret succesfuldt.');
    }

    public function destroyTemplate(ImportMappingTemplate $template)
    {
        $template->delete();

        return redirect()->back()->with('success', 'Skabelonen er blevet slettet.');
    }

    /**
     * Finder eller opretter KTR (Kontrakttype) og tilknytter via pivot-tabellen sager_ktr.
     * Håndterer fleksibel søgning (f.eks. "privatleasing" -> "privat leasing").
     */
    private function handleKtrImport(Sager $sag, ?string $ktrValue): void
    {
        if (empty($ktrValue)) {
            return;
        }

        $rawKtrValue = trim($ktrValue);
        
        // Normaliseret streng uden mellemrum, bindestreger og i lowercase (f.eks. "privatleasing")
        $normalizedInput = mb_strtolower(preg_replace('/[\s\-]+/', '', $rawKtrValue));

        try {
            // 1. Eksakt søgning på tekst eller forkortelse
            $ktr = \App\Models\KTR::where('tekst', $rawKtrValue)
                ->orWhere('forkortelse', $rawKtrValue)
                ->first();

            // 2. Hvis ikke fundet, søg fleksibelt i databasen ved at fjerne mellemrum og bindestreger
            if (!$ktr) {
                $allKtr = \App\Models\KTR::all();

                $ktr = $allKtr->first(function ($item) use ($normalizedInput) {
                    $dbTekst = mb_strtolower(preg_replace('/[\s\-]+/', '', (string) $item->tekst));
                    $dbForkortelse = mb_strtolower(preg_replace('/[\s\-]+/', '', (string) $item->forkortelse));

                    return $dbTekst === $normalizedInput || ($dbForkortelse !== '' && $dbForkortelse === $normalizedInput);
                });
            }

            // 3. Hvis den stadig ikke findes i databasen, oprettes den med den oprindelige tekst fra CSV
            if (!$ktr) {
                $ktr = \App\Models\KTR::create([
                    'tekst' => $rawKtrValue,
                ]);
            }

            // 4. Tilknyt til sagen via pivot-relationen sagerKtr()
            if ($ktr && method_exists($sag, 'sagerKtr')) {
                $sag->sagerKtr()->syncWithoutDetaching([$ktr->id]);
            }
        } catch (\Exception $e) {
            Log::error("Fejl i handleKtrImport for sag #{$sag->sagsnr}: " . $e->getMessage());
        }
    }
}