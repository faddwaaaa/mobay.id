<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicProfilekController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;

// LANDING PAGE
Route::get('/', function () {
    return view('landing.index');
});

// PUBLIC PROFILE
Route::get('/@{username}', [PublicProfilekController::class, 'profile']);
Route::get('/@{username}/click/{link}', [PublicProfilekController::class, 'redirect']);

// DASHBOARD (SATU SAJA)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // USER PROFILE
    Route::get('/dashboard/profile', [ProfileController::class, 'profile'])
        ->name('dashboard.profile');

    // PROFILE EDIT & UPDATE
    Route::get('/profile/edit', [ProfileController::class, 'edit'])
        ->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

// LOGOUT
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

// AUTH ROUTES
require __DIR__.'/auth.php';