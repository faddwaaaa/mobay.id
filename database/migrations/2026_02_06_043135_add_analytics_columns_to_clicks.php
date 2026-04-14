<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('clicks')) {
            return;
        }

        $afterReferrer = null;
        if (Schema::hasColumn('clicks', 'referrer')) {
            $afterReferrer = 'referrer';
        } elseif (Schema::hasColumn('clicks', 'referer')) {
            $afterReferrer = 'referer';
        }

        Schema::table('clicks', function (Blueprint $table) use ($afterReferrer) {
            if (!Schema::hasColumn('clicks', 'device_type')) {
                $table->string('device_type', 20)->nullable()->after('user_agent');
            }

            if (!Schema::hasColumn('clicks', 'referrer_source')) {
                $column = $table->string('referrer_source', 50)->nullable();
                if ($afterReferrer !== null) {
                    $column->after($afterReferrer);
                }
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('clicks')) {
            return;
        }

        Schema::table('clicks', function (Blueprint $table) {
            if (Schema::hasColumn('clicks', 'referrer_source')) {
                $table->dropColumn('referrer_source');
            }

            if (Schema::hasColumn('clicks', 'device_type')) {
                $table->dropColumn('device_type');
            }
        });
    }
};
