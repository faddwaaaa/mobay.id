<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rajaongkir_cities', function (Blueprint $table) {
            $table->string('village_code', 15)->primary(); // kode kelurahan 10 digit dari api.co.id
            $table->string('village_name');
            $table->string('district_name');
            $table->string('city_name');
            $table->string('province');
            $table->timestamps();

            $table->index('city_name');
            $table->index('village_name');
            $table->index('province');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rajaongkir_cities');
    }
};