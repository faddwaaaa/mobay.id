<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicProfilekController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\CallbackController;
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

    // Top-up API endpoint (called from dashboard JS)
    Route::post('/api/topup', [\App\Http\Controllers\TransactionController::class, 'createTopUp'])
        ->name('api.topup');

    // (withdraw approve route removed)

    // USER PROFILE - View profile page
    Route::get('/profile', [ProfileController::class, 'show'])
        ->name('profile.show');
    
    // PROFILE - Dashboard version
    Route::get('/dashboard/profile', [ProfileController::class, 'profile'])
        ->name('dashboard.profile');

    // PROFILE EDIT & UPDATE
    Route::get('/profile/edit', [ProfileController::class, 'edit'])
        ->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    // ==========================================
    // PAYMENT ROUTES (TOP-UP & WITHDRAW)
    // ==========================================

    // Top-up routes
    Route::get('/dashboard/topup', [TransactionController::class, 'showTopupForm'])
        ->name('topup.form');
    Route::get('/dashboard/topup/success', [TransactionController::class, 'topupSuccess'])
        ->name('topup.success');
    Route::get('/dashboard/topup/error', [TransactionController::class, 'topupError'])
        ->name('topup.error');
    Route::get('/dashboard/topup/pending', [TransactionController::class, 'topupPending'])
        ->name('topup.pending');

    // Withdraw routes
    Route::get('/dashboard/withdraw', [TransactionController::class, 'showWithdrawForm'])
        ->name('withdraw.form');

    // API routes for payment operations
    Route::prefix('api')->group(function () {
        // Dashboard stats
        Route::get('/dashboard/stats', [DashboardController::class, 'getStats'])
            ->name('api.dashboard.stats');

        // Transaction operations
        Route::post('/topup', [TransactionController::class, 'createTopUp'])
            ->name('api.topup.create');
        Route::get('/transactions', [TransactionController::class, 'getTransactionHistory'])
            ->name('api.transactions.history');

        // Withdrawal operations
        Route::post('/withdraw', [TransactionController::class, 'createWithdraw'])
            ->name('api.withdraw.create');
        Route::get('/withdrawals', [TransactionController::class, 'getWithdrawalHistory'])
            ->name('api.withdrawals.history');
    });
});



//link
Route::get('/links', [LinkController::class, 'index'])
        ->name('links.index');

// ==========================================
// MIDTRANS CALLBACK (PUBLIC - NO AUTH)
// ==========================================
// Must be public because Midtrans server sends callback
Route::post('/api/callback/midtrans', [CallbackController::class, 'handleMidtransCallback'])
    ->name('midtrans.callback');

// LOGOUT
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

// AUTH ROUTES
require __DIR__.'/auth.php';
