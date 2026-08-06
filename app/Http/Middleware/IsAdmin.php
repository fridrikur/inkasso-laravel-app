<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            abort(403, 'User is not authenticated');
        }

        if (!auth()->user()->hasRole('Admin')) {
            abort(403, 'User does not have Admin role');
        }

        return $next($request);
    }
}
