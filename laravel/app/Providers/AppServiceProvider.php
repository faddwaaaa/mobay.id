<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\User;
use App\Models\Page;
use App\Observers\UserObserver;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        /**
         * Register UserObserver untuk handle storage events
         * - Initialize storage saat user baru dibuat
         * - Update storage limit saat subscription plan berubah
         */
        User::observe(UserObserver::class);

        // Saat user baru dibuat
        User::created(function ($user) {
            Page::create([
                'user_id'    => $user->id,
                'title'      => 'Utama',
                'slug'       => 'utama',
                'is_default' => true,
            ]);
        });
    }
}
