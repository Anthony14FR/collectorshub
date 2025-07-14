<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pokedex extends Model
{
    protected $table = 'pokedex';

    protected $fillable = [
        'user_id',
        'pokemon_id',
        'nickname',
        'level',
        'star',
        'hp_left',
        'is_in_team',
        'is_favorite',
        'obtained_at',
        'cp'
    ];

    protected $casts = [
        'is_in_team' => 'boolean',
        'is_favorite' => 'boolean',
        'obtained_at' => 'datetime'
    ];


    public function calculateCP(): int
    {
        $pokemon = $this->pokemon;

        if (!$pokemon) {
            return 0;
        }

        $baseCP = $pokemon->hp + $pokemon->attack + $pokemon->defense +
                  $pokemon->special_attack + $pokemon->special_defense + $pokemon->speed;

        $finalCP = $baseCP;

        if ($pokemon->is_shiny) {
            $finalCP = floor($finalCP * 1.1);
        }

        $stars = $this->star ?? 1;
        $starMultiplier = $this->getStarMultiplier($stars);
        $finalCP = floor($finalCP * $starMultiplier);

        $rarityMultiplier = $this->getRarityMultiplier($pokemon->rarity ?? 'normal');
        $finalCP = floor($finalCP * $rarityMultiplier);

        return $finalCP * 10;
    }

    private function getStarMultiplier(int $stars): float
    {
        return match ($stars) {
            0 => 1.0,
            1 => 1.50,
            2 => 2.00,
            3 => 2.50,
            4 => 3.00,
            5 => 3.50,
            6 => 5.00,
            default => 1.0,
        };
    }

    private function getRarityMultiplier(string $rarity): float
    {
        return match (strtolower($rarity)) {
            'normal' => 1.10,
            'rare' => 1.50,
            'epic' => 2.25,
            'legendary' => 4.0,
            default => 1.10,
        };
    }

    protected static function booted()
    {
        static::saving(function ($pokedex) {
            $pokedex->cp = $pokedex->calculateCP();
        });
    }

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

    public function expeditionPokemons(): HasMany
    {
        return $this->hasMany(ExpeditionPokemon::class);
    }

    public function isInExpedition(): bool
    {
        return $this->expeditionPokemons()
            ->where('claimed_at', null)
            ->where('ends_at', '>', now())
            ->exists();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($pokedex) {
            $pokemon = Pokemon::find($pokedex->pokemon_id);
            if ($pokemon) {
                $pokedex->hp_left = $pokemon->hp;
            }
        });
    }
}
