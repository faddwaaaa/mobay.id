<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('blocks')) {
            return;
        }

        if (!Schema::hasColumn('blocks', 'product_id')) {
            Schema::table('blocks', function (Blueprint $table) {
                $table->unsignedBigInteger('product_id')->nullable()->after('content');
            });
        }

        if (Schema::hasTable('products') && !$this->hasForeignKey('blocks', 'blocks_product_id_foreign')) {
            Schema::table('blocks', function (Blueprint $table) {
                $table->foreign('product_id')
                    ->references('id')
                    ->on('products')
                    ->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('blocks')) {
            return;
        }

        if ($this->hasForeignKey('blocks', 'blocks_product_id_foreign')) {
            Schema::table('blocks', function (Blueprint $table) {
                $table->dropForeign('blocks_product_id_foreign');
            });
        }

        if (Schema::hasColumn('blocks', 'product_id')) {
            Schema::table('blocks', function (Blueprint $table) {
                $table->dropColumn('product_id');
            });
        }
    }

    private function hasForeignKey(string $table, string $constraint): bool
    {
        return DB::table('information_schema.TABLE_CONSTRAINTS')
            ->where('CONSTRAINT_SCHEMA', DB::raw('DATABASE()'))
            ->where('TABLE_NAME', $table)
            ->where('CONSTRAINT_NAME', $constraint)
            ->where('CONSTRAINT_TYPE', 'FOREIGN KEY')
            ->exists();
    }
};
