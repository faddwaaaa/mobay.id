<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\{
    DashboardController,
    ProfileController,
    LinkController,
    PageController,
    BlockController
};

// ======================
// LANDING PAGE
// ======================
Route::get('/', function () {
    return view('landing.index');
});

// ======================
// AUTH REQUIRED
// ======================
Route::middleware(['auth'])->group(function () {

    // ======================
    // DASHBOARD
    // ======================
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // ======================
    // PROFILE
    // ======================
    Route::get('/dashboard/profile', [ProfileController::class, 'profile'])
        ->name('dashboard.profile');

    Route::get('/profile/edit', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    // ======================
    // LINKS (LYNK.ID CLONE)
    // ======================
    Route::get('/links', [LinkController::class, 'index'])
        ->name('links.index');

    // ======================
    // PAGE
    // ======================
    Route::post('/pages', [PageController::class, 'store'])
        ->name('pages.store');

    Route::delete('/pages/{page}', [PageController::class, 'destroy'])
        ->name('pages.destroy');

    // ======================
    // BLOCK
    // ======================
    Route::get('/blocks/create', function () {
        return view('dashboard.links.blocks.create');
    })->name('blocks.create');

    Route::post('/blocks', [BlockController::class, 'store'])
        ->name('blocks.store');

    Route::delete('/blocks/{block}', [BlockController::class, 'destroy'])
        ->name('blocks.destroy');

    Route::post('/blocks/reorder', [BlockController::class, 'reorder'])
        ->name('blocks.reorder');

});

// ======================
// LOGOUT
// ======================
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

// ======================
// AUTH ROUTES
// ======================
require __DIR__.'/auth.php';
