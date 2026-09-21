<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Brev;
use App\Models\BrevFilter;

class BrevFilterController extends Controller
{
    public function index()
    {
        $breve = Brev::orderBy('brevpos', 'asc')->get();
        return view('sager.breve-filter', compact('breve'));
    }

    public function update(Request $request, $brevID)
    {
        // Hjælpefunktion til at oversætte true/false/1/-1 til 1 eller 0
        $val = fn($field) => filter_var($request->input($field, 0), FILTER_VALIDATE_BOOLEAN) || intval($request->input($field, 0)) !== 0 ? 1 : 0;

        BrevFilter::updateOrCreate(
            ['brevID' => $brevID],
            [
                'adresse'   => $val('adresse'),
                'dato'      => $val('dato'),
                'navn'      => $val('navn'),
                'sagsnr'    => $val('sagsnr'),
                'emne'      => $val('emne'),
                'skjulalle' => $val('skjulalle'),
                'visalle'   => $val('visalle'),
            ]
        );

        return response()->json(['message' => 'Brevfilter opdateret']);
    }

    public function updateSorting(Request $request)
    {
        $brevpos = $request->input('brevpos'); // Kommer som f.eks. "4,1,8,12..."

        if ($brevpos) {
            $ids = explode(',', $brevpos);
            foreach ($ids as $index => $id) {
                Brev::where('id', $id)->update(['brevpos' => $index + 1]);
            }
        }

        return response()->json(['message' => 'Brev opdateret']);
    }
}