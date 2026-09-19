<?php
namespace App\Http\Controllers;

use App\Models\Sager;

class SagerPrintController extends Controller
{
    public function show(Sager $sag)
    {
        if (!auth()->user()->hasAnyRole(['Admin', 'Medarbejder', 'Kreditor'])) {
            abort(403);
        }

        // 🟢 Eager load alle relevante relationer
        $sag->load([
            'kreditor', 
            'debitor', 
            'sagsbehandler', 
            'konsulent', 
            'status', 
            'ktr', 
            'bemaerkning', 
            'afslutning', 
            'udlaeg'
        ]);

        return view('sager.print-view', compact('sag'));
    }
}