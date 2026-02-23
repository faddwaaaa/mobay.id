<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cart extends Model
{
    protected $fillable = [
        'session_id',
        'user_id',
        'product_id',
        'quantity',
    ];

    // ─── Relasi ───────────────────────────────────────────────
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ─── Scope: ambil cart berdasarkan session aktif ──────────
    public function scopeForSession($query, string $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    // ─── Hitung subtotal item ini ─────────────────────────────
    public function getSubtotalAttribute(): float
    {
        $price = $this->product->discount > 0
            ? $this->product->price - ($this->product->price * $this->product->discount / 100)
            : $this->product->price;

        return $this->final_price * $this->quantity;
    }

    // ─── Final price per item (setelah diskon) ────────────────
    public function getFinalPriceAttribute(): float
{
    $product = $this->product;

    // discount adalah harga diskon langsung, bukan persentase
    return ($product->discount && $product->discount > 0 && $product->discount < $product->price)
        ? $product->discount
        : $product->price;
}

}