<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add balance field to users table for storing user wallet balance
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Balance in cents (Rp) to avoid floating point issues
            // e.g., 100000 = Rp 100.000
            $table->bigInteger('balance')->default(0)->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('balance');
        });
    }
};
