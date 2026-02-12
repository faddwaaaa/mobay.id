<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Block extends Model
{
    protected $fillable = [
        'page_id',
        'type',
        'content',
        'position',
        'is_active',
        'product_id' 
    ];

    protected $casts = [
        'content' => 'array',
    ];

    /*
    | RELATIONS
    */
    public function page()
    {
        return $this->belongsTo(Page::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
