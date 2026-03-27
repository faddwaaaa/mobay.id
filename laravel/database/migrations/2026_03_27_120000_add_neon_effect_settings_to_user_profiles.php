<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('user_profiles', 'btn_glow_color')) {
                $table->string('btn_glow_color', 20)->nullable()->default('#38bdf8')->after('btn_text_color');
            }

            if (!Schema::hasColumn('user_profiles', 'btn_glow_bg')) {
                $table->string('btn_glow_bg', 20)->nullable()->default('#111827')->after('btn_glow_color');
            }
        });
    }

    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            foreach (['btn_glow_color', 'btn_glow_bg'] as $col) {
                if (Schema::hasColumn('user_profiles', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
