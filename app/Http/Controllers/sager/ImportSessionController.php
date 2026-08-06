<?php

namespace App\Http\Controllers\Sager;

use App\Http\Controllers\Controller;
use App\Models\ImportSession;
use Illuminate\Support\Facades\DB;

class ImportSessionController extends Controller
{
    public function show(ImportSession $importSession)
    {
        return view('sager.import.session', [
            'session' => $importSession,
            'failedRows' => $importSession->meta['failed_rows'] ?? [],
        ]);
    }

    public function rollback(ImportSession $importSession)
    {
        DB::transaction(function () use ($importSession) {

            // Delete related sager
            $importSession->sager()->delete();

            // Update session
            $importSession->update([
                'status' => 'rolled_back',
                'meta' => array_merge($importSession->meta ?? [], [
                    'rolled_back_at' => now()->toDateTimeString(),
                    'rolled_back_by' => auth()->user()->id ?? null,
                ]),
            ]);
        });

        return redirect()
    ->route('sager.import.session', $importSession)
    ->with('success', 'Import er rullet tilbage');
    }
}
