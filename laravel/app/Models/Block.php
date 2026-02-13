<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Block extends Model
{
    use HasFactory;

    protected $fillable = [
        'page_id',
        'type',
        'content',
        'product_id',
        'position',
    ];

    protected $casts = [
        'content' => 'array',
        'position' => 'integer',
    ];

    /**
     * Relationship with Page
     */
    public function page()
    {
        return $this->belongsTo(Page::class);
    }

    /**
     * Relationship with Product
     * 🔥 PENTING: Ini diperlukan untuk load product data
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Boot method untuk auto-ordering
     */
    protected static function boot()
    {
        parent::boot();

        // Auto set position saat create
        static::creating(function ($block) {
            if (is_null($block->position)) {
                $maxPosition = static::where('page_id', $block->page_id)->max('position') ?? 0;
                $block->position = $maxPosition + 1;
            }
        });
    }
}