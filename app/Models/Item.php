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
        'image'
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
            if (!in_array($item->type, ['ball', 'avatar', 'background'])) {
                throw new \Exception('Type d\'item invalide');
            }
            if (!in_array($item->rarity, ['normal', 'rare', 'epic', 'legendary'])) {
                throw new \Exception('Rareté invalide');
            }
            if ($item->price < 0) {
                throw new \Exception('Le prix ne peut pas être négatif');
            }

            switch ($item->type) {
                case 'avatar':
                    if (empty($item->image) || !is_string($item->image)) {
                        throw new \Exception('Un avatar doit avoir un chemin d\'image valide');
                    }
                    if ($item->price < 0) {
                        throw new \Exception('Le prix de l\'avatar ne peut pas être négatif');
                    }
                    if (!empty($item->effect) && $item->effect !== []) {
                        throw new \Exception('Un avatar ne doit pas avoir d\'effet');
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
