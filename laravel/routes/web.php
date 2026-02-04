<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LinkController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\BlockController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\CallbackController;
use App\Http\Controllers\ProductController;

// LANDING PAGE
use App\Models\User;

/*
|--------------------------------------------------------------------------
| PREVIEW (PUBLIC)
|--------------------------------------------------------------------------
*/
Route::get('/preview/{username}', function ($username) {
    $user = User::where('username', $username)
        ->with(['pages' => function ($q) {
            $q->with('blocks');
        }])
        ->firstOrFail();

    $page = $user->pages->first();
    return view('preview', compact('user', 'page'));
});

/*
|--------------------------------------------------------------------------
| LANDING PAGE
|--------------------------------------------------------------------------
*/Route::get('/', function () {
    return view('landing.index');
});

// ======================
// AUTH REQUIRED
/*
|--------------------------------------------------------------------------
| AUTH ROUTES (LOGIN, REGISTER, ETC)
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| AUTH REQUIRED
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // DASHBOARD
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // PROFILE
    // Top-up API endpoint (called from dashboard JS)
    Route::post('/api/topup', [\App\Http\Controllers\TransactionController::class, 'createTopUp'])
        ->name('api.topup');

    // USER PROFILE
    Route::get('/profile', [ProfileController::class, 'show'])
        ->name('profile.show');
    
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
    Route::get('/links', [LinkController::class, 'index'])
        ->name('links.index');

    // ======================
    // PAGE CRUD (PERBAIKI ROUTE INI)
    Route::post('/pages', [PageController::class, 'store'])
        ->name('pages.store');
    
    Route::put('/pages/{page}', [PageController::class, 'update'])
        ->name('pages.update');
    
    Route::delete('/pages/{page}', [PageController::class, 'destroy'])
        ->name('pages.destroy');
    
    Route::get('/pages/{page}/edit', [PageController::class, 'edit'])
        ->name('pages.edit');

    // ======================
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/dashboard/profile', [ProfileController::class, 'profile'])->name('dashboard.profile');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // LINKS
    Route::get('/links', [LinkController::class, 'index'])->name('links.index');

    // PAGE
    Route::post('/pages', [PageController::class, 'store'])->name('pages.store');
    Route::delete('/pages/{page}', [PageController::class, 'destroy'])->name('pages.destroy');

    // BLOCK
    Route::get('/blocks/create', function () {
        return view('dashboard.links.blocks.create');
    })->name('blocks.create');

    Route::post('/blocks', [BlockController::class, 'store'])->name('blocks.store');
    Route::delete('/blocks/{block}', [BlockController::class, 'destroy'])->name('blocks.destroy');
    Route::post('/blocks/reorder', [BlockController::class, 'reorder'])->name('blocks.reorder');

    // LOGOUT
    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/login');
    })->name('logout');
});

// =========================================
// QR CODE CONTROLLER ROUTES
// QR CODE PAGE
Route::middleware('auth')->group(function () {
    Route::get('/qr-code', function () {
        $user = auth()->user();
        
        return view('qr-code', [
            'userSlug' => $user->username,
            'totalScans' => 0,
            'todayScans' => 0,
        ]);
    })->name('qrcode.show');
});

// ==========================================
// PAYMENT ROUTES (TOP-UP & WITHDRAW)
Route::get('/dashboard/topup', [TransactionController::class, 'showTopupForm'])
    ->name('topup.form');
Route::get('/dashboard/topup/success', [TransactionController::class, 'topupSuccess'])
    ->name('topup.success');
Route::get('/dashboard/topup/error', [TransactionController::class, 'topupError'])
    ->name('topup.error');
Route::get('/dashboard/topup/pending', [TransactionController::class, 'topupPending'])
    ->name('topup.pending');

Route::get('/dashboard/withdraw', [TransactionController::class, 'showWithdrawForm'])
    ->name('withdraw.form');

// API routes
Route::prefix('api')->group(function () {
    Route::get('/dashboard/stats', [DashboardController::class, 'getStats'])
        ->name('api.dashboard.stats');
    Route::post('/topup', [TransactionController::class, 'createTopUp'])
        ->name('api.topup.create');
    Route::get('/transactions', [TransactionController::class, 'getTransactionHistory'])
        ->name('api.transactions.history');
    Route::post('/withdraw', [TransactionController::class, 'createWithdraw'])
        ->name('api.withdraw.create');
    Route::get('/withdrawals', [TransactionController::class, 'getWithdrawalHistory'])
        ->name('api.withdrawals.history');
});

// ==========================================
// MIDTRANS CALLBACK
/*
|--------------------------------------------------------------------------
| PAYMENT ROUTES
|--------------------------------------------------------------------------
*/
Route::get('/dashboard/topup', [TransactionController::class, 'showTopupForm'])->name('topup.form');
Route::get('/dashboard/topup/success', [TransactionController::class, 'topupSuccess'])->name('topup.success');
Route::get('/dashboard/topup/error', [TransactionController::class, 'topupError'])->name('topup.error');
Route::get('/dashboard/topup/pending', [TransactionController::class, 'topupPending'])->name('topup.pending');
Route::get('/dashboard/withdraw', [TransactionController::class, 'showWithdrawForm'])->name('withdraw.form');

/*
|--------------------------------------------------------------------------
| API
|--------------------------------------------------------------------------
*/
Route::prefix('api')->group(function () {
    Route::post('/topup', [TransactionController::class, 'createTopUp'])->name('api.topup.create');
    Route::post('/withdraw', [TransactionController::class, 'createWithdraw'])->name('api.withdraw.create');
    Route::get('/transactions', [TransactionController::class, 'getTransactionHistory'])->name('api.transactions.history');
    Route::get('/withdrawals', [TransactionController::class, 'getWithdrawalHistory'])->name('api.withdrawals.history');
});

/*
|--------------------------------------------------------------------------
| MIDTRANS CALLBACK (PUBLIC)
|--------------------------------------------------------------------------
*/
Route::post('/api/callback/midtrans', [CallbackController::class, 'handleMidtransCallback'])
    ->name('midtrans.callback');
//link saya
Route::get('/links', [LinkController::class, 'index'])
        ->name('links.index');

// LOGOUT
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

// AUTH ROUTES
require __DIR__.'/auth.php';
/*
|--------------------------------------------------------------------------
| PUBLIC PROFILE (PALING BAWAH + FILTER)
|--------------------------------------------------------------------------
*/
Route::get('/{username}', function ($username) {
    $user = User::where('username', $username)
        ->with(['pages' => function ($q) {
            $q->where('is_active', 1)->with('blocks');
        }])
        ->firstOrFail();

    $page = $user->pages->first();
    return view('public.profile', compact('user', 'page'));
})->where('username', '[a-zA-Z0-9_]+');
//add produk
Route::middleware('auth')->group(function () {
    Route::get('/produk/tambah', [ProductController::class, 'create'])
        ->name('products.create');

    Route::post('/produk', [ProductController::class, 'store'])
        ->name('products.store');
});