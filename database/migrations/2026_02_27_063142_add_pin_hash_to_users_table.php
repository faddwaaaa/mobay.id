<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // PIN untuk konfirmasi penarikan / tambah rekening
            // Disimpan sebagai bcrypt hash, BUKAN plaintext
            $table->string('pin_hash')->nullable()->after('password');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('pin_hash');
        });
    }
};