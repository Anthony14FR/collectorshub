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
        'nickname',
        'level',
        'star',
        'hp_left',
        'is_in_team',
        'is_favorite',
        'obtained_at'
    ];

    protected $casts = [
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
            $pokemon = Pokemon::find($pokedex->pokemon_id);
            if ($pokemon) {
                $pokedex->hp_left = $pokemon->hp;
            }
        });
    }
}
