<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckProfileSetup
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && !Auth::user()->profile) {
            return redirect()->route('profile.edit')
                ->with('warning', 'Please complete your profile setup first.');
        }
        
        return $next($request);
    }
}