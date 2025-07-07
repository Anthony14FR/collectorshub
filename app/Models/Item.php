<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    protected $fillable = [
        'name',
        'description',
        'type',
        'effect',
        'price',
        'rarity',
        'image_url'
    ];

    protected $casts = [
        'effect' => 'array',
        'price' => 'integer'
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($item) {
            if (strlen($item->name) > 50) {
                throw new \Exception('Le nom ne peut pas dépasser 50 caractères');
            }
            if ($item->description && strlen($item->description) > 250) {
                throw new \Exception('La description ne peut pas dépasser 250 caractères');
            }
            if (!in_array($item->type, ['heal', 'boost', 'evolution', 'special'])) {
                throw new \Exception('Type d\'item invalide');
            }
            if (!in_array($item->rarity, ['normal', 'rare', 'epic', 'legendary'])) {
                throw new \Exception('Rareté invalide');
            }
            if ($item->price < 0) {
                throw new \Exception('Le prix ne peut pas être négatif');
            }

            if (!is_array($item->effect)) {
                throw new \Exception('Les effets doivent être un tableau');
            }

            switch ($item->type) {
                case 'heal':
                    if (!isset($item->effect['amount']) || !is_numeric($item->effect['amount'])) {
                        throw new \Exception('L\'effet de soin doit avoir un montant numérique');
                    }
                    if ($item->effect['amount'] < 0) {
                        throw new \Exception('Le montant de soin ne peut pas être négatif');
                    }
                    break;

                case 'boost':
                    if (!isset($item->effect['stat']) || !isset($item->effect['amount'])) {
                        throw new \Exception('L\'effet de boost doit avoir une statistique et un montant');
                    }
                    if (!in_array($item->effect['stat'], ['hp', 'attack', 'defense', 'speed', 'special_attack', 'special_defense'])) {
                        throw new \Exception('Statistique de boost invalide');
                    }
                    if (!is_numeric($item->effect['amount']) || $item->effect['amount'] < 1 || $item->effect['amount'] > 32) {
                        throw new \Exception('Le montant de boost doit être entre 1 et 32');
                    }
                    break;

                case 'evolution':
                    if (!isset($item->effect['pokemon_id']) || !is_numeric($item->effect['pokemon_id'])) {
                        throw new \Exception('L\'effet d\'évolution doit avoir un ID de Pokémon valide');
                    }
                    break;

                case 'special':
                    if (!isset($item->effect['type']) || !in_array($item->effect['type'], ['shiny', 'rare', 'legendary'])) {
                        throw new \Exception('Type d\'effet spécial invalide');
                    }
                    break;
            }
        });
    }

    public function inventory(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }

    public function promoCodes(): BelongsToMany
    {
        return $this->belongsToMany(PromoCode::class, 'promo_code_items')
            ->withPivot(['quantity'])
            ->withTimestamps();
    }
} 