<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RajaongkirCity extends Model
{
    protected $table      = 'rajaongkir_cities';
    protected $primaryKey = 'village_code';
    public    $incrementing = false;
    protected $keyType    = 'string';

    protected $fillable = [
        'village_code',
        'village_name',
        'district_name',
        'city_name',
        'province',
    ];
}