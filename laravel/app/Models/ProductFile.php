<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductFile extends Model
{
    public $timestamps = false;
    protected $fillable = ['product_id','file'];
}
