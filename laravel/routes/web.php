<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicProfileController;
use App\Http\Controllers\DashboardController;


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

// AUTH ROUTES (BREEZE)
require __DIR__.'/auth.php';
