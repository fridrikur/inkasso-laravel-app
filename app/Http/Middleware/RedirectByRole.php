<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectByRole
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return $next($request);
        }

        $user = auth()->user();

        // only redirect from root/dashboard entry
        if ($request->routeIs('dashboard')) {
            return redirect()->route($user->dashboardRoute());
        }

        return $next($request);
    }
}