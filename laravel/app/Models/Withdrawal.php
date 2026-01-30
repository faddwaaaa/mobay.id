<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Withdrawal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'status',
        'bank_name',
        'account_name',
        'account_number',
        'approved_by',
        'approved_at',
        'notes',
        'rejection_reason',
        'ip_address',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ===== RELATIONS =====

    /**
     * Get the user that requested the withdrawal
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin who approved the withdrawal
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // ===== SCOPES =====

    /**
     * Filter withdrawals by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Filter pending withdrawals
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Filter approved withdrawals
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Filter completed withdrawals
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Filter for date range
     */
    public function scopeForDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    // ===== METHODS =====

    /**
     * Check if withdrawal is pending approval
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if withdrawal is approved
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if withdrawal is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if withdrawal is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Format amount to Rupiah currency
     */
    public function formattedAmount(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    /**
     * Approve withdrawal and deduct balance
     * Must be called within a database transaction
     */
    public function approve(int $approvedByUserId): bool
    {
        if (!$this->isPending()) {
            return false;
        }

        $this->update([
            'status' => 'approved',
            'approved_by' => $approvedByUserId,
            'approved_at' => now(),
        ]);

        // Deduct balance from user
        $this->user->decrement('balance', $this->amount);

        return true;
    }

    /**
     * Reject withdrawal with reason
     */
    public function reject(string $reason): bool
    {
        if (!$this->isPending()) {
            return false;
        }

        $this->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
        ]);

        return true;
    }
}
