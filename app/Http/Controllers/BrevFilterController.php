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
        BrevFilter::updateOrCreate(
            ['brevID' => $brevID],
            [
                'adresse' => $request->input('adresse', 0),
                'dato' => $request->input('dato', 0),
                'navn' => $request->input('navn', 0),
                'sagsnr' => $request->input('sagsnr', 0),
                'emne' => $request->input('emne', 0),
                'skjulalle' => $request->input('skjulalle', 0),
                'visalle' => $request->input('visalle', 0),
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