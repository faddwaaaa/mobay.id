<?php

use Illuminate\Http\Request;
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
    SearchController,
    OrderController,
    RajaOngkirController
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


Route::middleware(['auth', 'verified'])->prefix('payment')->name('payment.')->group(function () {

    Route::get('/accounts', [PaymentAccountController::class, 'index'])
        ->name('accounts.index');

    Route::post('/accounts', [PaymentAccountController::class, 'store'])
        ->name('accounts.store');

    Route::patch('/accounts/{paymentAccount}/default', [PaymentAccountController::class, 'setDefault'])
        ->name('accounts.default');

    Route::delete('/accounts/{paymentAccount}', [PaymentAccountController::class, 'destroy'])
        ->name('accounts.destroy');

    Route::post('/accounts/verify', [PaymentAccountController::class, 'verifyAccount'])
        ->name('accounts.verify');

});


/*
|--------------------------------------------------------------------------
| API — HARUS DI ATAS /{username} AGAR TIDAK TERTIMPA
|--------------------------------------------------------------------------
*/
Route::prefix('api')->group(function () {
    // Ongkir (Binderbyte)
    Route::get('/ongkir/cities', [RajaOngkirController::class, 'cities']);
    Route::post('/ongkir/cost', [RajaOngkirController::class, 'cost']);

    // Midtrans callback
    Route::post('/callback/midtrans', [CallbackController::class, 'handleMidtransCallback'])
        ->name('midtrans.callback');

    // Transaksi
    Route::post('/topup', [TransactionController::class, 'createTopUp']);
    Route::post('/withdraw', [TransactionController::class, 'createWithdraw']);
    Route::get('/transactions', [TransactionController::class, 'getTransactionHistory']);
    Route::get('/withdrawals', [TransactionController::class, 'getWithdrawalHistory']);

    // Ambil data produk untuk render kartu
    Route::get('/product/{id}/data', [ProductController::class, 'apiShow']);
    Route::get('/product/{id}', [ProductController::class, 'apiShow']);

    // Catat klik produk
    Route::post('/product/{id}/view', [ProductController::class, 'trackView']);

    // Catat kunjungan halaman profil publik
    Route::post('/profile/{username}/view', [App\Http\Controllers\PublicProfileController::class, 'trackProfileView']);

    // Cart
    Route::get('/cart',          [CartController::class, 'index']);
    Route::post('/cart/add',     [CartController::class, 'add']);
    Route::patch('/cart/{id}',   [CartController::class, 'update']);
    Route::delete('/cart/clear', [CartController::class, 'clear']);
    Route::delete('/cart/{id}',  [CartController::class, 'remove']);

    // ============================================
    // BLOCKS API — untuk AJAX fetch saat pindah halaman
    // ============================================
    Route::get('/blocks', function (Request $request) {
        $pageId = $request->query('page_id');

        if (!$pageId) {
            return response()->json([
                'success' => false,
                'message' => 'Page ID required'
            ], 400);
        }

        $page = \App\Models\Page::with('blocks')->find($pageId);

        if (!$page) {
            return response()->json([
                'success' => false,
                'message' => 'Page not found'
            ], 404);
        }

        // Pastikan page milik user yang sedang login
        if ($page->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $blocks = $page->blocks->sortBy('position')->map(function ($block) {
            return [
                'id'         => $block->id,
                'type'       => $block->type,
                'content'    => $block->content,
                'product_id' => $block->product_id ?? null,
                'position'   => $block->position,
            ];
        })->values();

        return response()->json([
            'success'   => true,
            'blocks'    => $blocks,
            'pageTitle' => $page->title,
        ]);
    })->middleware('auth')->name('api.blocks');

});

/*
|--------------------------------------------------------------------------
| HISTORY & DETAIL TRANSAKSI
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/riwayat', [TransactionController::class, 'history'])->name('transactions.history');
    Route::get('/riwayat/pembayaran/{id}', [TransactionController::class, 'paymentDetail'])->name('transactions.payment-detail');
    Route::get('/riwayat/penarikan/{id}', [TransactionController::class, 'withdrawalDetail'])->name('transactions.withdrawal-detail');
});

Route::middleware(['auth', 'verified'])->prefix('payment')->name('payment.')->group(function () {

    Route::get('/accounts', [PaymentAccountController::class, 'index'])
        ->name('accounts.index');

    Route::post('/accounts', [PaymentAccountController::class, 'store'])
        ->name('accounts.store');

    Route::patch('/accounts/{paymentAccount}/default', [PaymentAccountController::class, 'setDefault'])
        ->name('accounts.default');

    Route::delete('/accounts/{paymentAccount}', [PaymentAccountController::class, 'destroy'])
        ->name('accounts.destroy');

    Route::post('/accounts/verify', [PaymentAccountController::class, 'verifyAccount'])
        ->name('accounts.verify');

    Route::post('/setup-pin', [PaymentAccountController::class, 'setupPin'])
        ->name('accounts.setup-pin');

});


use App\Http\Controllers\ShippingSettingsController;

Route::middleware(['auth'])->group(function () {
    Route::get('/settings/shipping',  [ShippingSettingsController::class, 'index'])->name('settings.shipping');
    Route::post('/settings/shipping', [ShippingSettingsController::class, 'save'])->name('settings.shipping.save');
});

/*
|--------------------------------------------------------------------------
| ORDER ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/pesanan', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/pesanan/{id}', [OrderController::class, 'show'])->name('orders.show');
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

    // Notifications
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread-count', [App\Http\Controllers\NotificationController::class, 'unreadCount'])->name('notifications.unread');
    Route::post('/notifications/mark-all', [App\Http\Controllers\NotificationController::class, 'markAllRead'])->name('notifications.markAll');
    Route::post('/notifications/{notification}/read', [App\Http\Controllers\NotificationController::class, 'markRead'])->name('notifications.read');

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

    return view('public.profile', compact('user', 'page'));
})->where('username', '[a-zA-Z0-9_]+')
  ->name('public.profile');
