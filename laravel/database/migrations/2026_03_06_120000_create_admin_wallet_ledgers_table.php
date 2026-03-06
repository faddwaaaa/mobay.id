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
        Schema::create('admin_wallet_ledgers', function (Blueprint $table) {
            $table->id();
            $table->enum('source', ['fee_payment', 'fee_withdraw', 'manual_adjustment'])->default('manual_adjustment');
            $table->enum('direction', ['credit', 'debit'])->default('credit');
            $table->unsignedBigInteger('amount');
            $table->unsignedBigInteger('balance_after')->default(0);
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['source', 'created_at']);
            $table->index(['reference_type', 'reference_id']);
            $table->index('created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_wallet_ledgers');
    }
};

