<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RajaongkirProvince extends Model
{
    protected $table      = 'rajaongkir_provinces';
    protected $primaryKey = 'province_id';
    public    $incrementing = false;

    protected $fillable = ['province_id', 'province'];
}