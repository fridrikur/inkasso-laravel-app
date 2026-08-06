<?php

namespace App\Http\Controllers\Sager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ImportConfirmController extends Controller
{
    public function confirm(Request $request)
    {
        $request->validate([
            'duplicate_action' => 'required|in:keep,replace,skip',
        ]);

        $import = Session::get('import');

        if (!$import) {
            abort(419);
        }

        $import['duplicate_action'] = $request->duplicate_action;
        Session::put('import', $import);

        return redirect()->route('sager.import.run');
    }
}