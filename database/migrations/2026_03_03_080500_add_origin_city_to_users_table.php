<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // village_code 10 digit dari api.co.id
            $table->string('origin_village_code', 15)->nullable()->after('email');
            // Label lengkap: "Purwokerto Utara, Purwokerto Utara, Banyumas"
            $table->string('origin_city_name')->nullable()->after('origin_village_code');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['origin_village_code', 'origin_city_name']);
        });
    }
};