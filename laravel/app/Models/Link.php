<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// IMPORT MODEL YANG DIPAKAI
use App\Models\User;
use App\Models\Click;

class Link extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'url',
        'icon',
        'position',
        'is_active',
        'clicks',
    ];

    /**
     * Link milik satu user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi klik (opsional)
     */
    public function clicks()
    {
        return $this->hasMany(Click::class);
    }
}
