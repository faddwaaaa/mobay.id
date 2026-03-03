<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RajaongkirCity extends Model
{
    protected $table      = 'rajaongkir_cities';
    protected $primaryKey = 'city_id';
    public    $incrementing = false;

    protected $fillable = [
        'city_id',
        'province_id',
        'province',
        'type',
        'city_name',
        'postal_code',
    ];

    /**
     * Label lengkap untuk dropdown: "Kota Bandung, Jawa Barat"
     */
    public function getFullLabelAttribute(): string
    {
        return "{$this->type} {$this->city_name}, {$this->province}";
    }
}