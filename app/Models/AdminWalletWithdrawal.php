<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminWalletWithdrawal extends Model
{
    use HasFactory;

    protected $fillable = [
        'amount',
        'bank_name',
        'account_name',
        'account_number',
        'status',
        'payout_id',
        'midtrans_response',
        'notes',
        'rejection_reason',
        'requested_by',
        'processed_by',
        'processed_at',
        'ip_address',
    ];

    protected $casts = [
        'amount' => 'integer',
        'midtrans_response' => 'array',
        'processed_at' => 'datetime',
    ];

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}

