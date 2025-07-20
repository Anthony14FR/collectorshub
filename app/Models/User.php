<?php

namespace App\Models;

use App\Services\TotpService;
use App\Services\XpService;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use HasRoles;

    protected $with = ['roles', 'permissions'];

    protected $table = 'users';

    public const ROLES = ['user', 'admin'];
    public const STATUSES = ['active', 'suspended', 'banned'];

    protected $fillable = [
        'username',
        'email',
        'password',
        'level',
        'experience',
        'cash',
        'last_login',
        'role',
        'status',
        'avatar',
        'unlocked_avatars',
        'google_id',
        'google_avatar',
        'discord_id',
        'discord_username',
        'discord_avatar',
        'provider',
        'provider_verified_at',
        'claimed_level_rewards',
        'background',
        'unlocked_backgrounds',
        'totp_secret',
        'totp_enabled',
        'infernal_tower_current_level',
        'infernal_tower_daily_defeats',
        'infernal_tower_last_reset',
        'daily_quest_bonus_claimed_date',
        'welcome_modal_dismissed',
        'last_expedition_reroll',
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
        'infernal_tower_current_level' => 1,
        'infernal_tower_daily_defeats' => 10,
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login' => 'datetime',
        'password' => 'hashed',
        'cash' => 'integer',
        'level' => 'integer',
        'experience' => 'integer',
        'unlocked_avatars' => 'array',
        'provider_verified_at' => 'datetime',
        'claimed_level_rewards' => 'array',
        'unlocked_backgrounds' => 'array',
        'infernal_tower_current_level' => 'integer',
        'infernal_tower_daily_defeats' => 'integer',
        'infernal_tower_last_reset' => 'datetime',
        'daily_quest_bonus_claimed_date' => 'date',
        'welcome_modal_dismissed' => 'boolean',
        'last_expedition_reroll' => 'datetime',
    ];

    protected $appends = ['experience_for_current_level', 'experience_for_next_level', 'experience_percentage'];

    public function getExperienceForCurrentLevelAttribute(): int
    {
        return app(XpService::class)->getTotalExperienceForLevel($this->level ?? 1);
    }

    public function getExperienceForNextLevelAttribute(): int
    {
        return app(XpService::class)->getTotalExperienceForLevel($this->level + 1);
    }

    public function getExperiencePercentageAttribute(): float
    {
        return app(XpService::class)->getExperiencePercentage($this);
    }

    public function addXp(int $xp): void
    {
        app(XpService::class)->addXp($this, $xp);
    }

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

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class)->orderBy('created_at', 'desc');
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
        return $this->userSuccesses()
            ->where('is_claimed', false)
            ->with('success');
    }

    public function claimedSuccesses()
    {
        return $this->userSuccesses()
            ->where('is_claimed', true)
            ->with('success');
    }

    public function hasSuccess($successKey): bool
    {
        return $this->successes()->where('key', $successKey)->exists();
    }

    public function getUnclaimedCount(): int
    {
        return $this->unclaimedSuccesses()->count();
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new \App\Notifications\VerifyEmailNotification());
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\ResetPasswordNotification($token));
    }

    public function hasVerifiedEmail(): bool
    {
        if ($this->provider) {
            return true;
        }

        return !is_null($this->email_verified_at);
    }

    public function userExpeditions(): HasMany
    {
        return $this->hasMany(UserExpedition::class);
    }

    public function expeditions(): BelongsToMany
    {
        return $this->belongsToMany(Expedition::class, 'user_expeditions')
            ->withPivot('date', 'status', 'started_at', 'ends_at', 'claimed_at')
            ->withTimestamps();
    }

    public function resetInfernalTowerDailyDefeats(): void
    {
        $now = now();

        if (is_null($this->infernal_tower_last_reset) || $this->infernal_tower_last_reset->isToday() === false) {
            $this->infernal_tower_daily_defeats = 10;
            $this->infernal_tower_last_reset = $now;
            $this->save();
        }
    }

    public function getRawDailyDefeats(): int
    {
        return $this->attributes['infernal_tower_daily_defeats'] ?? 0;
    }

    public function userFriends()
    {
        return $this->hasMany(UserFriend::class, 'user_id');
    }

    public function friends()
    {
        return $this->belongsToMany(User::class, 'user_friends', 'user_id', 'friend_id')
            ->wherePivot('status', 'accepted');
    }

    public function friendRequests()
    {
        return $this->hasMany(UserFriend::class, 'friend_id')->where('status', 'pending');
    }

    public function userFriendGiftsSent()
    {
        return $this->hasMany(UserFriendGift::class, 'sender_id');
    }

    public function userFriendGiftsReceived()
    {
        return $this->hasMany(UserFriendGift::class, 'receiver_id');
    }

    public function friendGiftsToClaim()
    {
        return $this->userFriendGiftsReceived()->where('is_claimed', false);
    }

    public function enableTotp(string $verificationCode): bool
    {
        $totpService = app(TotpService::class);

        if (!$this->totp_secret) {
            $this->totp_secret = $totpService->generateSecret();
        }

        if (!$totpService->verifyCode($this->totp_secret, $verificationCode)) {
            return false;
        }

        $this->totp_enabled = true;
        $this->save();

        return true;
    }

    public function disableTotp(): void
    {
        $this->totp_enabled = false;
        $this->totp_secret = null;
        $this->save();
    }

    public function hasTotpEnabled(): bool
    {
        return $this->totp_enabled && !is_null($this->totp_secret);
    }

    public function verifyTotpCode(string $code): bool
    {
        if (!$this->hasTotpEnabled()) {
            return false;
        }

        return app(TotpService::class)->verifyCode($this->totp_secret, $code);
    }

    public function getTotpQrUrl(): string
    {
        if (!$this->totp_secret) {
            $this->totp_secret = app(TotpService::class)->generateSecret();
            $this->save();
        }

        return app(TotpService::class)->getQrCodeUrl(
            $this->totp_secret,
            config('app.name'),
            $this->email
        );
    }

    public function club()
    {
        return $this->belongsToMany(Club::class, 'club_members')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function ledClub()
    {
        return $this->hasOne(Club::class, 'leader_id');
    }

    public function clubRequests()
    {
        return $this->hasMany(ClubRequest::class);
    }

    public function isInClub(): bool
    {
        return $this->club()->exists();
    }

    public function isClubLeader(): bool
    {
        return $this->ledClub()->exists();
    }

    public function dailyQuestProgress(): HasMany
    {
        return $this->hasMany(UserDailyQuestProgress::class);
    }

    public function todayQuestProgress(): HasMany
    {
        return $this->dailyQuestProgress()->where('date', today());
    }

    public function canClaimDailyBonus(): bool
    {
        return is_null($this->daily_quest_bonus_claimed_date) ||
               !$this->daily_quest_bonus_claimed_date->isToday();
    }
}
