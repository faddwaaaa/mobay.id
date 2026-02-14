<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_sales', function (Blueprint $table) {
            // Tambah kolom options jika belum ada
            if (!Schema::hasColumn('product_sales', 'options')) {
                $table->mediumText('options')->nullable()->after('qty');
            }

            // Tambah kolom notes dan ip_address di transactions jika belum ada
            if (!Schema::hasColumn('transactions', 'notes')) {
                $table->text('notes')->nullable()->after('midtrans_response');
            }
            if (!Schema::hasColumn('transactions', 'ip_address')) {
                $table->string('ip_address', 45)->nullable()->after('notes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('product_sales', function (Blueprint $table) {
            $table->dropColumn('options');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['notes', 'ip_address']);
        });
    }
};
