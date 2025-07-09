<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StatBoost extends Model
{
    protected $fillable = [
        'pokedex_id',
        'hp_boost',
        'attack_boost',
        'defense_boost',
        'speed_boost',
        'special_attack_boost',
        'special_defense_boost'
    ];

    public function pokedex(): BelongsTo
    {
        return $this->belongsTo(Pokedex::class);
    }
}
