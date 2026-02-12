<?php
// database/migrations/xxxx_xx_xx_create_clicks_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('clicks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('link_id');
            $table->unsignedBigInteger('user_id');
            $table->string('ip_address', 255);
            $table->string('user_agent', 255);
            $table->string('referer', 255)->nullable();
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
            
            $table->foreign('link_id')->references('id')->on('links')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            $table->index(['link_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index('ip_address');
        });
    }

    public function down()
    {
        Schema::dropIfExists('clicks');
    }
};