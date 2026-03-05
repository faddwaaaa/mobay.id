<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'shipping_enabled')) {
                $table->boolean('shipping_enabled')
                    ->default(true)
                    ->after('weight')
                    ->comment('Aktifkan ongkir otomatis untuk produk fisik');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'shipping_enabled')) {
                $table->dropColumn('shipping_enabled');
            }
        });
    }
};

