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
        Schema::create('payment_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->constrained()
                  ->onDelete('cascade');

            // Bank info
            $table->string('bank_code', 20);        // e.g. BCA, BRI, MANDIRI
            $table->string('bank_name', 100);       // e.g. Bank Central Asia

            // Account number stored encrypted (AES-256)
            // Never store plaintext — use Laravel's encrypted cast
            $table->text('account_number_encrypted'); // encrypted via Model cast
            $table->string('account_number_last4', 4); // for display masking only

            // Account holder name (encrypted)
            $table->text('account_holder_encrypted');

            // Optional user-defined label
            $table->string('label', 50)->nullable();

            // Flags
            $table->boolean('is_default')->default(false);
            $table->boolean('is_verified')->default(false); // verified via bank inquiry

            // Soft deletes for audit trail (never hard-delete financial data)
            $table->softDeletes();
            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'is_default']);
            $table->index(['user_id', 'deleted_at']);
        });

        Schema::create('payment_account_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('payment_account_id')->nullable()->constrained('payment_accounts')->onDelete('set null');
            $table->string('action', 50);     // created, deleted, set_default, verify_attempt, pin_failed
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->json('metadata')->nullable(); // extra context (bank_code, masked number, etc.)
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_account_audit_logs');
        Schema::dropIfExists('payment_accounts');
    }
};