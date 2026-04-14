<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambahkan kolom untuk tracking storage usage:
     * - storage_used: jumlah storage yang sudah digunakan (dalam bytes)
     * - storage_limit: batas maksimal storage (dalam bytes)
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Storage used dalam bytes (default 0)
            $table->bigInteger('storage_used')->default(0)->after('subscription_plan');
            
            // Storage limit dalam bytes berdasarkan subscription plan
            // Free: 20 MB (20971520 bytes)
            // Pro: 1 GB (1073741824 bytes)
            $table->bigInteger('storage_limit')->default(20971520)->after('storage_used');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['storage_used', 'storage_limit']);
        });
    }
};
