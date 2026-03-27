<?php

namespace App\Models;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


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
        'block_layouts' => ['default', 'grid', 'compact', 'highlight', 'masonry', 'carousel'],
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
        'storage_limit'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function isPro(): bool
    {
        return in_array((string) $this->subscription_plan, ['pro', 'premium'], true);
    }

    public function appearanceAccess(): array
    {
        return $this->isPro()
            ? self::PRO_APPEARANCE_ACCESS
            : self::FREE_APPEARANCE_ACCESS;
    }

    /**
     * ===== STORAGE MANAGEMENT =====
     * Method untuk mengelola kapasitas penyimpanan user
     */

    /**
     * Dapatkan info penyimpanan user
     */
    public function getStorageInfo(): array
    {
        return \App\Services\StorageService::getStorageInfo($this);
    }

    /**
     * Validasi apakah user bisa upload file
     */
    public function canUpload(int $fileSize): array
    {
        return \App\Services\StorageService::validateUpload($this, $fileSize);
    }

    /**
     * Tambahkan storage usage setelah file diupload
     */
    public function addStorageUsage(int $fileSize): void
    {
        \App\Services\StorageService::addStorageUsage($this, $fileSize);
    }

    /**
     * Kurangi storage usage ketika file dihapus
     */
    public function removeStorageUsage(int $fileSize): void
    {
        \App\Services\StorageService::removeStorageUsage($this, $fileSize);
    }

    /**
     * Dapatkan sisa storage yang tersedia
     */
    public function getAvailableStorage(): int
    {
        return \App\Services\StorageService::getAvailableStorage($this);
    }

    /**
     * Dapatkan persentase penggunaan storage
     */
    public function getStoragePercentage(): float
    {
        return \App\Services\StorageService::getStoragePercentage($this);
    }

    /**
     * Set storage limit berdasarkan subscription plan
     */
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
        return $this->hasOne(UserProfile::class); // alias untuk controller appearance
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

    // Generate username dari email
    public static function generateUsernameFromEmail($email)
    {
        return strtok($email, '@'); // Ambil bagian sebelum @
    }


public function paymentAccounts()
{
    return $this->hasMany(PaymentAccount::class)->whereNull('deleted_at')->orderByDesc('is_default');
}

public function profileReports()
{
    return $this->hasMany(ProfileReport::class, 'reported_user_id');
}
}


