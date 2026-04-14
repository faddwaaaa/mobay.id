<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentAccountAuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'payment_account_id',
        'action',
        'ip_address',
        'user_agent',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function paymentAccount(): BelongsTo
    {
        return $this->belongsTo(PaymentAccount::class);
    }

    // -----------------------------------------------------------------------
    // Static helper to write a log entry quickly
    // -----------------------------------------------------------------------

    public static function record(
        int $userId,
        string $action,
        ?int $accountId = null,
        array $metadata = []
    ): void {
        static::create([
            'user_id'            => $userId,
            'payment_account_id' => $accountId,
            'action'             => $action,
            'ip_address'         => request()->ip(),
            'user_agent'         => request()->userAgent(),
            'metadata'           => $metadata,
        ]);
    }
}