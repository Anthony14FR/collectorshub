<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExpeditionPokemon extends Model
{
    protected $table = 'expedition_pokemons';

    protected $fillable = [
        'expedition_id',
        'pokedex_id',
        'started_at',
        'ends_at',
        'claimed_at'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ends_at' => 'datetime',
        'claimed_at' => 'datetime'
    ];

    public function expedition(): BelongsTo
    {
        return $this->belongsTo(Expedition::class);
    }

    public function pokedex(): BelongsTo
    {
        return $this->belongsTo(Pokedex::class);
    }

    public function isActive(): bool
    {
        return $this->claimed_at === null && $this->ends_at > now();
    }

    public function isCompleted(): bool
    {
        return $this->ends_at <= now() && $this->claimed_at === null;
    }

    public function isClaimed(): bool
    {
        return $this->claimed_at !== null;
    }
}
