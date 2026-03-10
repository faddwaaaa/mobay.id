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
        'origin_village_code',
        'origin_city_id',
        'origin_city_name'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

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
}


