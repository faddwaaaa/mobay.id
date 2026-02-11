<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->enum('product_type', ['umkm', 'digital'])->default('umkm');
            
            $table->string('title');
            $table->text('description')->nullable();

            // uang
            $table->decimal('price', 15, 2);
            $table->decimal('discount', 15, 2)->nullable();

            // null = unlimited
            $table->integer('stock')->nullable();
            $table->integer('purchase_limit')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('products');
    }
};
