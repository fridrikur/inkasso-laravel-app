<?php

namespace App\Http\Controllers\Sager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kreditorer;

class ImportUploadController extends Controller
{
    public function upload(Request $request, Kreditorer $kreditor)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv',
        ]);

        $path = $request->file('file')->store('imports');

        return redirect()->route('sager.import.preview', [
            'path'    => $path,
            'kreditor' => $kreditor,
        ]);
    }
}
