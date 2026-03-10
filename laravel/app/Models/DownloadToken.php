<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class DownloadToken extends Model
{
    protected $fillable = [
        'digital_order_id',
        'token',
        'buyer_email',
        'download_count',
        'max_downloads',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'download_count' => 'integer',
        'max_downloads' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($token) {
            $token->token = Str::random(64);
        });
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(DigitalOrder::class, 'digital_order_id');
    }

    public function isValid(): bool
    {
        return $this->expires_at->isFuture()
            && $this->download_count < $this->max_downloads;
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isMaxDownloads(): bool
    {
        return $this->download_count >= $this->max_downloads;
    }
}