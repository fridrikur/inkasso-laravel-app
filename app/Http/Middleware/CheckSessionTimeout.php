<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSessionTimeout
{
    public function handle(Request $request, Closure $next): Response
    {
        // Omregn minutter fra session config til sekunder
        $timeout = config('session.lifetime', 15) * 60; 

        if (auth()->check()) {
            $lastActivity = session('last_activity');

            if ($lastActivity && (time() - $lastActivity > $timeout)) {
                // 1. Gem altid seneste bruger-ID, så /re-authenticate kan genkende brugeren
                $userId = auth()->id();

                auth()->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                // Husk bruger-ID i den nye, ryddede session
                session(['last_user_id' => $userId]);

                // 2. Returnér 401 for ALLE forespørgsler ved timeout (også almindelige navigationskald)
                // Det tvinger frontenden/Livewire til at åbne modalen i stedet for et hårdt sideoverskift!
                if ($request->ajax() || $request->wantsJson() || $request->header('X-Livewire')) {
                    return response()->json([
                        'message' => 'Session udløbet pga. inaktivitet',
                        'timeout' => true
                    ], 401);
                }

                // Hvis det er en fuld side-indlæsning, kan vi enten sende 401 eller lade den åbne siden med den låste modal
                return response()->json(['timeout' => true], 401);
            }

            // Gem løbende last_user_id mens brugeren er aktiv
            session(['last_user_id' => auth()->id()]);

            // Opdater KUN tidsstemplet ved almindelige sidevisninger (ikke ved wire:poll)
            if (! $request->ajax() && ! $request->header('X-Livewire')) {
                session(['last_activity' => time()]);
            }
        }

        return $next($request);
    }
}