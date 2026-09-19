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

            if ($dokument->sag_id !== $sag->id) {
                abort(404);
            }

            // Rens fil-stien, så vi fjerner evt. foranstillet '/storage/' eller 'storage/'
            $cleanPath = ltrim(str_replace('/storage', '', $dokument->file_path), '/');

            // 1. Tjek i standard Laravel storage (storage/app/public/...)
            $fullPath = storage_path('app/public/' . $cleanPath);
            if (file_exists($fullPath)) {
                return response()->download($fullPath, $dokument->file_name);
            }

            // 2. Tjek direkte på filnavnet i public/storage mappen
            $publicPath = public_path('storage/' . $cleanPath);
            if (file_exists($publicPath)) {
                return response()->download($publicPath, $dokument->file_name);
            }

            // 3. Fallback: Hvis filen ligger et helt andet sted eller bruger det rå filnavn
            $altPath = storage_path('app/public/' . $dokument->file_name);
            if (file_exists($altPath)) {
                return response()->download($altPath, $dokument->file_name);
            }

            abort(404, 'Fysisk fil blev ikke fundet på serveren (Søgt på: ' . $fullPath . ')');
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