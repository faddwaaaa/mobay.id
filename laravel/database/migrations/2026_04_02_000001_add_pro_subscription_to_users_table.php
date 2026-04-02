<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('pro_until')->nullable()->comment('Tanggal Pro berakhir');
            $table->enum('pro_type', ['monthly', 'yearly'])->nullable()->comment('Tipe paket Pro: monthly atau yearly');
            $table->string('xendit_invoice_id')->nullable()->unique()->comment('ID invoice dari Xendit');
            $table->string('xendit_external_id')->nullable()->unique()->comment('External ID dari Xendit');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['pro_until', 'pro_type', 'xendit_invoice_id', 'xendit_external_id']);
        });
    }
};
