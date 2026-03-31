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
        Schema::create('ledgers', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_type'); // payment, payout, fee, etc.
            $table->string('reference_id')->nullable(); // order_id, withdrawal_id, etc.
            $table->unsignedBigInteger('user_id')->nullable();
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('IDR');
            $table->string('description');
            $table->json('metadata')->nullable(); // additional data
            $table->timestamps();

            $table->index(['transaction_type', 'reference_id']);
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ledgers');
    }
};
