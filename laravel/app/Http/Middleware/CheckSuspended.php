<?php
// ================================================================
// FILE: app/Http/Middleware/CheckSuspended.php  (file BARU)
// Middleware yang memblokir semua akses user yang di-suspend
// ================================================================

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckSuspended
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->is_suspended) {
            $allowedRoutes = [
                'suspended',
                'appeal.store',
                'appeal.status',
                'logout',
            ];

            // Jangan blokir route yang memang dibutuhkan user suspended
            if (!$request->routeIs($allowedRoutes)) {
                return redirect()->route('suspended');
            }
        }

        return $next($request);
    }
}
