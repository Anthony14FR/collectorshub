<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PromoCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'is_active',
        'is_global',
        'cash',
        'expires_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_global' => 'boolean',
        'cash' => 'integer',
        'expires_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($promoCode) {
            if (strlen($promoCode->code) > 20) {
                throw new \Exception('Le code ne peut pas dépasser 20 caractères');
            }
            if ($promoCode->cash < 0) {
                throw new \Exception('Le montant de la réduction ne peut pas être négatif');
            }
            if ($promoCode->expires_at && $promoCode->expires_at < now()) {
                throw new \Exception('La date d\'expiration ne peut pas être dans le passé');
            }

            $exists = static::where('code', $promoCode->code)
                ->where('id', '!=', $promoCode->id)
                ->exists();

            if ($exists) {
                throw new \Exception('Ce code promo existe déjà');
            }
        });
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'promo_code_items')
            ->withPivot(['quantity'])
            ->withTimestamps();
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'promo_code_users')
            ->withPivot(['is_used', 'used_at'])
            ->withTimestamps();
    }

    public function getRouteKeyName()
    {
        return 'id';
    }
}
