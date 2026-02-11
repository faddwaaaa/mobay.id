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


}
