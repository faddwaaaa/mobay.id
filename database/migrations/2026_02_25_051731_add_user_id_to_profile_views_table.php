<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Jalankan migration ini HANYA jika kolom user_id belum ada di tabel profile_views
 * Command: php artisan migrate
 */
return new class extends Migration
{
    public function up(): void
    {
        // Cek dulu struktur tabel profile_views
        // Jika belum ada kolom user_id, tambahkan
        if (!Schema::hasColumn('profile_views', 'user_id')) {
            Schema::table('profile_views', function (Blueprint $table) {
                $table->foreignId('user_id')->after('id')->constrained()->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('profile_views', 'user_id')) {
            Schema::table('profile_views', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            });
        }
    }
};