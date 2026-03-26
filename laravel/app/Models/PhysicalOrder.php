<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class PhysicalOrder extends Model
{
    protected $fillable = [
        'product_id',
        'seller_id',
        'buyer_name',
        'buyer_email',
        'buyer_phone',
        'order_code',
        'product_name',
        'product_price',
        'quantity',
        'shipping_cost',
        'total_amount',
        'shipping_address',
        'shipping_city',
        'shipping_province',
        'shipping_postal_code',
        'status',
        'midtrans_order_id',
        'midtrans_transaction_id',
        'payment_method',
        'midtrans_response',
        'paid_at',
        'courier_code',
        'courier_service',
        'tracking_number',
        'tracking_url',
        'estimated_arrival',
        'shipped_at',
        'biteship_order_id',
        'biteship_status',
    ];

    protected $casts = [
        'midtrans_response' => 'array',
        'paid_at'           => 'datetime',
        'shipped_at'        => 'datetime',
        'product_price'     => 'decimal:2',
        'shipping_cost'     => 'decimal:2',
        'total_amount'      => 'decimal:2',
    ];

    // Auto-generate order_code saat creating (pola sama seperti DigitalOrder)
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            $order->order_code = 'FORD-' . strtoupper(Str::random(10));
        });
    }

    // ===== RELATIONS =====

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    // ===== HELPERS =====

    public function statusLabel(): string
    {
        return match($this->status) {
            'pending'    => 'Menunggu Pembayaran',
            'paid'       => 'Dibayar',
            'processing' => 'Diproses',
            'shipped'    => 'Dikirim',
            'delivered'  => 'Diterima',
            'cancelled'  => 'Dibatalkan',
            'returned'   => 'Diretur',
            default      => ucfirst($this->status),
        };
    }

    public function isReadyToShip(): bool
    {
        return $this->status === 'paid';
    }

    public function formattedTotal(): string
    {
        return 'Rp ' . number_format($this->total_amount, 0, ',', '.');
    }
}