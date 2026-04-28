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

        // Alias middleware
        $middleware->alias([
            'is_admin'    => \App\Http\Middleware\IsAdmin::class,
            'suspended'   => \App\Http\Middleware\CheckSuspended::class,
            'pro.expired' => \App\Http\Middleware\CheckExpiredProAccess::class,
        ]);

        // Middleware yang membutuhkan session/auth dijalankan di group web
        $middleware->appendToGroup('web', AddStorageToView::class);
        $middleware->appendToGroup('web', \App\Http\Middleware\CheckSuspended::class);
        $middleware->appendToGroup('web', \App\Http\Middleware\CheckExpiredProAccess::class);

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
