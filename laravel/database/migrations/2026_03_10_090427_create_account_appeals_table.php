<?php
// ================================================================
// FILE: database/migrations/xxxx_create_account_appeals_table.php
// Jalankan: php artisan migrate
// ================================================================

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('account_appeals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('ticket_code', 20)->unique(); // APL-XXXXXXXX
            $table->text('reason');                      // Alasan banding dari user
            $table->text('additional_info')->nullable(); // Info tambahan / bukti
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('admin_note')->nullable();      // Catatan keputusan admin
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('account_appeals');
    }
};