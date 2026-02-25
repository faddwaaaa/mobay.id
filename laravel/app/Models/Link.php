<?php
// app/Models/Link.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    protected $fillable = [
        'user_id',
        'url',
        'short_code',
        'title',
        'description',
        'views',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'views' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function click()
    {
        return $this->hasMany(Click::class);
    }

    // Accessor untuk total klik
    public function getClicksCountAttribute()
    {
        return $this->click()->count();
    }

    // Accessor untuk unique visitors
    public function getUniqueVisitorsAttribute()
    {
        return $this->click()->distinct('ip_address')->count('ip_address');
    }
}