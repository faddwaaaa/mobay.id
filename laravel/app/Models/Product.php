<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'user_id',
        'product_type',
        'title',
        'description',
        'price',
        'discount',
        'stock',
        'purchase_limit'
    ];

    public function images() {
        return $this->hasMany(ProductImage::class);
    }

    public function files() {
        return $this->hasMany(ProductFile::class);
    }

    public function block()
{
    return $this->hasOne(Block::class);
}

}
