<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DigitalProduct extends Model
{
    protected $fillable = [
        'product_id',   // ✅ relasi ke tabel products yang sudah ada
        'name',
        'description',
        'price',
        'file_path',
        'file_name',
        'is_active',
    ];

    protected $casts = [
        'price'     => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(DigitalOrder::class);
    }

    // Relasi ke tabel products yang sudah ada
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}