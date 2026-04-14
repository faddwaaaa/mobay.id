<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('product_sales') && !Schema::hasColumn('product_sales', 'options')) {
            Schema::table('product_sales', function (Blueprint $table) {
                $table->mediumText('options')->nullable()->after('qty');
            });
        }

        if (Schema::hasTable('transactions')) {
            if (!Schema::hasColumn('transactions', 'notes')) {
                Schema::table('transactions', function (Blueprint $table) {
                    $table->text('notes')->nullable();
                });
            }

            if (!Schema::hasColumn('transactions', 'ip_address')) {
                Schema::table('transactions', function (Blueprint $table) {
                    $table->string('ip_address', 45)->nullable()->after('notes');
                });
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('product_sales') && Schema::hasColumn('product_sales', 'options')) {
            Schema::table('product_sales', function (Blueprint $table) {
                $table->dropColumn('options');
            });
        }

        if (Schema::hasTable('transactions')) {
            if (Schema::hasColumn('transactions', 'ip_address')) {
                Schema::table('transactions', function (Blueprint $table) {
                    $table->dropColumn('ip_address');
                });
            }

            if (Schema::hasColumn('transactions', 'notes')) {
                Schema::table('transactions', function (Blueprint $table) {
                    $table->dropColumn('notes');
                });
            }
        }
    }
};
