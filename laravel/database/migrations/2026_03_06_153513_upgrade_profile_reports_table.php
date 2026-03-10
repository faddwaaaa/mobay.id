<?php
// ═══════════════════════════════════════════════════════════════════
// FILE 1: database/migrations/xxxx_upgrade_profile_reports_table.php
// ═══════════════════════════════════════════════════════════════════

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profile_reports', function (Blueprint $table) {
            // Bukti (path JSON array)
            if (!Schema::hasColumn('profile_reports', 'evidence_paths')) {
                $table->json('evidence_paths')->nullable()->after('detail');
            }
            // Jumlah file bukti (denormalized untuk query cepat)
            if (!Schema::hasColumn('profile_reports', 'evidence_count')) {
                $table->unsignedTinyInteger('evidence_count')->default(0)->after('evidence_paths');
            }
            // Ticket code unik untuk user reference
            if (!Schema::hasColumn('profile_reports', 'ticket_code')) {
                $table->string('ticket_code', 16)->nullable()->unique()->after('id');
            }
            // Catatan internal moderator
            if (!Schema::hasColumn('profile_reports', 'moderator_note')) {
                $table->text('moderator_note')->nullable()->after('reviewed_at');
            }
            // Auto-freeze flag (sistem set otomatis, bukan suspend)
            if (!Schema::hasColumn('profile_reports', 'triggered_freeze')) {
                $table->boolean('triggered_freeze')->default(false)->after('moderator_note');
            }
        });
    }

    public function down(): void
    {
        Schema::table('profile_reports', function (Blueprint $table) {
            $table->dropColumn(['evidence_paths','evidence_count','ticket_code','moderator_note','triggered_freeze']);
        });
    }
};