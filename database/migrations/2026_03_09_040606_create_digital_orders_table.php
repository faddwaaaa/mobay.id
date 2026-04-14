<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('digital_products', function (Blueprint $table) {
            $table->id();
            // ✅ Relasi ke tabel products yang sudah ada
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('file_path');
            $table->string('file_name');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('digital_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('digital_product_id')->constrained()->cascadeOnDelete();
            $table->string('buyer_email');
            $table->string('buyer_name');
            $table->string('order_code')->unique();
            $table->enum('status', ['pending', 'paid', 'cancelled'])->default('pending');
            $table->decimal('amount', 10, 2);
            $table->timestamps();
        });

        Schema::create('download_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('digital_order_id')->constrained()->cascadeOnDelete();
            $table->string('token', 64)->unique();
            $table->string('buyer_email');
            $table->integer('download_count')->default(0);
            $table->integer('max_downloads')->default(3);
            $table->timestamp('expires_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('download_tokens');
        Schema::dropIfExists('digital_orders');
        Schema::dropIfExists('digital_products');
    }
};