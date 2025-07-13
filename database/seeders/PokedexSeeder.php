<?php

namespace Database\Seeders;

use App\Models\Pokedex;
use App\Models\Pokemon;
use App\Models\User;
use Illuminate\Database\Seeder;

class PokedexSeeder extends Seeder
{
    public function run()
    {
        $pokemon = Pokemon::first();
        $admin = User::where('username', 'admin')->first();

        if (!$pokemon) {
            throw new \Exception('No Pokemon found. Run PokemonSeeder first.');
        }

        if (!$admin) {
            throw new \Exception('Admin user not found. Run UserSeeder first.');
        }

        Pokedex::create([
            'user_id' => $admin->id,
            'pokemon_id' => $pokemon->id,
            'is_in_team' => false,
            'level' => 1,
        ]);

        $this->seedUpgradePokemon($admin);
    }

    private function seedUpgradePokemon(User $admin)
    {
        $pikachu = Pokemon::where('name', 'Pikachu')->first();

        if (!$pikachu) {
            $pikachu = Pokemon::first();
        }

        if (!$pikachu) {
            return;
        }

        for ($i = 0; $i < 170; $i++) {
            Pokedex::create([
                'user_id' => $admin->id,
                'pokemon_id' => $pikachu->id,
                'nickname' => null,
                'level' => 1,
                'star' => 0,
                'hp_left' => $pikachu->hp,
                'is_in_team' => false,
                'is_favorite' => false,
                'obtained_at' => now()
            ]);
        }

        $randomPokemons = Pokemon::where('id', '!=', $pikachu->id)->take(5)->get();
        
        foreach ($randomPokemons as $pokemon) {
            for ($i = 0; $i < 2; $i++) {
                Pokedex::create([
                    'user_id' => $admin->id,
                    'pokemon_id' => $pokemon->id,
                    'nickname' => null,
                    'level' => 1,
                    'star' => 1,
                    'hp_left' => $pokemon->hp,
                    'is_in_team' => false,
                    'is_favorite' => false,
                    'obtained_at' => now()
                ]);
            }

            for ($i = 0; $i < 6; $i++) {
                Pokedex::create([
                    'user_id' => $admin->id,
                    'pokemon_id' => $pokemon->id,
                    'nickname' => null,
                    'level' => 1,
                    'star' => 3,
                    'hp_left' => $pokemon->hp,
                    'is_in_team' => false,
                    'is_favorite' => false,
                    'obtained_at' => now()
                ]);
            }
        }
    }
}
