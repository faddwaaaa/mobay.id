<?php
// database/migrations/xxxx_create_download_otps_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('download_otps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('download_token_id')->constrained('download_tokens')->cascadeOnDelete();
            $table->string('otp', 6);
            $table->timestamp('expires_at');
            $table->boolean('used')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('download_otps');
    }
};