<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('payou_id_clicks', function (Blueprint $table) {
            $table->string('device_type', 20)->nullable()->after('user_agent');
            $table->string('referrer_source', 50)->nullable()->after('referrer');
            $table->index('created_at');
            $table->index('link_id');
        });
    }

    public function down(): void
    {
        Schema::table('payou_id_clicks', function (Blueprint $table) {
            $table->dropColumn(['device_type', 'referrer_source']);
        });
    }
};
