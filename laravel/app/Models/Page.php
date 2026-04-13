<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = [
        'user_id', 'title', 'slug', 'position', 'is_active'
    ];

    public function blocks()
    {
        return $this->hasMany(Block::class)->orderBy('position');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Link milik user yang sama dengan page ini.
     * Karena Link tidak punya page_id, kita ambil lewat user.
     */
    public function links()
    {
        return $this->hasManyThrough(
            Link::class,  // model tujuan
            User::class,  // model perantara
            'id',         // FK di users (dicocokkan dengan pages.user_id)
            'user_id',    // FK di links
            'user_id',    // local key di pages
            'id'          // local key di users
        );
    }
}