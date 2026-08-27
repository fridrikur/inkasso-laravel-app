<?php

namespace App\Http\Controllers;

use App\Models\Sager;
use App\Models\Dokument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DokumenterController extends Controller
{
    public function store(Request $request, Sager $sag)
        {
            $user = auth()->user();

        if (!$user->hasAnyRole(['Admin','Medarbejder','Kreditor'])) {
            abort(403);
        }

            $request->validate([
                'file' => 'required|file|max:10240', // 10MB
            ]);

            $file = $request->file('file');

            $path = $file->store('dokumenter/' . $sag->id, 'public');

            Dokument::create([
                'sag_id'        => $sag->id,
                'file_name'     => $file->getClientOriginalName(),
                'file_path'     => $path,
                'file_size'     => $file->getSize(),
                'uploaded_date' => now(),
            ]);

            return back()->with('success', 'Dokument uploadet');
        }

        public function destroy(Dokument $dokument)
        {
            if (!auth()->user()->hasAnyRole(['Admin', 'Medarbejder'])) {
                abort(403);
            }

            Storage::disk('public')->delete($dokument->file_path);
            $dokument->delete();

            return back()->with('success', 'Dokument slettet');
        }

        public function index(Sager $sag)
        {
            $dokumenter = $sag->dokumenter()->latest()->get();

            return view('sager.dokumenter.index', compact('sag', 'dokumenter'));
        }

        public function download(Sager $sag, Dokument $dokument)
        {
            $user = auth()->user();
            
            if (!$user->hasAnyRole(['Admin', 'Medarbejder', 'Kreditor'])) {
                abort(403);
            }

            // Ekstra sikkerhed: Tjek at dokumentet rent faktisk tilhører den pågældende sag
            if ($dokument->sag_id !== $sag->id) {
                abort(404);
            }

            if (!Storage::disk('public')->exists($dokument->file_path)) {
                abort(404, 'Filen blev ikke fundet.');
            }

            return Storage::disk('public')->download($dokument->file_path, $dokument->file_name);
        }

        public function downloadAll(Sager $sag)
        {
            $user = auth()->user();
            
            if (!$user->hasAnyRole(['Admin', 'Medarbejder', 'Kreditor'])) {
                abort(403);
            }

            $dokumenter = $sag->dokumenter;

            if ($dokumenter->isEmpty()) {
                return back()->with('error', 'Ingen dokumenter at downloade.');
            }

            // Opret et unikt navn til den midlertidige zip-fil
            $zipFileName = 'sag_' . ($sag->sagsnr ?? $sag->id) . '_dokumenter.zip';
            $zipPath = storage_path('app/public/' . $zipFileName);

            $zip = new \ZipArchive();

            if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
                foreach ($dokumenter as $dok) {
                    $fullPath = storage_path('app/public/' . $dok->file_path);
                    
                    // Tjek at filen rent faktisk eksisterer på disken, før den tilføjes
                    if (file_exists($fullPath)) {
                        // Brug det originale filnavn i zip-arkivet (sørg for at undgå navnekonflikter hvis filer hedder det samme)
                        $zip->addFile($fullPath, $dok->file_name);
                    }
                }
                $zip->close();
            } else {
                return back()->with('error', 'Kunne ikke oprette zip-arkiv.');
            }

            // Send zip-filen til download og slet den midlertidigt fra serveren bagefter
            return response()->download($zipPath)->deleteFileAfterSend(true);
        }

}