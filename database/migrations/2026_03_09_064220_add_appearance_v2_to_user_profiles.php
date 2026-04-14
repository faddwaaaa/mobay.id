<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            // Gradient (belum ada sama sekali)
            if (!Schema::hasColumn('user_profiles', 'bg_gradient_start')) {
                $table->string('bg_gradient_start', 20)->nullable()->after('bg_type');
            }
            if (!Schema::hasColumn('user_profiles', 'bg_gradient_end')) {
                $table->string('bg_gradient_end', 20)->nullable()->after('bg_gradient_start');
            }
            if (!Schema::hasColumn('user_profiles', 'bg_gradient_direction')) {
                $table->string('bg_gradient_direction', 30)->nullable()->default('to bottom')->after('bg_gradient_end');
            }

            // Button (nama baru, bukan replace kolom lama)
            if (!Schema::hasColumn('user_profiles', 'btn_style')) {
                $table->string('btn_style', 20)->nullable()->default('fill')->after('bg_gradient_direction');
            }
            if (!Schema::hasColumn('user_profiles', 'btn_shape')) {
                $table->string('btn_shape', 20)->nullable()->default('rounded')->after('btn_style');
            }
            if (!Schema::hasColumn('user_profiles', 'btn_color')) {
                $table->string('btn_color', 20)->nullable()->default('#3b82f6')->after('btn_shape');
            }
            if (!Schema::hasColumn('user_profiles', 'btn_text_color')) {
                $table->string('btn_text_color', 20)->nullable()->default('#ffffff')->after('btn_color');
            }

            // Font family (nama baru, kolom 'font' tetap dibiarkan)
            if (!Schema::hasColumn('user_profiles', 'font_family')) {
                $table->string('font_family', 50)->nullable()->default('Plus Jakarta Sans')->after('btn_text_color');
            }
        });
    }

    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $cols = [
                'bg_gradient_start', 'bg_gradient_end', 'bg_gradient_direction',
                'btn_style', 'btn_shape', 'btn_color', 'btn_text_color', 'font_family',
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('user_profiles', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};