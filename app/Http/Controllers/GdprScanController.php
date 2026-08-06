<?php

namespace App\Http\Controllers;

use App\Services\Gdpr\SagerGdprService;

class GdprScanController extends Controller
{
    public function run(SagerGdprService $service)
    {
        $result = $service->scan();

        // Store FULL collections (or IDs if you prefer)
        \App\Models\GdprScan::create([
            'expired' => $result['expired']->pluck('id')->toArray(),
            'expiring' => $result['expiring']->pluck('id')->toArray(),
        ]);

        return redirect()->route('gdpr.sager.retention');
    }
}