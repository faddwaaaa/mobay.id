<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Berat produk dalam gram (wajib untuk kalkulasi ongkir)
            $table->unsignedInteger('weight')->default(1000)->after('purchase_limit')->comment('Berat dalam gram');

            // Hapus shipping_cost flat (diganti RajaOngkir)
            if (Schema::hasColumn('products', 'shipping_cost')) {
                $table->dropColumn('shipping_cost');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('weight');
            $table->bigInteger('shipping_cost')->nullable()->after('purchase_limit');
        });
    }
};