<?php

use Illuminate\Support\Facades\Route;
<<<<<<< HEAD
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\{
    DashboardController,
    ProfileController,
    LinkController,
    PageController,
    BlockController
};
=======
use App\Http\Controllers\PublicProfilekController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\CallbackController;
use Illuminate\Support\Facades\Auth;
>>>>>>> fcec5cf8c788fced1ce930754125131cc88d9143

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

<<<<<<< HEAD
    // ======================
    // PROFILE
    // ======================
=======
    // Top-up API endpoint (called from dashboard JS)
    Route::post('/api/topup', [\App\Http\Controllers\TransactionController::class, 'createTopUp'])
        ->name('api.topup');

    // (withdraw approve route removed)

    // USER PROFILE - View profile page
    Route::get('/profile', [ProfileController::class, 'show'])
        ->name('profile.show');
    
    // PROFILE - Dashboard version
>>>>>>> fcec5cf8c788fced1ce930754125131cc88d9143
    Route::get('/dashboard/profile', [ProfileController::class, 'profile'])
        ->name('dashboard.profile');

    Route::get('/profile/edit', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
<<<<<<< HEAD

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
=======

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

>>>>>>> fcec5cf8c788fced1ce930754125131cc88d9143
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
<<<<<<< HEAD
// ======================
=======
>>>>>>> fcec5cf8c788fced1ce930754125131cc88d9143
require __DIR__.'/auth.php';
