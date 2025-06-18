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
        'description',
        'discount_percentage',
        'start_date',
        'end_date',
        'max_uses',
        'current_uses'
    ];

    protected $casts = [
        'discount_percentage' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'max_uses' => 'integer',
        'current_uses' => 'integer'
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($promoCode) {
            if (strlen($promoCode->code) > 20) {
                throw new \Exception('Le code ne peut pas dépasser 20 caractères');
            }
            if (strlen($promoCode->description) > 250) {
                throw new \Exception('La description ne peut pas dépasser 250 caractères');
            }
            if ($promoCode->discount_percentage < 0 || $promoCode->discount_percentage > 100) {
                throw new \Exception('Le pourcentage de réduction doit être entre 0 et 100');
            }
            if ($promoCode->max_uses < 0) {
                throw new \Exception('Le nombre maximum d\'utilisations ne peut pas être négatif');
            }
            if ($promoCode->current_uses < 0) {
                throw new \Exception('Le nombre d\'utilisations actuelles ne peut pas être négatif');
            }
            if ($promoCode->current_uses > $promoCode->max_uses) {
                throw new \Exception('Le nombre d\'utilisations actuelles ne peut pas dépasser le maximum');
            }
            if ($promoCode->start_date && $promoCode->end_date && $promoCode->start_date > $promoCode->end_date) {
                throw new \Exception('La date de début doit être antérieure à la date de fin');
            }

            $exists = static::where('code', $promoCode->code)
                ->where('id', '!=', $promoCode->id)
                ->exists();

            if ($exists) {
                throw new \Exception('Ce code promo existe déjà');
            }

            $now = now();
            if ($promoCode->start_date && $promoCode->start_date > $now) {
                throw new \Exception('La date de début ne peut pas être dans le futur');
            }
            if ($promoCode->end_date && $promoCode->end_date < $now) {
                throw new \Exception('La date de fin ne peut pas être dans le passé');
            }

            if ($promoCode->items()->exists()) {
                foreach ($promoCode->items as $item) {
                    if ($item->pivot->quantity < 1) {
                        throw new \Exception('La quantité d\'items dans la promo doit être supérieure à 0');
                    }
                    if ($item->pivot->quantity > 999) {
                        throw new \Exception('La quantité d\'items dans la promo ne peut pas dépasser 999');
                    }
                }
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
} 