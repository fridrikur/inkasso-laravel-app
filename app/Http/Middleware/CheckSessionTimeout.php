<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSessionTimeout
{
    public function handle(Request $request, Closure $next): Response
    {
        $timeout = config('session.lifetime', 15) * 60; 

        if (auth()->check()) {
            $lastActivity = session('last_activity');

            if ($lastActivity && (time() - $lastActivity > $timeout)) {
                $userId = auth()->id();

                auth()->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                session(['last_user_id' => $userId]);

                if ($request->ajax() || $request->wantsJson() || $request->header('X-Livewire')) {
                    return response()->json([
                        'message' => 'Session udløbet pga. inaktivitet',
                        'timeout' => true
                    ], 401);
                }

                return response()->json(['timeout' => true], 401);
            }

            session(['last_user_id' => auth()->id()]);

            // 🟢 Tjek om det er et wire:poll kald (ekskludér disse), men tillad almindelige Livewire-handlinger
            $isPolling = $request->header('X-Livewire') && str_contains($request->header('X-Livewire-Directive', ''), 'poll');

            if (! $isPolling) {
                session(['last_activity' => time()]);
            }
        }

        return $next($request);
    }
}