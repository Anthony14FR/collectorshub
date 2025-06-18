<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pokedex extends Model
{
    protected $table = 'pokedex';

    protected $fillable = [
        'user_id',
        'pokemon_id',
        'pokedex_id',
        'name',
        'nickname',
        'types',
        'resistances',
        'evolution_id',
        'pre_evolution_id',
        'description',
        'height',
        'weight',
        'rarity',
        'level',
        'star',
        'is_shiny',
        'hp',
        'hp_left',
        'attack',
        'defense',
        'speed',
        'special_attack',
        'special_defense',
        'generation',
        'is_in_team',
        'is_favorite',
        'obtained_at'
    ];

    protected $casts = [
        'types' => 'array',
        'resistances' => 'array',
        'is_shiny' => 'boolean',
        'is_in_team' => 'boolean',
        'is_favorite' => 'boolean',
        'obtained_at' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pokemon(): BelongsTo
    {
        return $this->belongsTo(Pokemon::class);
    }

    public function statBoost(): HasOne
    {
        return $this->hasOne(StatBoost::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($pokedex) {
            if (isset($pokedex->HP)) {
                $pokedex->hpLeft = $pokedex->HP;
            }
        });
    }
}
