<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LinkController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\BlockController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\CallbackController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\LinkRedirectController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\PaymentAccountController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\RajaOngkirController;
use App\Http\Controllers\AppearanceController;
use App\Http\Controllers\PublicProfileController;
use App\Http\Controllers\PublicProfileReportController;
use App\Http\Controllers\DigitalOrderController;
use App\Http\Controllers\ShippingSettingsController;
use App\Http\Controllers\BiteshipWebhookController;
use App\Http\Controllers\ProSubscriptionController;
use App\Models\User;

use App\Http\Controllers\Admin\ProfileReportController;
use App\Http\Controllers\Admin\PhysicalOrderShipmentController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

Route::get('/', [LandingController::class, 'index'])->name('home');
Route::get('/service', [LandingController::class, 'service'])->name('service');
Route::get('/faq', [LandingController::class, 'faq'])->name('faq');
Route::get('/about', [LandingController::class, 'about'])->name('about');
Route::get('/contact', [LandingController::class, 'contact'])->name('contact');

require __DIR__ . '/auth.php';
require __DIR__ . '/webhook_routes.php';



/*
|--------------------------------------------------------------------------
| HALAMAN SUSPENDED — di luar auth agar bisa diakses user yang disuspend
|--------------------------------------------------------------------------
*/
Route::get('/suspended', function () {
    if (auth()->check() && !auth()->user()->is_suspended) {
        return redirect()->route('dashboard');
    }
    return view('suspended');
})->name('suspended');

Route::get('/pro-expired', function () {
    abort_unless(auth()->check(), 403);

    if (!auth()->user()->hasExpiredProAccess()) {
        return redirect()->route('dashboard');
    }

    return view('pro.expired', [
        'user' => auth()->user(),
    ]);
})->middleware('auth')->name('pro.expired');


/*
|--------------------------------------------------------------------------
| CHECKOUT & DIGITAL PRODUCT — HARUS DI LUAR AUTH & DI ATAS SEMUA WILDCARD
|--------------------------------------------------------------------------
*/
Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
Route::get('/checkout/pending', [CheckoutController::class, 'pending'])->name('checkout.pending');
Route::get('/checkout/checkpoint', [CheckoutController::class, 'checkpointShow'])->name('checkout.checkpoint.show');
Route::post('/checkout/checkpoint', [CheckoutController::class, 'checkpointStore'])->name('checkout.checkpoint.store');
Route::get('/checkout/payment-method', [CheckoutController::class, 'paymentMethodShow'])->name('checkout.payment-method.show');
Route::post('/checkout/create-charge', [CheckoutController::class, 'createCharge'])->name('checkout.createCharge');
Route::get('/checkout/{productId}', [CheckoutController::class, 'show'])->name('checkout.show');
Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');
Route::post('/midtrans/webhook', [CheckoutController::class, 'webhook'])->name('midtrans.webhook');

// ✅ Xendit simplified checkout flow
Route::post('/checkout/xendit/create-invoice', [CheckoutController::class, 'processXenditCheckout'])->name('checkout.xendit.create-invoice');
Route::post('/checkout/xendit/callback', [CheckoutController::class, 'handleXenditCallback'])->name('checkout.xendit.callback');

// ✅ FIX: Dipindah ke sini — harus di luar auth & di atas wildcard
Route::get('/payment/success/{orderCode}', [DigitalOrderController::class, 'paymentSuccess'])
    ->name('payment.show');

// ✅ FIX: Dipindah ke sini — pembeli tidak perlu login untuk download
Route::match(['get', 'post'], '/download/{token}', [DigitalOrderController::class, 'verifyDownload'])
    ->name('download.verify');



    // ================================================================
// TAMBAHKAN KE routes/web.php
// Di dalam group Route::middleware('auth')->group(...)
// ================================================================

// Banding — hanya user yang disuspend bisa POST
Route::post('/appeal',        [\App\Http\Controllers\AppealController::class, 'store']) ->name('appeal.store');
Route::get('/appeal/status',  [\App\Http\Controllers\AppealController::class, 'status'])->name('appeal.status');


/*
|--------------------------------------------------------------------------
| PAYMENT ACCOUNT
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->prefix('payment')->name('payment.')->group(function () {
    Route::get('/accounts', [PaymentAccountController::class, 'index'])->name('accounts.index');
    Route::post('/accounts', [PaymentAccountController::class, 'store'])->name('accounts.store');
    Route::patch('/accounts/{paymentAccount}/default', [PaymentAccountController::class, 'setDefault'])->name('accounts.default');
    Route::delete('/accounts/{paymentAccount}', [PaymentAccountController::class, 'destroy'])->name('accounts.destroy');
    Route::post('/accounts/verify', [PaymentAccountController::class, 'verifyAccount'])->name('accounts.verify');
    Route::post('/setup-pin', [PaymentAccountController::class, 'setupPin'])->name('accounts.setup-pin');
});

Route::prefix('api')->group(function () {
    Route::get('/ongkir/cities', [RajaOngkirController::class, 'cities']);
    Route::post('/ongkir/cost', [RajaOngkirController::class, 'cost']);
    Route::post('/callback/midtrans', [CallbackController::class, 'handleMidtransCallback'])->name('midtrans.callback');

    // ===== STORAGE API =====
    Route::middleware('auth')->get('/storage/info', [DashboardController::class, 'getStorageInfo'])->name('api.storage.info');

    Route::post('/callback/midtrans', [CallbackController::class, 'handleMidtransCallback'])->name('midtrans.callback');

    Route::post('/topup', [TransactionController::class, 'createTopUp']);
    Route::post('/withdraw', [TransactionController::class, 'createWithdraw']);
    Route::get('/transactions', [TransactionController::class, 'getTransactionHistory']);
    Route::get('/withdrawals', [TransactionController::class, 'getWithdrawalHistory']);
    Route::get('/product/{id}/data', [ProductController::class, 'apiShow']);
    Route::get('/product/{id}', [ProductController::class, 'apiShow']);
    Route::post('/product/{id}/view', [ProductController::class, 'trackView']);
    Route::post('/profile/{username}/view', [App\Http\Controllers\PublicProfileController::class, 'trackProfileView']);

    Route::get('/product/{id}/data', [ProductController::class, 'apiShow']);
    Route::get('/product/{id}', [ProductController::class, 'apiShow']);
    Route::post('/product/{id}/view', [ProductController::class, 'trackView']);
    Route::get('/products/batch', [ProductController::class, 'apiBatch']);

    Route::post('/profile/{username}/view', [PublicProfileController::class, 'trackProfileView']);

    // FIX 2: backslash nyasar di depan Route dihapus
    Route::get('/cart',          [CartController::class, 'index']);
    Route::post('/cart/add',     [CartController::class, 'add']);
    Route::patch('/cart/{id}',   [CartController::class, 'update']);
    Route::delete('/cart/clear', [CartController::class, 'clear']);
    Route::delete('/cart/{id}',  [CartController::class, 'remove']);

    Route::post('/report-digital-problem', [DigitalOrderController::class, 'reportProblem'])
    ->name('digital.report');

    Route::get('/blocks', function (Request $request) {
        $pageId = $request->query('page_id');
        if (!$pageId) return response()->json(['success' => false, 'message' => 'Page ID required'], 400);
        $page = \App\Models\Page::with('blocks')->find($pageId);
        if (!$page) return response()->json(['success' => false, 'message' => 'Page not found'], 404);
        if ($page->user_id !== auth()->id()) return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        $blocks = $page->blocks->sortBy('position')->map(function ($block) {
            return ['id' => $block->id, 'type' => $block->type, 'content' => $block->content, 'product_id' => $block->product_id ?? null, 'position' => $block->position];
        })->values();
        $blocks = $page->blocks->sortBy('position')->map(fn ($b) => [
            'id' => $b->id, 'type' => $b->type, 'content' => $b->content,
            'product_id' => $b->product_id ?? null, 'position' => $b->position,
        ])->values();
        return response()->json(['success' => true, 'blocks' => $blocks, 'pageTitle' => $page->title]);
    })->middleware('auth')->name('api.blocks');

    // Polling updated_at untuk sync appearance di profil publik
    Route::get('/profile/{username}/updated_at', function ($username) {
        $user    = \App\Models\User::where('username', $username)->firstOrFail();
        $profile = $user->userProfile;
        return response()->json(['updated_at' => $profile?->updated_at]);
    });
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
    Route::get('/accounts', [PaymentAccountController::class, 'index'])->name('accounts.index');
    Route::post('/accounts', [PaymentAccountController::class, 'store'])->name('accounts.store');
    Route::patch('/accounts/{paymentAccount}/default', [PaymentAccountController::class, 'setDefault'])->name('accounts.default');
    Route::delete('/accounts/{paymentAccount}', [PaymentAccountController::class, 'destroy'])->name('accounts.destroy');
    Route::post('/accounts/verify', [PaymentAccountController::class, 'verifyAccount'])->name('accounts.verify');
    Route::post('/setup-pin', [PaymentAccountController::class, 'setupPin'])->name('accounts.setup-pin');
});

/*
|--------------------------------------------------------------------------
| SHIPPING SETTINGS
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/settings/shipping', [App\Http\Controllers\ShippingSettingsController::class, 'index'])->name('settings.shipping');
    Route::post('/settings/shipping', [App\Http\Controllers\ShippingSettingsController::class, 'save'])->name('settings.shipping.save');
});


/*
|--------------------------------------------------------------------------
| ORDER ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // ✅ spesifik dulu, baru wildcard
    Route::get('/pesanan/fisik/{physicalOrder}/resi', [PhysicalOrderShipmentController::class, 'edit'])
        ->name('physical-orders.shipment.edit');
    Route::put('/pesanan/fisik/{physicalOrder}/resi', [PhysicalOrderShipmentController::class, 'update'])
        ->name('physical-orders.shipment.update');

    Route::get('/pesanan', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/pesanan/{id}', [OrderController::class, 'show'])->name('orders.show');
});


/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware(['auth'])->group(function () {

    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])
        ->name('admin.dashboard');

    // ✅ TAMBAHAN: Admin input resi produk fisik
    Route::middleware(['is_admin'])->group(function () {
        Route::get('physical-orders/{physicalOrder}/shipment', [PhysicalOrderShipmentController::class, 'edit'])
            ->name('admin.physical-orders.shipment.edit');
        Route::put('physical-orders/{physicalOrder}/shipment', [PhysicalOrderShipmentController::class, 'update'])
            ->name('admin.physical-orders.shipment.update');
    });

    Route::get   ('/reports',                           [ProfileReportController::class, 'index'])       ->name('admin.reports.index');
    Route::get   ('/reports/export',                    [ProfileReportController::class, 'exportCsv'])   ->name('admin.reports.export');
    Route::get   ('/reports/{report}',                  [ProfileReportController::class, 'show'])         ->name('admin.reports.show');
    Route::patch ('/reports/{report}/status',           [ProfileReportController::class, 'updateStatus'])->name('admin.reports.updateStatus');
    Route::patch ('/reports/{report}/note',             [ProfileReportController::class, 'saveNote'])    ->name('admin.reports.saveNote');
    Route::get   ('/reports/{report}/evidence',         [ProfileReportController::class, 'viewEvidence'])->name('admin.reports.evidence');
    Route::get   ('/reports/{report}/evidence/{index}', [ProfileReportController::class, 'evidenceFile'])->name('admin.reports.evidence.file');
    Route::get   ('/appeals',                           [\App\Http\Controllers\Admin\AppealController::class, 'index'])        ->name('admin.appeals.index');
    Route::get   ('/appeals/{appeal}',                  [\App\Http\Controllers\Admin\AppealController::class, 'show'])         ->name('admin.appeals.show');
    Route::get   ('/appeals/{appeal}/evidence',         [\App\Http\Controllers\Admin\AppealController::class, 'viewEvidence']) ->name('admin.appeals.evidence');
    Route::get   ('/appeals/{appeal}/evidence/{index}', [\App\Http\Controllers\Admin\AppealController::class, 'evidenceFile']) ->name('admin.appeals.evidence.file');
    Route::patch ('/appeals/{appeal}/approve',          [\App\Http\Controllers\Admin\AppealController::class, 'approve'])      ->name('admin.appeals.approve');
    Route::patch ('/appeals/{appeal}/reject',           [\App\Http\Controllers\Admin\AppealController::class, 'reject'])       ->name('admin.appeals.reject');

});


/*
|--------------------------------------------------------------------------
| AUTHENTICATED ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/chart-data', [DashboardController::class, 'chartData']);
    Route::post('/dashboard/subscription/testing-activate', [DashboardController::class, 'activateTestingPro'])
        ->name('dashboard.subscription.testing-activate');

    Route::prefix('analitik')->name('analitik.')->group(function () {
        Route::get('/', [AnalyticsController::class, 'index'])->name('index');
        Route::get('/export', [AnalyticsController::class, 'export'])->name('export');
    });

    Route::get('/premium', function () { return view('premium.index'); })->name('premium.index');
    Route::get('/premium', fn () => view('premium.index'))->name('premium.index');

    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/dashboard/profile', [ProfileController::class, 'profile'])->name('dashboard.profile');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/links', [LinkController::class, 'index'])->name('links.index');

    Route::post('/pages', [PageController::class, 'store'])->name('pages.store');
    Route::put('/pages/{page}', [PageController::class, 'update'])->name('pages.update');
    Route::delete('/pages/{page}', [PageController::class, 'destroy'])->name('pages.destroy');
    Route::get('/pages/{page}/edit', [PageController::class, 'edit'])->name('pages.edit');

    Route::get('/blocks/create', fn () => view('dashboard.links.blocks.create'))->name('blocks.create');
    Route::post('/blocks/reorder', [BlockController::class, 'reorder'])->name('blocks.reorder');
    Route::resource('blocks', BlockController::class)->only(['store', 'update', 'destroy']);
    Route::post('/blocks/add-product', [BlockController::class, 'addProductBlock'])->name('blocks.addProduct');

    Route::get('/qr-code', function () {
        $user = Auth::user();
        return view('qr-code', ['userSlug' => $user->username, 'totalScans' => 0, 'todayScans' => 0]);
    })->name('qrcode.show');


    Route::get('/notifications/all',[App\Http\Controllers\NotificationController::class, 'page'])->name('notifications.index');
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index']);
    Route::get('/notifications/unread-count', [App\Http\Controllers\NotificationController::class, 'unreadCount'])->name('notifications.unread');
    Route::post('/notifications/mark-all', [App\Http\Controllers\NotificationController::class, 'markAllRead'])->name('notifications.markAll');
    Route::post('/notifications/{notification}/read', [App\Http\Controllers\NotificationController::class, 'markRead'])->name('notifications.read');
    Route::delete('/notifications/all', [App\Http\Controllers\NotificationController::class, 'destroyAll'])->name('notifications.destroyAll');
    Route::delete('/notifications/{notification}', [App\Http\Controllers\NotificationController::class, 'destroy'])->name('notifications.destroy');

    Route::get('/produk', [ProductController::class, 'index'])->name('products.manage');
    Route::post('/produk', [ProductController::class, 'store'])->name('products.store');
    Route::delete('/produk/{produk}', [ProductController::class, 'destroy'])->name('products.destroy');
    Route::put('/produk/{product}/update', [ProductController::class, 'update'])->name('products.update');

    Route::get('/dashboard/topup', [TransactionController::class, 'showTopupForm'])->name('topup.form');
    Route::get('/dashboard/topup/success', [TransactionController::class, 'topupSuccess'])->name('topup.success');
    Route::get('/dashboard/topup/error', [TransactionController::class, 'topupError'])->name('topup.error');
    Route::get('/dashboard/topup/pending', [TransactionController::class, 'topupPending'])->name('topup.pending');
    Route::get('/dashboard/withdraw', [TransactionController::class, 'showWithdrawForm'])->name('withdraw.form');
    Route::post('/withdrawal', [TransactionController::class, 'createWithdraw'])->name('withdrawal.store');

    // ─── Tampilan / Appearance ───
    Route::get('/appearance',                [AppearanceController::class, 'index'])->name('dashboard.appearance');
    Route::get('/appearance/preview',        [AppearanceController::class, 'preview'])->name('dashboard.appearance.preview');
    Route::post('/appearance/save',          [AppearanceController::class, 'save'])->name('dashboard.appearance.save');
    Route::post('/appearance/upload-bg',     [AppearanceController::class, 'uploadBgImage'])->name('dashboard.appearance.uploadBg');
    Route::post('/appearance/upload-banner', [AppearanceController::class, 'uploadBanner'])->name('dashboard.appearance.uploadBanner');
    Route::post('/appearance/reset',         [AppearanceController::class, 'reset'])->name('dashboard.appearance.reset');
    Route::post('/appearance/delete-banner', [AppearanceController::class, 'deleteBanner'])->name('dashboard.appearance.deleteBanner');
    Route::post('appearance/delete-bg', [AppearanceController::class, 'deleteBg'])->name('dashboard.appearance.deleteBg');

    Route::get('/debug-last-trx', function () {
        $trx = App\Models\Transaction::latest()->first();
        return ['order_id' => $trx->order_id ?? null, 'status' => $trx->status ?? null,
                'amount' => $trx->amount ?? null,
                'notes' => is_string($trx->notes ?? null) ? json_decode($trx->notes, true) : $trx->notes,
                'created_at' => $trx->created_at ?? null];
    });

    // ─── Pro Subscription ───
    Route::prefix('pro')->name('pro.')->group(function () {
        Route::post('/create-invoice', [ProSubscriptionController::class, 'createInvoice'])->name('create-invoice');
        Route::get('/status', [ProSubscriptionController::class, 'checkStatus'])->name('status');
        Route::get('/payment/success', [ProSubscriptionController::class, 'paymentSuccess'])->name('payment.success');
        Route::get('/payment/failed', [ProSubscriptionController::class, 'paymentFailed'])->name('payment.failed');
        Route::post('/testing-activate', [DashboardController::class, 'activateTestingPro'])->name('testing.activate');
    });

    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/login');
    })->name('logout');
});


/*
|--------------------------------------------------------------------------
| LAPORAN PUBLIK
|--------------------------------------------------------------------------
*/
Route::post('/{username}/report', [PublicProfileReportController::class, 'store'])
    ->where('username', '[a-zA-Z0-9_]+')
    ->name('public.profile.report');


/*
|--------------------------------------------------------------------------
| PREVIEW (PUBLIC)
|--------------------------------------------------------------------------
*/
Route::get('/preview/{username}', function ($username) {
    $user = User::where('username', $username)
        ->with(['profile', 'pages' => fn ($q) => $q->with('blocks')])
        ->firstOrFail();

    $profile = $user->userProfile;
    $socialLinks = collect($profile?->social_links ?? [])->filter()->toArray();

    return view('public.profile', compact('user', 'profile', 'socialLinks'));
})->name('preview.profile');

//biteship
Route::post('/webhooks/biteship', [\App\Http\Controllers\BiteshipWebhookController::class, 'handle'])
    ->name('webhooks.biteship');

    Route::get('/debug/biteship', function () {
    return response()->json([
        'webhook_secret_set' => !empty(config('services.biteship.webhook_secret')),
        'api_key_set'        => !empty(env('BITESHIP_API_KEY')),
        'webhook_url'        => config('app.url') . '/webhooks/biteship', // ✅ ganti ini
        'app_url'            => config('app.url'),
        'environment'        => app()->environment(),
    ]);
})->middleware('auth');

/*
|--------------------------------------------------------------------------
| LINK TRACKING /go/
|--------------------------------------------------------------------------
*/
Route::get('/go/{username}', [LinkRedirectController::class, 'redirect'])->name('link.redirect');

Route::get('/search', [SearchController::class, 'search']);


Route::get('/{short_code}', function ($short_code) {
    if (\App\Models\User::where('username', $short_code)->exists()) {
        $user = \App\Models\User::where('username', $short_code)
            ->with(['profile', 'pages' => fn ($q) => $q->where('is_active', 1)->with('blocks')])
            ->firstOrFail();

        // Blokir profil user yang disuspend
        if ($user->is_suspended || $user->hasExpiredProAccess()) abort(404);

        $profile     = $user->userProfile;
        $socialLinks = collect($profile?->social_links ?? [])->filter()->toArray();
        return view('public.profile', compact('user', 'profile', 'socialLinks'));
    }
    return app(\App\Http\Controllers\LinkController::class)->redirect(request(), $short_code);
})->where('short_code', '[a-zA-Z0-9]{6,8}')->name('link.redirect.code');

Route::get('/{username}', function ($username) {
    $user = \App\Models\User::where('username', $username)
        ->with(['profile', 'pages' => fn ($q) => $q->where('is_active', 1)->with('blocks')])
        ->firstOrFail();

    // Blokir profil user yang disuspend
    if ($user->is_suspended || $user->hasExpiredProAccess()) abort(404);

    $profile     = $user->userProfile;
    $socialLinks = collect($profile?->social_links ?? [])->filter()->toArray();
    return view('public.profile', compact('user', 'profile', 'socialLinks'));
})->where('username', '[a-zA-Z0-9_]+')->name('public.profile');
