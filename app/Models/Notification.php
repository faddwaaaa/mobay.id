<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'icon',
        'link',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: only unread
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope: for current user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Helper: create an order notification
     */
    public static function createOrderNotif(int $userId, string $orderId, string $buyerName, string $productName): self
    {
        return self::create([
            'user_id' => $userId,
            'type'    => 'order',
            'title'   => 'Pesanan Baru Masuk!',
            'message' => "📦 {$buyerName} memesan {$productName} (#{$orderId})",
            'icon'    => 'fas fa-shopping-bag',
            'link'    => route('transactions.history'),
            'is_read' => false,
        ]);
    }

    /**
     * Helper: create a payment notification
     */
    public static function createPaymentNotif(int $userId, string $orderId, string $amount): self
    {
        return self::create([
            'user_id' => $userId,
            'type'    => 'payment',
            'title'   => 'Pembayaran Diterima!',
            'message' => "💰 Pembayaran #{$orderId} sebesar {$amount} berhasil dikonfirmasi.",
            'icon'    => 'fas fa-circle-check',
            'link'    => route('transactions.history'),
            'is_read' => false,
        ]);
    }
}