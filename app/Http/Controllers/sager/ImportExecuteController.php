<?php

namespace App\Http\Controllers\Sager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Sager;
use App\Models\Debitorer;
use App\Models\Kreditorer;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Throwable;
use App\Models\ImportSession;

class ImportExecuteController extends Controller
{
    public function rollback(ImportSession $session)
    {
        DB::transaction(function () use ($session) {

            // Delete only sager created by this import
            $session->sager()->each(function ($sag) {
                $sag->delete();
            });

            // Clean pivot automatically via cascade
            $session->update([
                'status' => 'rolled_back',
            ]);
        });

        return back()->with('success', 'Import rullet tilbage');
    }

    public function run(Request $request, Kreditorer $kreditor)
    {
        $failedRows = [];

        $path   = $request->file_path;
        $action = $request->duplicate_action;

        DB::beginTransaction();

        // 🧠 Create import session
        $session = ImportSession::create([
            'kreditor_id' => $kreditor->id,
            'file_path'   => $path,
            'status'      => 'running',
        ]);

        try {
            $fullPath = Storage::path($path);
            $data = Excel::toArray([], $fullPath)[0];

            $headers = array_map('trim', $data[0]);
            $rows    = array_slice($data, 1);

            $inserted = 0;
            $failed   = 0;

            foreach ($rows as $index => $row) {
                try {
                    $rowData = array_combine($headers, $row);

                    $sagsnr = trim($rowData['Kontraktnummer'] ?? '');

                    if (!$sagsnr) {
                        throw new \Exception('Manglende Kontraktnummer');
                    }
                    // existing duplicate logic untouched
                    if (Sager::where('sagsnr', $sagsnr)->exists()) {
                        if ($action === 'skip') {
                            throw new \Exception('Dublet ignoreret');
                        }

                        if ($action === 'replace') {
                            Sager::where('sagsnr', $sagsnr)->delete();
                        }

                        if ($action === 'keep') {
                            $i = 2;
                            while (Sager::where('sagsnr', "{$sagsnr} ({$i})")->exists()) {
                                $i++;
                            }
                            $sagsnr = "{$sagsnr} ({$i})";
                        }
                    }

                    $sag =  Sager::create([
                        'import_session_id' => $session->id, // 🔑
                        'sagsnr' => $sagsnr,
                        'stelnr' => 'UNKNOWN',
                        'modtaget' => now(),
                        'hovedstol' => $rowData['Udestående_Balance'] ?? 0,
                        'renter' => 0,
                        'gebyr' => 0,
                        'ialt' => 0,
                        'startgebyr' => 0,
                        'aktiv' => trim(
                            ($rowData['Mærke'] ?? '') . ' ' .
                            ($rowData['Model'] ?? '') . ' ' .
                            ($rowData['RegNr'] ?? '')
                        ),
                        'restgaeld_kreditor' => $rowData['Total_Restance'] ?? null,
                        'senesterapport' => !empty($rowData['Seneste_paragraf_10'])
                            ? Carbon::createFromFormat('d.m.Y', $rowData['Seneste_paragraf_10'])->format('Y-m-d')
                            : null,
                    ]);
                    // 🔗 Attach to import session
                    $session->sager()->attach($sag->id);

                    $debitorNavn = trim($rowData['Navn_Hoveddebitor'] ?? '');

                    if (!$debitorNavn) {
                        throw new \Exception('Navn_Hoveddebitor mangler');
                    }

                    $debitor = Debitorer::firstOrCreate(
                        ['navn' => $debitorNavn]
                    );

                                                    
                    $sag->sagerkreditor()->attach($kreditor->id);
                    $sag->sagerdebitor()->attach($debitor->id);



                    $inserted++;

                } catch (Throwable $e) {
                    $failed++;

                    $failedRows[] = [
                        'row'    => $index + 2,
                        'sagsnr' => $rowData['Kontraktnummer'] ?? '—',
                        'reason' => $e->getMessage(),
                    ];
                }
            }

            // Update session stats
            $session->update([
                'inserted' => $inserted,
                'failed'   => $failed,
                'status'   => 'completed',
                'meta'     => [
                    'failed_rows' => $failedRows,
                ],
            ]);

            DB::commit();

            return view('sager.import.done', [
                'inserted'   => $inserted,
                'failed'     => $failed,
                'failedRows' => $failedRows,
                'kreditor'   => $kreditor,
                'session'    => $session,
            ]);

        } catch (Throwable $e) {
            DB::rollBack();

            $session->update([
                'status' => 'failed',
                'meta'   => ['error' => $e->getMessage()],
            ]);

            return back()->withErrors([
                'import' => 'Importen fejlede: ' . $e->getMessage(),
            ]);
        }
    }
}
