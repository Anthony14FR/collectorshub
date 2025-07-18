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
        $pokemon = Pokemon::find(1);
        $admin = User::where('username', 'admin')->first();

        if (!$pokemon) {
            throw new \Exception('No Pokemon found. Run PokemonSeeder first.');
        }

        if (!$admin) {
            throw new \Exception('Admin user not found. Run UserSeeder first.');
        }

        if (!Pokedex::where('user_id', $admin->id)->where('pokemon_id', $pokemon->id)->exists()) {
            Pokedex::create([
                'user_id' => $admin->id,
                'pokemon_id' => $pokemon->id,
                'is_in_team' => false,
                'level' => 1,
            ]);
        }

        $this->seedUpgradePokemon($admin);
    }

    private function seedUpgradePokemon(User $admin)
    {
        $pikachu = Pokemon::where('name', 'Pikachu')->first();

        if (!$pikachu) {
            $pikachu = Pokemon::first();
        }

        $existingPikachuCount = Pokedex::where('user_id', $admin->id)
            ->where('pokemon_id', $pikachu->id)
            ->where('star', 0)
            ->count();

        $pikachuToCreate = 170 - $existingPikachuCount;

        for ($i = 0; $i < $pikachuToCreate; $i++) {
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
            $existingStar1PokemonCount = Pokedex::where('user_id', $admin->id)
                ->where('pokemon_id', $pokemon->id)
                ->where('star', 1)
                ->count();
            $star1PokemonToCreate = 2 - $existingStar1PokemonCount;

            for ($i = 0; $i < $star1PokemonToCreate; $i++) {
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

            $existingStar3PokemonCount = Pokedex::where('user_id', $admin->id)
                ->where('pokemon_id', $pokemon->id)
                ->where('star', 3)
                ->count();
            $star3PokemonToCreate = 6 - $existingStar3PokemonCount;

            for ($i = 0; $i < $star3PokemonToCreate; $i++) {
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
