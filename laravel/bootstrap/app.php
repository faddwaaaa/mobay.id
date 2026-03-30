<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\TrackClickMetadata;
use App\Http\Middleware\AddStorageToView;
use Illuminate\Routing\Router;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(function (Router $router) {
        // Route utama (web)
        $router->middleware('web')
            ->group(base_path('routes/web.php'));

        // Route console
        $router->middleware('web')
            ->group(base_path('routes/console.php'));

        // Route admin — middleware web + auth + is_admin sudah di sini
        $router->middleware(['web', 'auth', 'is_admin'])
            ->prefix('admin')
            ->name('admin.')
            ->group(base_path('routes/admin.php'));
    })
    ->withMiddleware(function (Middleware $middleware): void {
        // Track klik metadata
        $middleware->append(TrackClickMetadata::class);

        // Tambahkan storage info ke semua view
        $middleware->append(AddStorageToView::class);

        // Alias middleware
        $middleware->alias([
            'is_admin'  => \App\Http\Middleware\IsAdmin::class,
            'suspended' => \App\Http\Middleware\CheckSuspended::class,
        ]);

        // CheckSuspended otomatis berjalan di semua route web
        $middleware->appendToGroup('web', \App\Http\Middleware\CheckSuspended::class);

        // ✅ Pengecualian CSRF — webhook tidak bisa kirim CSRF token
        $middleware->validateCsrfTokens(except: [
            'midtrans/webhook',
            'webhook/midtrans/disbursement',
            'webhooks/biteship',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();