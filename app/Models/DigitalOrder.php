<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class DigitalOrder extends Model
{
    protected $fillable = [
        'digital_product_id',
        'buyer_email',
        'buyer_name',
        'order_code',
        'status',
        'amount',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            $order->order_code = 'ORD-' . strtoupper(Str::random(10));
        });
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(DigitalProduct::class, 'digital_product_id');
    }

    public function downloadToken(): HasOne
    {
        return $this->hasOne(DownloadToken::class);
    }
}