<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Cache destinasi dari Komerce API (RajaOngkir baru)
        Schema::create('rajaongkir_cities', function (Blueprint $table) {
            $table->unsignedBigInteger('city_id')->primary(); // id dari Komerce API
            $table->unsignedInteger('province_id')->default(0);
            $table->string('province');
            $table->string('type')->nullable();        // Kecamatan / Kota / Kabupaten
            $table->string('city_name');
            $table->string('postal_code', 10)->nullable();
            $table->string('label')->nullable();       // Label lengkap dari API
            $table->string('subdistrict')->nullable(); // Kecamatan
            $table->timestamps();

            $table->index(['city_name']);
            $table->index(['province']);
        });

        Schema::create('rajaongkir_provinces', function (Blueprint $table) {
            $table->unsignedInteger('province_id')->primary();
            $table->string('province');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rajaongkir_cities');
        Schema::dropIfExists('rajaongkir_provinces');
    }
};