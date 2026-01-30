<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Create transactions table for tracking Midtrans top-up transactions
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            
            // Foreign key to user
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');
            
            // Midtrans identifiers
            $table->string('order_id')->unique();
            $table->string('transaction_id')->nullable()->unique();
            
            // Transaction details
            $table->bigInteger('amount'); // in Rupiah (cents)
            $table->enum('status', [
                'pending',      // Waiting for payment
                'settlement',   // Payment successful
                'failed',       // Payment failed
                'expired',      // Payment expired
                'denied',       // Payment denied
                'cancelled'     // Cancelled by system
            ])->default('pending');
            
            // Payment method from Midtrans
            $table->string('payment_method')->nullable();
            
            // Store full Midtrans response as JSON
            $table->json('midtrans_response')->nullable();
            
            // Metadata
            $table->text('notes')->nullable();
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
        Schema::dropIfExists('transactions');
    }
};
