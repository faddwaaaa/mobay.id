<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\User;
use App\Models\Page;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
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
