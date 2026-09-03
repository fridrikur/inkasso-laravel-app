<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\SettingsService;
use Symfony\Component\HttpFoundation\Response;

class CheckIpWhitelist
{
    public function handle(Request $request, Closure $next): Response
    {
        $settings = app(SettingsService::class);
        $whitelistString = $settings->get('allowed_ips', '');

        // Hvis feltet er helt tomt, tillader vi enten alt eller spærrer. 
        // Lad os sige, at hvis der ikke er sat nogen IP'er endnu, lader vi admin sætte dem op, 
        // eller vi blokerer, hvis der er defineret en liste.
        if (empty(trim($whitelistString))) {
            return $next($request);
        }

        // Opdel IP-adresser med komma, semikolon eller linjeskift
        $allowedIps = preg_split('/[\s,\;]+/', trim($whitelistString));
        $allowedIps = array_filter(array_map('trim', $allowedIps));

        // $clientIp = $request->ip();
        $clientIp = '8.8.8.8'; // En tilfældig ekstern IP (f.eks. Google DNS)

        // Tjek om klientens IP er på listen
        if (!in_array($clientIp, $allowedIps)) {
            // Vis en Forbidden (403) side, hvis IP'en ikke er godkendt
            abort(403, 'Adgang nægtet: Din IP-adresse (' . $clientIp . ') er ikke godkendt til at tilgå dette system.');
        }

        return $next($request);
    } 
}