<?php

namespace App\Http\Controllers\sager;

use App\Http\Controllers\Controller;
use App\Models\ImportSession;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImportSessionController extends Controller
{
    public function show($id)
    {
        // Indlæs kreditor og sager med tilhørende sagerdebitor-relation
        $session = ImportSession::with(['kreditor', 'sager.sagerdebitor'])->findOrFail($id);

        return view('sager.import.show', [
            'session'    => $session,
            'kreditor'   => $session->kreditor,
            'sager'      => $session->sager,
            'failedRows' => $session->meta['failed_rows'] ?? [],
        ]);
    }

    public function rollback(ImportSession $importSession)
    {
        if ($importSession->status === 'rolled_back') {
            return redirect()
                ->route('sager.import.session', $importSession)
                ->with('error', 'Denne import er allerede rullet tilbage.');
        }

        DB::beginTransaction();

        try {
            // Hent og slet tilknyttede sager samt deres pivot-relationer
            foreach ($importSession->sager as $sag) {
                if (method_exists($sag, 'sagerdebitor')) {
                    $sag->sagerdebitor()->detach();
                }
                if (method_exists($sag, 'sagerkreditor')) {
                    $sag->sagerkreditor()->detach();
                }
                if (method_exists($sag, 'importSessions')) {
                    $sag->importSessions()->detach();
                }

                $sag->delete();
            }

            // Opdatér session status og timestamp
            $importSession->update([
                'status' => 'rolled_back',
                'meta'   => array_merge($importSession->meta ?? [], [
                    'rolled_back_at' => now()->toDateTimeString(),
                    'rolled_back_by' => auth()->id() ?? null,
                ]),
            ]);

            DB::commit();

            return redirect()
                ->route('sager.import.session', $importSession)
                ->with('success', 'Importen blev rullet tilbage, og sagerne er blevet slettet.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Rollback error for import session #{$importSession->id}: " . $e->getMessage());

            return redirect()
                ->route('sager.import.session', $importSession)
                ->with('error', 'Der opstod en fejl under tilbagerulningen: ' . $e->getMessage());
        }
    }
}