<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

/**
 * AddStorageToView Middleware
 * 
 * Menambahkan informasi storage user ke shared view data
 * sehingga bisa diakses di semua blade template
 */
class AddStorageToView
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            $proExpiryReminder = null;

            if (method_exists($user, 'shouldShowProExpiryReminder') && $user->shouldShowProExpiryReminder()) {
                $proExpiryReminder = [
                    'remaining_days' => max(0, (int) $user->getProRemainingDays()),
                    'expired_at' => optional($user->pro_until)?->format('d M Y H:i'),
                ];
            }
             
            // Tambahkan storage info ke shared view data
            view()->share([
                'userStorageInfo' => $user->getStorageInfo(),
                'userStoragePercentage' => $user->getStoragePercentage(),
                'proExpiryReminder' => $proExpiryReminder,
            ]);
        }

        return $next($request);
    }
}
