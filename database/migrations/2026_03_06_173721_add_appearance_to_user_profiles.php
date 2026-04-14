<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            // Background
            $table->string('bg_type')->default('color')->after('background_color'); // color | gradient | image
            $table->string('bg_gradient_start')->nullable()->after('bg_type');
            $table->string('bg_gradient_end')->nullable()->after('bg_gradient_start');
            $table->string('bg_gradient_direction')->default('to bottom')->after('bg_gradient_end');
            $table->string('bg_image')->nullable()->after('bg_gradient_direction'); // path ke storage

            // Button style
            $table->string('btn_style')->default('fill')->after('bg_image'); // fill | outline | hard_shadow | soft_shadow
            $table->string('btn_shape')->default('rounded')->after('btn_style'); // square | rounded | pill
            $table->string('btn_color')->default('#2563eb')->after('btn_shape');
            $table->string('btn_text_color')->default('#ffffff')->after('btn_color');

            // Font
            $table->string('font_family')->default('Plus Jakarta Sans')->after('btn_text_color');

            // Template
            $table->string('template')->default('classic')->after('font_family');
        });
    }

    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'bg_type', 'bg_gradient_start', 'bg_gradient_end',
                'bg_gradient_direction', 'bg_image',
                'btn_style', 'btn_shape', 'btn_color', 'btn_text_color',
                'font_family', 'template'
            ]);
        });
    }
};