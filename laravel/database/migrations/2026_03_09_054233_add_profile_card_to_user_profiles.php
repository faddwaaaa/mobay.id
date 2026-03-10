<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('user_profiles', 'banner_image')) {
                $table->string('banner_image')->nullable()->after('bg_image');
            }
            if (!Schema::hasColumn('user_profiles', 'about')) {
                $table->text('about')->nullable()->after('banner_image');
            }
            if (!Schema::hasColumn('user_profiles', 'text_color')) {
                $table->string('text_color', 20)->default('#111827')->after('about');
            }
            if (!Schema::hasColumn('user_profiles', 'social_links')) {
                $table->json('social_links')->nullable()->after('text_color');
            }
        });
    }

    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $cols = ['banner_image', 'about', 'text_color', 'social_links'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('user_profiles', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};