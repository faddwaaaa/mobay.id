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
    Schema::table('product_sales', function (Blueprint $table) {
        if (!Schema::hasColumn('product_sales', 'price')) {
            $table->unsignedBigInteger('price')->default(0)->after('qty');
        }
    });
}

public function down(): void
{
    Schema::table('product_sales', function (Blueprint $table) {
        $table->dropColumn('price');
    });
}
};
