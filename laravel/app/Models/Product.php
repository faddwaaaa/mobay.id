<?php

namespace App\Models;
use App\Models\ProductSale;
use App\Models\ProductViews;

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
        'purchase_limit',
        'weight'
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

public function views()
{
    return $this->hasMany(ProductViews::class);
}

public function transaction()
{
    return $this->hasMany(Transaction::class);
}

public function sales()
{
    return $this->hasMany(ProductSale::class);
}
}
