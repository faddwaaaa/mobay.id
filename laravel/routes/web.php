<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

use App\Http\Controllers\{
    DashboardController,
    ProfileController,
    LinkController,
    PageController,
    BlockController,
    TransactionController,
    CallbackController,
    ProductController,
    AnalyticsController,
    LinkRedirectController
};

/*
|--------------------------------------------------------------------------
| LANDING PAGE
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => view('landing.index'));


/*
|--------------------------------------------------------------------------
| AUTH ROUTES
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';


/*
|--------------------------------------------------------------------------
| AUTHENTICATED ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');


    /*
    |--------------------------------------------------------------------------
    | ANALYTICS
    |--------------------------------------------------------------------------
    */
    Route::prefix('analitik')->name('analitik.')->group(function () {
        Route::get('/', [AnalyticsController::class, 'index'])
            ->name('index');
    });


    /*
    |--------------------------------------------------------------------------
    | PROFILE
    |--------------------------------------------------------------------------
    */
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


    /*
    |--------------------------------------------------------------------------
    | LINKS
    |--------------------------------------------------------------------------
    */
    Route::get('/links', [LinkController::class, 'index'])
        ->name('links.index');


    /*
    |--------------------------------------------------------------------------
    | PAGE
    |--------------------------------------------------------------------------
    */
    Route::post('/pages', [PageController::class, 'store'])
        ->name('pages.store');

    Route::put('/pages/{page}', [PageController::class, 'update'])
        ->name('pages.update');

    Route::delete('/pages/{page}', [PageController::class, 'destroy'])
        ->name('pages.destroy');

    Route::get('/pages/{page}/edit', [PageController::class, 'edit'])
        ->name('pages.edit');


    /*
    |--------------------------------------------------------------------------
    | BLOCK (FIXED)
    |--------------------------------------------------------------------------
    */
    Route::get('/blocks/create', fn () => view('dashboard.links.blocks.create'))
        ->name('blocks.create');

    Route::post('/blocks/reorder', [BlockController::class, 'reorder'])
        ->name('blocks.reorder');

    Route::resource('blocks', BlockController::class)
        ->only(['store', 'update', 'destroy']);


    /*
    |--------------------------------------------------------------------------
    | QR CODE
    |--------------------------------------------------------------------------
    */
    Route::get('/qr-code', function () {
        $user = auth()->user();
        return view('qr-code', [
            'userSlug'   => $user->username,
            'totalScans' => 0,
            'todayScans' => 0,
        ]);
    })->name('qrcode.show');


    /*
    |--------------------------------------------------------------------------
    | PRODUK
    |--------------------------------------------------------------------------
    */
    Route::get('/produk', [ProductController::class, 'index'])
        ->name('products.manage');

    Route::post('/produk', [ProductController::class, 'store'])
        ->name('products.store');

    Route::delete('/produk/{produk}', [ProductController::class, 'destroy'])
        ->name('products.destroy');


    /*
    |--------------------------------------------------------------------------
    | PAYMENT
    |--------------------------------------------------------------------------
    */
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


    /*
    |--------------------------------------------------------------------------
    | LOGOUT
    |--------------------------------------------------------------------------
    */
    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/login');
    })->name('logout');
});


/*
|--------------------------------------------------------------------------
| API
|--------------------------------------------------------------------------
*/
Route::prefix('api')->group(function () {

    Route::post('/topup', [TransactionController::class, 'createTopUp'])
        ->name('api.topup.create');

    Route::post('/withdraw', [TransactionController::class, 'createWithdraw'])
        ->name('api.withdraw.create');

    Route::get('/transactions', [TransactionController::class, 'getTransactionHistory'])
        ->name('api.transactions.history');

    Route::get('/withdrawals', [TransactionController::class, 'getWithdrawalHistory'])
        ->name('api.withdrawals.history');
});


/*
|--------------------------------------------------------------------------
| MIDTRANS CALLBACK
|--------------------------------------------------------------------------
*/
Route::post('/api/callback/midtrans', [CallbackController::class, 'handleMidtransCallback'])
    ->name('midtrans.callback');


/*
|--------------------------------------------------------------------------
| PREVIEW (PUBLIC)
|--------------------------------------------------------------------------
*/
Route::get('/preview/{username}', function ($username) {
    $user = User::where('username', $username)
        ->with(['pages' => fn ($q) => $q->with('blocks')])
        ->firstOrFail();

    $page = $user->pages->first();

    return view('preview', compact('user', 'page'));
})->name('preview.profile');


/*
|--------------------------------------------------------------------------
| LINK TRACKING DENGAN PREFIX /go/
| Format: http://localhost:8000/go/asadtevy94
|--------------------------------------------------------------------------
*/
Route::get('/go/{username}', [LinkRedirectController::class, 'redirect'])
    ->name('link.redirect');


/*
|--------------------------------------------------------------------------
| SHORT LINK REDIRECT (untuk short_code 6-8 karakter)
| Format: http://localhost:8000/abc123
|--------------------------------------------------------------------------
*/
Route::get('/{short_code}', [LinkController::class, 'redirect'])
    ->where('short_code', '[a-zA-Z0-9]{6,8}')
    ->name('link.redirect.code');


/*
|--------------------------------------------------------------------------
| PUBLIC PROFILE (HARUS PALING BAWAH)
| Format: http://localhost:8000/username
|--------------------------------------------------------------------------
*/
Route::get('/{username}', function ($username) {
    $user = User::where('username', $username)
        ->with(['pages' => fn ($q) => $q->where('is_active', 1)->with('blocks')])
        ->firstOrFail();

    $page = $user->pages->first();

    return view('public.profile', compact('user', 'page'));
})->where('username', '[a-zA-Z0-9_]+')
  ->name('public.profile');


// ```

// ## Format URL yang Didukung:

// ### 1. **Link Tracking dengan Prefix `/go/`** ✅
// ```
// http://localhost:8000/go/asadtevy94
// ```
// → Track klik + redirect ke link tujuan

// ### 2. **Short Code Link** ✅
// ```
// http://localhost:8000/abc123
// ```
// → Short link 6-8 karakter (dari kolom `short_code`)

// ### 3. **Public Profile** ✅
// ```
// http://localhost:8000/asadtevy94