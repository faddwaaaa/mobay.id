<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Product;
use App\Models\ProfileView;

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
    LinkRedirectController,
    LinksController,
    CheckoutController,
    LandingController,
    CartController,
    PaymentAccountController,
    SearchController
};
/*
|--------------------------------------------------------------------------
| LANDING PAGE
|--------------------------------------------------------------------------
*/
Route::get('/', [LandingController::class, 'index'])->name('home');
Route::get('/service', [LandingController::class, 'service'])->name('service');
Route::get('/faq', [LandingController::class, 'faq'])->name('faq');
Route::get('/about', [LandingController::class, 'about'])->name('about');
Route::get('/contact', [LandingController::class, 'contact'])->name('contact');


/*
|--------------------------------------------------------------------------
| AUTH ROUTES
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';


/*
|--------------------------------------------------------------------------
| CHECKOUT — HARUS DI LUAR AUTH & DI ATAS PUBLIC PROFILE
|--------------------------------------------------------------------------
*/
Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
Route::get('/checkout/pending', [CheckoutController::class, 'pending'])->name('checkout.pending');
Route::get('/checkout/{productId}', [CheckoutController::class, 'show'])->name('checkout.show');
Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');
Route::post('/midtrans/webhook', [CheckoutController::class, 'webhook'])->name('midtrans.webhook');


// File: routes/web.php (tambahkan di dalam middleware auth group)

Route::middleware(['auth', 'verified'])->prefix('payment')->name('payment.')->group(function () {

    // Main page — list saved accounts
    Route::get('/accounts', [PaymentAccountController::class, 'index'])
        ->name('accounts.index');

    // Save new account
    Route::post('/accounts', [PaymentAccountController::class, 'store'])
        ->name('accounts.store');

    // Set as default
    Route::patch('/accounts/{paymentAccount}/default', [PaymentAccountController::class, 'setDefault'])
        ->name('accounts.default');

    // Delete account (requires PIN in body)
    Route::delete('/accounts/{paymentAccount}', [PaymentAccountController::class, 'destroy'])
        ->name('accounts.destroy');

    // Verify bank account number via Midtrans
    Route::post('/accounts/verify', [PaymentAccountController::class, 'verifyAccount'])
        ->name('accounts.verify');

});


/*
|--------------------------------------------------------------------------
| API — HARUS DI ATAS /{username} AGAR TIDAK TERTIMPA
|--------------------------------------------------------------------------
*/
Route::prefix('api')->group(function () {

    // Midtrans callback
    Route::post('/callback/midtrans', [CallbackController::class, 'handleMidtransCallback'])
        ->name('midtrans.callback');

    // Transaksi
    Route::post('/topup', [TransactionController::class, 'createTopUp']);
    Route::post('/withdraw', [TransactionController::class, 'createWithdraw']);
    Route::get('/transactions', [TransactionController::class, 'getTransactionHistory']);
    Route::get('/withdrawals', [TransactionController::class, 'getWithdrawalHistory']);

    // ✅ FIX: Pisah endpoint render produk (tanpa tracking) dari endpoint catat klik
    // HAPUS route lama: Route::get('/product/{id}', ...) yang langsung catat ProductViews

    // Ambil data produk untuk render kartu — TIDAK mencatat view
    Route::get('/product/{id}/data', [ProductController::class, 'apiShow']);
    Route::get('/product/{id}', [ProductController::class, 'apiShow']);

    // Catat klik produk — dipanggil HANYA saat user benar-benar klik produk
    Route::post('/product/{id}/view', [ProductController::class, 'trackView']);

    // ✅ FIX: Catat kunjungan halaman profil publik — dipanggil via AJAX saat halaman dibuka
    Route::post('/profile/{username}/view', [App\Http\Controllers\PublicProfileController::class, 'trackProfileView']);

    // Cart
    Route::get('/cart',          [CartController::class, 'index']);
    Route::post('/cart/add',     [CartController::class, 'add']);
    Route::patch('/cart/{id}',   [CartController::class, 'update']);
    Route::delete('/cart/clear', [CartController::class, 'clear']);
    Route::delete('/cart/{id}',  [CartController::class, 'remove']);
});

// HISTORY & DETAIL TRANSAKSI — HARUS DI LUAR AUTH & DI ATAS PUBLIC PROFILE
// HISTORY & DETAIL TRANSAKSI — HARUS DI LUAR AUTH & DI ATAS PUBLIC PROFILE
// HISTORY & DETAIL TRANSAKSI — HARUS DI LUAR AUTH & DI ATAS PUBLIC PROFILE

Route::middleware('auth')->group(function () {
    // Riwayat Transaksi
    Route::get('/riwayat', [TransactionController::class, 'history'])->name('transactions.history');
    Route::get('/riwayat/pembayaran/{id}', [TransactionController::class, 'paymentDetail'])->name('transactions.payment-detail');
    Route::get('/riwayat/penarikan/{id}', [TransactionController::class, 'withdrawalDetail'])->name('transactions.withdrawal-detail');
});


/*
|--------------------------------------------------------------------------
| AUTHENTICATED ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/dashboard/chart-data', [DashboardController::class, 'chartData'])->middleware('auth');

    // Analytics
    Route::prefix('analitik')->name('analitik.')->group(function () {
        Route::get('/', [AnalyticsController::class, 'index'])->name('index');
    });

    // Profile
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/dashboard/profile', [ProfileController::class, 'profile'])->name('dashboard.profile');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Links
    Route::get('/links', [LinkController::class, 'index'])->name('links.index');

    // Pages
    Route::post('/pages', [PageController::class, 'store'])->name('pages.store');
    Route::put('/pages/{page}', [PageController::class, 'update'])->name('pages.update');
    Route::delete('/pages/{page}', [PageController::class, 'destroy'])->name('pages.destroy');
    Route::get('/pages/{page}/edit', [PageController::class, 'edit'])->name('pages.edit');

    // Blocks
    Route::get('/blocks/create', fn () => view('dashboard.links.blocks.create'))->name('blocks.create');
    Route::post('/blocks/reorder', [BlockController::class, 'reorder'])->name('blocks.reorder');
    Route::resource('blocks', BlockController::class)->only(['store', 'update', 'destroy']);
    Route::post('/blocks/add-product', [BlockController::class, 'addProductBlock'])->name('blocks.addProduct');

    // QR Code
    Route::get('/qr-code', function () {
        $user = Auth::user();
        return view('qr-code', [
            'userSlug'   => $user->username,
            'totalScans' => 0,
            'todayScans' => 0,
        ]);
    })->name('qrcode.show');

    // Produk
    Route::get('/produk', [ProductController::class, 'index'])->name('products.manage');
    Route::post('/produk', [ProductController::class, 'store'])->name('products.store');
    Route::delete('/produk/{produk}', [ProductController::class, 'destroy'])->name('products.destroy');
    Route::put('/produk/{product}/update', [ProductController::class, 'update'])->name('products.update');

    // Topup & Withdraw
    Route::get('/dashboard/topup', [TransactionController::class, 'showTopupForm'])->name('topup.form');
    Route::get('/dashboard/topup/success', [TransactionController::class, 'topupSuccess'])->name('topup.success');
    Route::get('/dashboard/topup/error', [TransactionController::class, 'topupError'])->name('topup.error');
    Route::get('/dashboard/topup/pending', [TransactionController::class, 'topupPending'])->name('topup.pending');
    Route::get('/dashboard/withdraw', [TransactionController::class, 'showWithdrawForm'])->name('withdraw.form');
    Route::post('/withdrawal', [TransactionController::class, 'createWithdraw'])->name('withdrawal.store');

    // Debug
    Route::get('/debug-last-trx', function () {
        $trx = App\Models\Transaction::latest()->first();
        return [
            'order_id'   => $trx->order_id ?? null,
            'status'     => $trx->status ?? null,
            'amount'     => $trx->amount ?? null,
            'notes'      => is_string($trx->notes ?? null) ? json_decode($trx->notes, true) : $trx->notes,
            'created_at' => $trx->created_at ?? null,
        ];
    });

    // Logout
    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/login');
    })->name('logout');
});


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
| LINK TRACKING /go/
|--------------------------------------------------------------------------
*/
Route::get('/go/{username}', [LinkRedirectController::class, 'redirect'])
->name('link.redirect');

Route::get('/search', [SearchController::class, 'search']);

/*
|--------------------------------------------------------------------------
| SHORT LINK REDIRECT (6-8 karakter) — sebelum /{username}
|--------------------------------------------------------------------------
*/
Route::get('/{short_code}', function ($short_code) {
    // Kalau ada di tabel users sebagai username, skip ke public profile
    if (\App\Models\User::where('username', $short_code)->exists()) {
        $user = \App\Models\User::where('username', $short_code)
            ->with(['pages' => fn ($q) => $q->where('is_active', 1)->with('blocks')])
            ->firstOrFail();

        $page = $user->pages->first();

        return view('public.profile', compact('user', 'page'));
    }

    return app(\App\Http\Controllers\LinkController::class)->redirect(request(), $short_code);
})->where('short_code', '[a-zA-Z0-9]{6,8}')
  ->name('link.redirect.code');


/*
|--------------------------------------------------------------------------
| PUBLIC PROFILE — HARUS PALING BAWAH
|--------------------------------------------------------------------------
*/
Route::get('/{username}', function ($username) {
    $user = User::where('username', $username)
        ->with(['pages' => fn ($q) => $q->where('is_active', 1)->with('blocks')])
        ->firstOrFail();

    $page = $user->pages->first();

    // ✅ FIX: Tracking views dilakukan via AJAX dari blade, bukan di sini
    // Hapus: $user->increment('profile_views');

    return view('public.profile', compact('user', 'page'));
})->where('username', '[a-zA-Z0-9_]+')
  ->name('public.profile');