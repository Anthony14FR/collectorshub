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
            'pokedex_id' => $pokemon->pokedex_id,
            'name' => $pokemon->name,
            'types' => $pokemon->types,
            'resistances' => $pokemon->resistances,
            'evolution_id' => $pokemon->evolution_id,
            'pre_evolution_id' => $pokemon->pre_evolution_id,
            'description' => $pokemon->description,
            'height' => $pokemon->height,
            'weight' => $pokemon->weight,
            'rarity' => $pokemon->rarity,
            'hp' => $pokemon->hp,
            'attack' => $pokemon->attack,
            'defense' => $pokemon->defense,
            'speed' => $pokemon->speed,
            'special_attack' => $pokemon->special_attack,
            'special_defense' => $pokemon->special_defense,
            'generation' => $pokemon->generation,
            'is_in_team' => true,
            'level' => 1,
        ]);
    }
}
