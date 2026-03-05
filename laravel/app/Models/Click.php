<?php
// app/Models/Click.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Click extends Model
{
    protected $fillable = [
    'link_id',
    'ip_address',
    'user_agent',
    'referrer',  
    'referrer_source',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function link()
    {
        return $this->belongsTo(Link::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}