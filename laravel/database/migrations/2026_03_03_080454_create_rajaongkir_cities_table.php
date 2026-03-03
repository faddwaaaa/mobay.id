<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Cache kota dari RajaOngkir agar tidak hit API setiap saat
        Schema::create('rajaongkir_cities', function (Blueprint $table) {
            $table->unsignedInteger('city_id')->primary(); // city_id dari RajaOngkir
            $table->unsignedInteger('province_id');
            $table->string('province');
            $table->string('type');       // Kota / Kabupaten
            $table->string('city_name');
            $table->string('postal_code', 10)->nullable();
            $table->timestamps();

            $table->index(['city_name']);
            $table->index(['province_id']);
        });

        // Cache provinsi
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