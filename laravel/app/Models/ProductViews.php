<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductViews extends Model
{
    protected $fillable = ['product_id'];
    public $timestamps = true;

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}


