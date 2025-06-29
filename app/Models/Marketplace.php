<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Marketplace extends Model
{
    protected $table = 'marketplace';

    protected $fillable = [
        'seller_id',
        'pokemon_id',
        'price',
        'status',
        'sold_at',
        'buyer_id'
    ];

    protected $casts = [
        'price' => 'integer',
        'sold_at' => 'datetime'
    ];

    protected $attributes = [
        'status' => 'active'
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($marketplace) {
            if ($marketplace->price < 0) {
                throw new \Exception('Le prix ne peut pas être négatif');
            }
            
            if (!in_array($marketplace->status, ['active', 'sold', 'cancelled'])) {
                throw new \Exception('Statut invalide');
            }

            if ($marketplace->buyer_id && $marketplace->seller_id === $marketplace->buyer_id) {
                throw new \Exception('Le vendeur ne peut pas être l\'acheteur');
            }

            if ($marketplace->status === 'sold') {
                if (!$marketplace->sold_at) {
                    $marketplace->sold_at = now();
                }
                if (!$marketplace->buyer_id) {
                    throw new \Exception('Un acheteur est requis pour une vente');
                }
            } else {
                if ($marketplace->sold_at) {
                    throw new \Exception('La date de vente ne peut être définie que pour une vente complétée');
                }
                if ($marketplace->buyer_id) {
                    throw new \Exception('Un acheteur ne peut être défini que pour une vente complétée');
                }
            }
        });
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function pokemon(): BelongsTo
    {
        return $this->belongsTo(Pokedex::class, 'pokemon_id');
    }
} 