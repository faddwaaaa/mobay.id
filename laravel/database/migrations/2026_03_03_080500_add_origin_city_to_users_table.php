<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // city_id dari RajaOngkir — kota asal pengiriman seller
            $table->unsignedInteger('origin_city_id')->nullable()->after('email')->comment('ID kota asal (RajaOngkir)');
            $table->string('origin_city_name')->nullable()->after('origin_city_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['origin_city_id', 'origin_city_name']);
        });
    }
};