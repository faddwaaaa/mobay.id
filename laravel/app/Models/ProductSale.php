<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductSale extends Model
{
    protected $fillable = ['product_id', 'qty'];
    public $timestamps = true;
}
