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
use App\Models\Success;
use App\Models\UserSuccess;

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

    public function successes()
    {
        return $this->belongsToMany(Success::class, 'user_successes')
                    ->withTimestamps()
                    ->withPivot('unlocked_at', 'is_claimed', 'claimed_at');
    }

    public function userSuccesses()
    {
        return $this->hasMany(UserSuccess::class);
    }

    public function unclaimedSuccesses()
    {
        return $this->userSuccesses()->unclaimed()->with('success');
    }

    public function claimedSuccesses()
    {
        return $this->userSuccesses()->claimed()->with('success');
    }

    public function hasSuccess($successKey): bool
    {
        return $this->successes()->where('key', $successKey)->exists();
    }

    public function claimSuccess($successId): bool
    {
        $userSuccess = $this->userSuccesses()
                            ->where('success_id', $successId)
                            ->where('is_claimed', false)
                            ->first();

        if ($userSuccess) {
            $userSuccess->claim();
            return true;
        }

        return false;
    }

    public function claimAllSuccesses(): int
    {
        $unclaimedCount = $this->userSuccesses()->unclaimed()->count();
        
        $this->userSuccesses()->unclaimed()->update([
            'is_claimed' => true,
            'claimed_at' => now()
        ]);

        return $unclaimedCount;
    }

    public function getSuccessProgress()
    {
        $totalSuccesses = Success::count();
        $unlockedSuccesses = $this->successes()->count();
        $claimedSuccesses = $this->userSuccesses()->claimed()->count();
        $unclaimedSuccesses = $this->userSuccesses()->unclaimed()->count();
        
        return [
            'total' => $totalSuccesses,
            'unlocked' => $unlockedSuccesses,
            'claimed' => $claimedSuccesses,
            'unclaimed' => $unclaimedSuccesses,
            'percentage' => $totalSuccesses > 0 ? round(($unlockedSuccesses / $totalSuccesses) * 100, 2) : 0
        ];
    }

    public function getUnclaimedCount(): int
    {
        return $this->userSuccesses()->unclaimed()->count();
    }
}
