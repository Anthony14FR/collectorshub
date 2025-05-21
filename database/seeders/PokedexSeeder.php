<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pokemon;
use App\Models\Pokedex;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class PokedexSeeder extends Seeder
{
    public function run()
    {
        $pokemon = Pokemon::find(1);
        $admin = User::where('username', 'admin')->first();

        if (!$pokemon) {
            throw new \Exception('Pokemon with ID 1 not found. Run PokemonSeeder first.');
        }

        Pokedex::create([
            'user_id' => $admin->id,
            'pokemon_id' => $pokemon->id,
            'pokedexId' => $pokemon->pokedexId,
            'name' => $pokemon->name,
            'types' => $pokemon->types,
            'resistances' => $pokemon->resistances,
            'evolutionId' => $pokemon->evolutionId,
            'preEvolutionId' => $pokemon->preEvolutionId,
            'description' => $pokemon->description,
            'height' => $pokemon->height,
            'weight' => $pokemon->weight,
            'rarity' => $pokemon->rarity,
            'HP' => $pokemon->HP,
            'attack' => $pokemon->attack,
            'defense' => $pokemon->defense,
            'speed' => $pokemon->speed,
            'special_attack' => $pokemon->special_attack,
            'special_defense' => $pokemon->special_defense,
            'generation' => $pokemon->generation,
            'isInTeam' => true,
            'level' => 1,
        ]);
    }
}
