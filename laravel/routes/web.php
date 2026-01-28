<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicProfileController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Auth;


// LANDING PAGE
Route::get('/', function () {
    return view('landing.index');
});

// PUBLIC PROFILE
Route::get('/@{username}', [PublicProfileController::class, 'show']);
Route::get('/@{username}/click/{link}', [PublicProfileController::class, 'redirect']);

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard.index');
    });
});

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect('/login');
})->name('logout');

// AUTH ROUTES (BREEZE)
require __DIR__.'/auth.php';
