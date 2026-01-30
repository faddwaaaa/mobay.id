<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Create withdrawals table for tracking manual withdrawal requests
     * Withdrawals are NOT processed through Midtrans, only through manual admin approval
     */
    public function up(): void
    {
        Schema::create('withdrawals', function (Blueprint $table) {
            $table->id();
            
            // Foreign key to user (who requested withdrawal)
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');
            
            // Withdrawal details
            $table->bigInteger('amount'); // in Rupiah (cents)
            $table->enum('status', [
                'pending',      // Waiting for admin approval
                'approved',     // Approved by admin, balance deducted
                'rejected',     // Rejected by admin
                'completed',    // Funds transferred
                'cancelled'     // Cancelled by user
            ])->default('pending');
            
            // Bank account details (optional, for reference)
            $table->string('bank_name')->nullable();
            $table->string('account_name')->nullable();
            $table->string('account_number')->nullable();
            
            // Admin approval tracking
            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            
            // Notes from user or admin
            $table->text('notes')->nullable();
            $table->text('rejection_reason')->nullable();
            
            // Metadata
            $table->ipAddress('ip_address')->nullable();
            
            // Timestamps
            $table->timestamps();
            
            // Indexes for performance
            $table->index('user_id');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('withdrawals');
    }
};
