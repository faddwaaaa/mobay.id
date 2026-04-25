<?php

namespace App\Models;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;   

// RELATION MODELS
use App\Models\UserProfile;
use App\Models\Link;
use App\Models\SocialLink;
use App\Models\Transaction;
use App\Models\Withdrawal;
use App\Models\Page;
use App\Models\PaymentAccount;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const FREE_APPEARANCE_ACCESS = [
        'background_types' => ['color', 'gradient'],
        'button_styles' => ['fill', 'outline', 'soft_shadow', 'hard_shadow'],
        'fonts' => ['Plus Jakarta Sans', 'Inter', 'Poppins', 'Lato'],
        'block_layouts' => ['default', 'compact', 'grid'],
        'max_social_links' => 5,
        'analytics_basic' => true,
    ];

    public const PRO_APPEARANCE_ACCESS = [
        'background_types' => ['color', 'gradient', 'image'],
        'button_styles' => ['fill', 'outline', 'hard_shadow', 'soft_shadow', 'ghost', 'minimal', 'neon', 'glass'],
        'fonts' => ['Plus Jakarta Sans', 'Inter', 'Poppins', 'Lato', 'Merriweather', 'Space Grotesk', 'Nunito', 'DM Sans', 'Playfair Display', 'Roboto Mono', 'Dancing Script'],
        'block_layouts' => ['default', 'grid', 'compact', 'highlight'],
        'max_social_links' => 15,
        'analytics_advanced' => true,
        'custom_css' => true,
        'animation_effects' => true,
        'priority_support' => true,
    ];

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'google_id',
        'avatar',
        'email_verified_at',
        'balance',
        'role',          
        'is_suspended',
        'subscription_plan',
        'origin_village_code',
        'origin_city_id',
        'origin_city_name',
        'storage_used',
        'storage_limit',
        'pro_until',
        'pro_type',
        'xendit_invoice_id',
        'xendit_external_id'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'pro_until' => 'datetime',
        'password' => 'hashed',
    ];

    public function isPro(): bool
    {
        if ($this->isProActive()) {
            return true;
        }

        return $this->hasProPlan() && $this->pro_until === null;
    }

    public function isProActive(): bool
    {
        return $this->pro_until && $this->pro_until > now();
    }

    public function hasProPlan(): bool
    {
        return in_array((string) $this->subscription_plan, ['pro', 'premium'], true);
    }

    public function hasExpiredProAccess(): bool
    {
        return $this->hasProPlan()
            && $this->pro_until !== null
            && !$this->isProActive();
    }

    public function getProRemainingDays(): ?int
    {
        if (!$this->isProActive()) {
            return null;
        }

        return now()->diffInDays($this->pro_until, false);
    }

    public function shouldShowProExpiryReminder(int $days = 5): bool
    {
        $remainingDays = $this->getProRemainingDays();

        return $remainingDays !== null && $remainingDays <= $days;
    }

    public function appearanceAccess(): array
    {
        return $this->isPro()
            ? self::PRO_APPEARANCE_ACCESS
            : self::FREE_APPEARANCE_ACCESS;
    }

    /**
     * ===== STORAGE MANAGEMENT =====
     */

    public function getStorageInfo(): array
    {
        return \App\Services\StorageService::getStorageInfo($this);
    }

    public function canUpload(int $fileSize): array
    {
        return \App\Services\StorageService::validateUpload($this, $fileSize);
    }

    public function addStorageUsage(int $fileSize): void
    {
        \App\Services\StorageService::addStorageUsage($this, $fileSize);
    }

    public function removeStorageUsage(int $fileSize): void
    {
        \App\Services\StorageService::removeStorageUsage($this, $fileSize);
    }

    public function getAvailableStorage(): int
    {
        return \App\Services\StorageService::getAvailableStorage($this);
    }

    public function getStoragePercentage(): float
    {
        return \App\Services\StorageService::getStoragePercentage($this);
    }

    public function updateStorageLimit(): void
    {
        \App\Services\StorageService::updateStorageLimit($this);
    }

    // ===== RELATIONS =====

    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    public function userProfile()
    {
        return $this->hasOne(UserProfile::class);
    }

    public function links()
    {
        return $this->hasMany(Link::class);
    }

    public function socialLinks()
    {
        return $this->hasMany(SocialLink::class);
    }

    public function pages()
    {
        return $this->hasMany(Page::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class);
    }

    public function approvedWithdrawals()
    {
        return $this->hasMany(Withdrawal::class, 'approved_by');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function clicks()
    {
        return $this->hasMany(Click::class);
    }

    public static function generateUsernameFromEmail($email)
    {
        return strtok($email, '@');
    }

    public function paymentAccounts()
    {
        return $this->hasMany(PaymentAccount::class)->whereNull('deleted_at')->orderByDesc('is_default');
    }

    public function profileReports()
    {
        return $this->hasMany(ProfileReport::class, 'reported_user_id');
    }

    public function getAvatarUrlAttribute(): string
    {
        if (!$this->avatar) {
            return asset('images/default-avatar.png');
        }

        return Str::startsWith($this->avatar, ['http://', 'https://'])
            ? $this->avatar
            : Storage::url($this->avatar);
    }
}
