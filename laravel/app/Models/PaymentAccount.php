<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentAccount extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'bank_code',
        'bank_name',
        'account_number_encrypted',
        'account_number_last4',
        'account_holder_encrypted',
        'label',
        'is_default',
        'is_verified',
    ];

    /**
     * Encrypted casts — Laravel uses AES-256-CBC automatically.
     * Requires APP_KEY to be set in .env
     */
    protected $casts = [
        'account_number_encrypted' => 'encrypted',
        'account_holder_encrypted'  => 'encrypted',
        'is_default'                => 'boolean',
        'is_verified'               => 'boolean',
    ];

    /**
     * Hide sensitive fields from serialization (API responses, logs, etc.)
     */
    protected $hidden = [
        'account_number_encrypted',
        'account_holder_encrypted',
    ];

    // -----------------------------------------------------------------------
    // Relationships
    // -----------------------------------------------------------------------

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // -----------------------------------------------------------------------
    // Accessors (safe display values only)
    // -----------------------------------------------------------------------

    /**
     * Masked account number for display: "•••• •••• 1234"
     */
    public function getMaskedNumberAttribute(): string
    {
        return '•••• •••• ' . $this->account_number_last4;
    }

    /**
     * Decrypt and return account holder name.
     * Only call when strictly necessary (e.g. withdrawal processing).
     */
    public function getAccountHolderAttribute(): string
    {
        return $this->account_holder_encrypted ?? '';
    }

    /**
     * Decrypt and return full account number.
     * Only call when strictly necessary (e.g. sending to payment gateway).
     */
    public function getAccountNumberAttribute(): string
    {
        return $this->account_number_encrypted ?? '';
    }

    // -----------------------------------------------------------------------
    // Scopes
    // -----------------------------------------------------------------------

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    // -----------------------------------------------------------------------
    // Business logic helpers
    // -----------------------------------------------------------------------

    /**
     * Safely build a display-only array (no sensitive data).
     */
    public function toSafeArray(): array
    {
        return [
            'id'            => $this->id,
            'bank_code'     => $this->bank_code,
            'bank_name'     => $this->bank_name,
            'masked_number' => $this->masked_number,
            'holder_name'   => $this->account_holder,  // decrypted for display
            'label'         => $this->label,
            'is_default'    => $this->is_default,
            'is_verified'   => $this->is_verified,
            'created_at'    => $this->created_at?->toDateString(),
        ];
    }
}