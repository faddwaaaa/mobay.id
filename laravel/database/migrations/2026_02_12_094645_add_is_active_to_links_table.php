<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('links', function (Blueprint $table) {
            if (!Schema::hasColumn('links', 'is_active')) {
                $table->tinyInteger('is_active')->default(1)->after('views');
            }
        });
    }

    public function down()
    {
        Schema::table('links', function (Blueprint $table) {
            if (Schema::hasColumn('links', 'is_active')) {
                $table->dropColumn('is_active');
            }
        });
    }
};