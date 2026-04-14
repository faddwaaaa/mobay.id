<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // ← tambah ini

return new class extends Migration
{
    public function up()
{
    // Ubah dulu jadi VARCHAR supaya bebas
    DB::statement("
        ALTER TABLE products 
        MODIFY COLUMN product_type VARCHAR(255) NULL
    ");

    // Bersihkan data
    DB::statement("
        UPDATE products 
        SET product_type = 'fisik' 
        WHERE product_type IS NULL
           OR product_type = ''
           OR product_type NOT IN ('fisik','digital')
    ");

    // Ubah ke ENUM final
    DB::statement("
        ALTER TABLE products 
        MODIFY COLUMN product_type 
        ENUM('fisik','digital') 
        NOT NULL DEFAULT 'fisik'
    ");
}
};