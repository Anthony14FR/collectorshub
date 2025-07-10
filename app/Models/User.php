<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use HasRoles;

    protected $with = ['roles', 'permissions'];

    protected $table = 'users';

    public const ROLES = ['user', 'premium', 'admin'];
    public const STATUSES = ['active', 'suspended', 'banned'];

    protected $fillable = [
        'username',
        'email',
        'level',
        'experience',
        'cash',
        'last_login',
        'role',
        'status',
        'avatar',
        'unlocked_avatars',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $attributes = [
        'level' => 1,
        'experience' => 0,
        'cash' => 0,
        'role' => 'user',
        'status' => 'active',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login' => 'datetime',
        'password' => 'hashed',
        'cash' => 'integer',
        'level' => 'integer',
        'experience' => 'integer',
        'unlocked_avatars' => 'array',
    ];

    public function checkPassword(string $password): bool
    {
        return Hash::check($password, $this->password);
    }

    public function pokedex(): HasMany
    {
        return $this->hasMany(Pokedex::class);
    }

    public function inventory(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }

    public function marketplaceSales(): HasMany
    {
        return $this->hasMany(Marketplace::class, 'seller_id');
    }

    public function marketplacePurchases(): HasMany
    {
        return $this->hasMany(Marketplace::class, 'buyer_id');
    }

    public function promoCodes(): BelongsToMany
    {
        return $this->belongsToMany(PromoCode::class, 'promo_code_users')
            ->withPivot(['is_used', 'used_at'])
            ->withTimestamps();
    }
}
