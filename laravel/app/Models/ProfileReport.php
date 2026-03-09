<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProfileReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_code',
        'reported_user_id',
        'reason',
        'detail',
        'evidence_paths',
        'evidence_count',
        'reporter_ip',
        'user_agent',
        'page_url',
        'status',              // pending | reviewed | rejected
        'reviewed_by',
        'reviewed_at',
        'moderator_note',
        'triggered_freeze',
    ];

    protected $casts = [
        'reviewed_at'     => 'datetime',
        'evidence_paths'  => 'array',
        'triggered_freeze'=> 'boolean',
    ];

    // Bobot per kategori untuk risk scoring
    public const RISK_WEIGHTS = [
        'violence'      => 5,
        'scam'          => 4,
        'hate_speech'   => 4,
        'fake_account'  => 3,
        'adult_content' => 3,
        'copyright'     => 2,
        'spam'          => 1,
        'other'         => 1,
    ];

    // Ambang laporan untuk auto-flag (BUKAN auto-suspend)
    public const FREEZE_THRESHOLD     = 10; // laporan unik dalam 1 jam
    public const FREEZE_WINDOW_HOURS  = 1;

    protected static function booted(): void
    {
        static::creating(function ($model) {
            $model->ticket_code ??= 'RPT-' . strtoupper(Str::random(8));
        });
    }

    // ── Relations ──
    public function reportedUser()
    {
        return $this->belongsTo(User::class, 'reported_user_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // ── Helpers ──
    public function getRiskScoreAttribute(): int
    {
        $weight    = self::RISK_WEIGHTS[$this->reason] ?? 1;
        $totalRep  = $this->reportedUser?->profile_reports_count ?? 1;
        return $weight * min($totalRep, 10);
    }

    public function getRiskLevelAttribute(): string
    {
        $s = $this->risk_score;
        if ($s >= 30) return 'KRITIS';
        if ($s >= 15) return 'TINGGI';
        if ($s >= 5)  return 'SEDANG';
        return 'RENDAH';
    }
}