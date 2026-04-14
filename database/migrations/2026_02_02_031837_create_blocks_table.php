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
        Schema::create('blocks', function (Blueprint $table) {
            $table->id();

            // Relasi ke page
            $table->foreignId('page_id')
                ->constrained()
                ->cascadeOnDelete();

            // Tipe block (text, image, link, video, product)
            $table->string('type');

            // Relasi ke product (optional). FK ditambahkan di migration terpisah
            // karena tabel products dibuat setelah migration ini.
            $table->unsignedBigInteger('product_id')->nullable();

            // Content fleksibel (nullable untuk product block)
            $table->json('content')->nullable();

            // Urutan block
            $table->integer('position')->default(0);

            // Status aktif
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blocks');
    }
};
