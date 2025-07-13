<?php

namespace App\Models;

use App\Notifications\ResetPassword;
use App\Notifications\VerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'cash',
        'level',
        'exp',
        'starter_pokemon',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPassword($token));
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmail);
    }

    /**
     * Relationships
     */
    public function pokedex()
    {
        return $this->hasMany(Pokedex::class);
    }

    public function marketplaceListings()
    {
        return $this->hasMany(MarketplaceListing::class, 'seller_id');
    }

    public function inventory()
    {
        return $this->hasMany(Inventory::class);
    }

    public function promoCodes()
    {
        return $this->belongsToMany(PromoCode::class, 'promo_code_user')
            ->withTimestamps();
    }

    public function successesClaimed()
    {
        return $this->belongsToMany(Success::class, 'user_success')
            ->withTimestamps();
    }
}