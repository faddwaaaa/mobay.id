<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // ← tambah ini

return new class extends Migration
{
    public function up()
    {
        DB::statement("UPDATE products SET product_type = 'fisik' WHERE product_type NOT IN ('fisik', 'digital')");
        DB::statement("ALTER TABLE products MODIFY COLUMN product_type ENUM('fisik', 'digital') NOT NULL DEFAULT 'fisik'");
    }

    public function down()
    {
        DB::statement("ALTER TABLE products MODIFY COLUMN product_type VARCHAR(255) NOT NULL DEFAULT 'fisik'");
    }
};