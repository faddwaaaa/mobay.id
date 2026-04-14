<?php
// Jalankan: php artisan tinker --execute="require 'cek.php';"
// ATAU taruh file ini di root laravel lalu: php cek.php

require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$user    = App\Models\User::where('username','estre')->first();
$profile = $user->profile;

echo "=== USER ===\n";
echo "id: ".$user->id."\n";
echo "username: ".$user->username."\n\n";

echo "=== PROFILE ===\n";
echo "profile id: ".($profile->id ?? 'NULL')."\n";
echo "social_links raw: ".json_encode($profile->social_links ?? null)."\n";
echo "instagram col: ".($profile->instagram ?? 'NULL')."\n";
echo "telegram col: ".($profile->telegram ?? 'NULL')."\n";
echo "text_color: ".($profile->text_color ?? 'NULL')."\n";