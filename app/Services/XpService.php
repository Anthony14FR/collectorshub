<?php

namespace App\Services;

use App\Models\User;
use App\Models\Pokedex;
use App\Models\Pokemon;

class XpService
{
    public function addXpForPokedexEntry(User $user, Pokedex $pokedex)
    {
        $pokemon = $pokedex->pokemon;
        if (!$pokemon) {
            $pokemon = Pokemon::find($pokedex->pokemon_id);
            if (!$pokemon) return;
        }

        $isShiny = isset($pokemon->is_shiny) && $pokemon->is_shiny;
        $rarity = $pokemon->rarity;
        $xp = 0;
        if (!$isShiny) {
            $xp = match ($rarity) {
                'legendary' => 35,
                'epic' => 10,
                'rare' => 5,
                default => 1,
            };
        } else {
            $xp = match ($rarity) {
                'legendary' => 70,
                'epic' => 20,
                'rare' => 10,
                default => 5,
            };
        }

        $alreadyOwned = $user->pokedex()->where('pokemon_id', $pokemon->id)->count() > 1;
        if (!$alreadyOwned) {
            $bonus = match ($rarity) {
                'legendary' => 15,
                'epic' => 10,
                'rare' => 5,
                default => 1,
            };
            $xp += $bonus;
        }

        $user->experience += $xp;
        $user->save();
    }
} 