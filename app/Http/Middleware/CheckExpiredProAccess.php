<?php

namespace App\Http\Middleware;

use App\Services\StorageService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckExpiredProAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();

        if (!$user->hasExpiredProAccess()) {
            return $next($request);
        }

        StorageService::updateStorageLimit($user);

        $allowedRoutes = [
            'pro.expired',
            'premium.index',
            'pro.create-invoice',
            'pro.status',
            'pro.payment.success',
            'pro.payment.failed',
            'pro.testing.activate',
            'logout',
        ];

        if (!$request->routeIs($allowedRoutes)) {
            return redirect()->route('pro.expired');
        }

        return $next($request);
    }
}
