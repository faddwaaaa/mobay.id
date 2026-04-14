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
        Schema::create('admin_wallet_withdrawals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('amount');
            $table->string('bank_name');
            $table->string('account_name');
            $table->string('account_number');
            $table->enum('status', ['pending', 'approved', 'completed', 'rejected'])->default('pending');
            $table->string('payout_id')->nullable()->index();
            $table->json('midtrans_response')->nullable();
            $table->text('notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->foreignId('requested_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('processed_at')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_wallet_withdrawals');
    }
};

