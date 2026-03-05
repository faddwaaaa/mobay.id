<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\TrackClickMetadata;
use Illuminate\Routing\Router;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(function (Router $router) {
        // Route utama (web)
        $router->middleware('web')
            ->group(base_path('routes/web.php'));

        // Route console
        $router->middleware('web')
            ->group(base_path('routes/console.php'));

        // Route admin
        $router->middleware(['web', 'auth', 'is_admin'])
            ->prefix('admin')
            ->name('admin.')
            ->group(base_path('routes/admin.php'));
    })
    ->withMiddleware(function (Middleware $middleware): void {
        // Middleware lama kamu
        $middleware->append(TrackClickMetadata::class);

        // Tambahan middleware admin
        $middleware->alias([
            'is_admin' => \App\Http\Middleware\IsAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();