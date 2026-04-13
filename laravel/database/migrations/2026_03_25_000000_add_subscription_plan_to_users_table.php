<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Cek apakah kolom sudah ada sebelum menambahkan
        if (!Schema::hasColumn('users', 'subscription_plan')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('subscription_plan', 20)->default('free')->after('role');
            });

            DB::table('users')
                ->whereNull('subscription_plan')
                ->update(['subscription_plan' => 'free']);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('subscription_plan');
        });
    }
};
