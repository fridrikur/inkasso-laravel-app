<?php

namespace App\Http\Controllers\Sager;

use App\Http\Controllers\Controller;
use App\Models\Kreditorer;

class ImportFormController extends Controller
{
    public function show(Kreditorer $kreditor)
    {
        return view('sager.import.form', [
            'kreditor' => $kreditor,
        ]);
    }
}
