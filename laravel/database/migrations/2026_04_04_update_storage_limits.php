<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Update storage limits untuk semua existing users:
     * - Free users: dari 20 MB (20971520) ke 10 MB (10485760)
     * - Pro users: dari 1 GB (1073741824) ke 50 MB (52428800)
     */
    public function up(): void
    {
        // Update Free users ke 10 MB
        DB::table('users')
            ->where('subscription_plan', 'free')
            ->orWhereNull('subscription_plan')
            ->update(['storage_limit' => 10485760]);

        // Update Pro users ke 50 MB
        DB::table('users')
            ->whereIn('subscription_plan', ['pro', 'premium'])
            ->update(['storage_limit' => 52428800]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert Free users ke 20 MB
        DB::table('users')
            ->where('subscription_plan', 'free')
            ->orWhereNull('subscription_plan')
            ->update(['storage_limit' => 20971520]);

        // Revert Pro users ke 1 GB
        DB::table('users')
            ->whereIn('subscription_plan', ['pro', 'premium'])
            ->update(['storage_limit' => 1073741824]);
    }
};
