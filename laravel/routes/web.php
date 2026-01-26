<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicProfileController;

// LANDING PAGE
Route::get('/', function () {
    return view('landing.index');
});

// PUBLIC PROFILE
Route::get('/@{username}', [PublicProfileController::class, 'show']);
Route::get('/@{username}/click/{link}', [PublicProfileController::class, 'redirect']);

// AUTH ROUTES (BREEZE)
require __DIR__.'/auth.php';
