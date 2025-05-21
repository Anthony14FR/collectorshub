<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pokedex extends Model
{
    protected $guarded = ['id'];
    protected $casts = [
        'types' => 'array',
        'resistances' => 'array',
        'isShiny' => 'boolean',
        'isInTeam' => 'boolean',
        'isFavorite' => 'boolean',
        'obtainedAt' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pokemon()
    {
        return $this->belongsTo(Pokemon::class);
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
