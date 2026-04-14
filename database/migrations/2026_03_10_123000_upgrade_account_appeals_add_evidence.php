<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('account_appeals', function (Blueprint $table) {
            if (!Schema::hasColumn('account_appeals', 'evidence_paths')) {
                $table->json('evidence_paths')->nullable()->after('additional_info');
            }

            if (!Schema::hasColumn('account_appeals', 'evidence_count')) {
                $table->unsignedTinyInteger('evidence_count')->default(0)->after('evidence_paths');
            }
        });
    }

    public function down(): void
    {
        Schema::table('account_appeals', function (Blueprint $table) {
            $drops = [];

            if (Schema::hasColumn('account_appeals', 'evidence_count')) {
                $drops[] = 'evidence_count';
            }

            if (Schema::hasColumn('account_appeals', 'evidence_paths')) {
                $drops[] = 'evidence_paths';
            }

            if (!empty($drops)) {
                $table->dropColumn($drops);
            }
        });
    }
};
