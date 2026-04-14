<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('physical_orders', function (Blueprint $table) {
            $table->id();

            // Relasi ke product yang sudah ada
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();

            // Relasi ke seller (user pemilik produk)
            $table->foreignId('seller_id')->constrained('users')->cascadeOnDelete();

            // Info pembeli (tidak harus punya akun)
            $table->string('buyer_name');
            $table->string('buyer_email');
            $table->string('buyer_phone')->nullable();

            // Kode unik order (pola sama seperti DigitalOrder)
            $table->string('order_code')->unique(); // contoh: FORD-XXXXXXXXXX

            // Detail produk (snapshot saat checkout, biar tidak berubah kalau produk diedit)
            $table->string('product_name');
            $table->decimal('product_price', 12, 2);
            $table->integer('quantity')->default(1);

            // Ongkos kirim
            $table->decimal('shipping_cost', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2);

            // Alamat pengiriman
            $table->text('shipping_address');
            $table->string('shipping_city');
            $table->string('shipping_province');
            $table->string('shipping_postal_code', 10);

            // Status order
            // pending → paid → processing → shipped → delivered
            $table->enum('status', [
                'pending',
                'paid',
                'processing',
                'shipped',
                'delivered',
                'cancelled',
                'returned',
            ])->default('pending');

            // Pembayaran (Midtrans, sama seperti Transaction yang sudah ada)
            $table->string('midtrans_order_id')->nullable()->unique();
            $table->string('midtrans_transaction_id')->nullable();
            $table->string('payment_method')->nullable();
            $table->json('midtrans_response')->nullable();
            $table->timestamp('paid_at')->nullable();

            // Info pengiriman (diisi admin/seller saat input resi)
            $table->string('courier_code')->nullable();     // jne, sicepat, jnt, dll
            $table->string('courier_service')->nullable();  // REG, YES, OKE, dll
            $table->string('tracking_number')->nullable();
            $table->string('tracking_url')->nullable();
            $table->string('estimated_arrival')->nullable();
            $table->timestamp('shipped_at')->nullable();

            // Biteship webhook
            $table->string('biteship_order_id')->nullable();
            $table->string('biteship_status')->nullable();

            $table->timestamps();

            // Index
            $table->index('seller_id');
            $table->index('buyer_email');
            $table->index('status');
            $table->index('tracking_number');
            $table->index('biteship_order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('physical_orders');
    }
};