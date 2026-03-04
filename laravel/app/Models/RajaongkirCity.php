<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RajaongkirCity extends Model
{
    protected $table    = 'rajaongkir_cities';
    protected $fillable = ['city_name', 'province'];
}