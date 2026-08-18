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
}