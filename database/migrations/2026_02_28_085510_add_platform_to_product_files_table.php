<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('product_files', function (Blueprint $table) {
            // platform: 'upload' | 'dropbox' | 'gdrive' | 'other'
            $table->string('platform')->default('upload')->after('file');
            // url eksternal (dropbox, gdrive, other) — null kalau upload biasa
            $table->text('file_url')->nullable()->after('platform');

            // file boleh null kalau pakai URL
            $table->string('file')->nullable()->change();
        });
    }

    public function down(): void {
        Schema::table('product_files', function (Blueprint $table) {
            $table->dropColumn(['platform', 'file_url']);
            $table->string('file')->nullable(false)->change();
        });
    }
};