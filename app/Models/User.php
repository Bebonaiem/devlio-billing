<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'first_name',
        'last_name',
        'name',
        'email',
        'password',
        'provider',
        'provider_id',
        'pterodactyl_user_id',
        'pterodactyl_api_key',
        'tfa_secret',
        'role_id',
        'email_verified',
        'affiliate_code',
        'referred_by',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'pterodactyl_api_key',
        'tfa_secret',
        'provider_id',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'email_verified' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (User $user) {
            if (empty($user->affiliate_code)) {
                $user->affiliate_code = Str::random(10);
            }
        });
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function credits(): HasMany
    {
        return $this->hasMany(Credit::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function notificationPreferences(): HasMany
    {
        return $this->hasMany(NotificationPreference::class);
    }

    public function billingAgreements(): HasMany
    {
        return $this->hasMany(BillingAgreement::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    public function apiKeys(): HasMany
    {
        return $this->hasMany(ApiKey::class);
    }

    public function referredBy()
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(User::class, 'referred_by');
    }

    public function affiliateCommissions(): HasMany
    {
        return $this->hasMany(AffiliateCommission::class, 'affiliate_id');
    }

    public function referredAffiliateCommissions(): HasMany
    {
        return $this->hasMany(AffiliateCommission::class, 'referred_user_id');
    }

    public function paymentMethods()
    {
        return $this->hasMany(PaymentMethod::class);
    }

    public function servers(): HasMany
    {
        return $this->hasMany(Server::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function serversThroughServices(): HasManyThrough
    {
        return $this->hasManyThrough(Server::class, Service::class);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }
}
