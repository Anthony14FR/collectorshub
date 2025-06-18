<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PokemonSeeder extends Seeder
{
    public function run()
    {
        $jsonPath = storage_path('app/private/data/pokemon_test.json');
        $jsonData = json_decode(file_get_contents($jsonPath), true);

        foreach ($jsonData as $pokemon) {
            DB::table('pokemon')->insert([
                'id' => $pokemon['id'],
                'pokedex_id' => $pokemon['pokedexId'],
                'name' => $pokemon['name'],
                'types' => json_encode($pokemon['apiTypes']),
                'resistances' => json_encode($pokemon['apiResistances']),
                'evolution_id' => $pokemon['apiEvolutions'][0]['pokedexId'] ?? null,
                'pre_evolution_id' => $pokemon['apiPreEvolution']['pokedexId'] ?? null,
                'description' => $pokemon['description'],
                'height' => $pokemon['height'],
                'weight' => $pokemon['weight'],
                'rarity' => $pokemon['rarity'],
                'is_shiny' => $pokemon['isShiny'],
                'hp' => $pokemon['stats']['HP'],
                'attack' => $pokemon['stats']['attack'],
                'defense' => $pokemon['stats']['defense'],
                'speed' => $pokemon['stats']['speed'],
                'special_attack' => $pokemon['stats']['special_attack'],
                'special_defense' => $pokemon['stats']['special_defense'],
                'generation' => $pokemon['apiGeneration'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
