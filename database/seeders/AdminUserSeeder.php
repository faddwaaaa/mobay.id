<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
       User::updateOrCreate(
    ['email' => 'admin@payou.id'],
    [
        'name'              => 'Super Admin',
        'username'          => 'superadmin',
        'email'             => 'admin@payou.id',
        'password'          => Hash::make('password123'),
        'role'              => 'admin',
        'email_verified_at' => now(),
    ]
);
    }
}