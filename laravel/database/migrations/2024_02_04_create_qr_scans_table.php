<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tambahkan kolom di tabel users untuk tracking
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('qr_scans')->default(0)->after('email');
        });
        
        // Tabel terpisah untuk log scan detail (opsional)
        Schema::create('qr_scans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('device_type', 50)->nullable(); // mobile, desktop, tablet
            $table->string('referrer')->nullable();
            $table->timestamp('scanned_at');
            $table->timestamps();
            
            $table->index(['user_id', 'scanned_at']);
            $table->index('scanned_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('qr_scans');
        });
        
        Schema::dropIfExists('qr_scans');
    }
};
