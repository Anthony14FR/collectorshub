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
        $admin = User::where('username', 'admin')->first();

        if (!$admin) {
            throw new \Exception('Admin user not found');
        }

        if (Pokedex::where('user_id', $admin->id)->exists()) {
            $this->command->info('Le Pokédex de l\'admin existe déjà. Skipping...');
            return;
        }

        $this->seedAdminPokedex($admin);
        $this->seedOtherUsersPokedex();
    }

    private function seedAdminPokedex(User $admin)
    {
        $this->seedAdminPikachu($admin);
        $this->seedAdminBulbizarre($admin);
        $this->seedAdminPokemonByType($admin);
    }

    private function seedAdminPikachu(User $admin)
    {
        $pikachu = Pokemon::where('name', 'Pikachu')->first();

        if (!$pikachu) {
            throw new \Exception('Pikachu introuvable dans la base de données.');
        }

        $pikachuStars = [
            0 => 4,
            1 => 4,
            2 => 4,
            3 => 4,
            4 => 6,
            5 => 6,
        ];

        foreach ($pikachuStars as $star => $count) {
            $existingCount = Pokedex::where('user_id', $admin->id)
                ->where('pokemon_id', $pikachu->id)
                ->where('star', $star)
                ->count();

            $toCreate = $count - $existingCount;

            for ($i = 0; $i < $toCreate; $i++) {
                Pokedex::create([
                    'user_id' => $admin->id,
                    'pokemon_id' => $pikachu->id,
                    'nickname' => null,
                    'level' => 1,
                    'star' => $star,
                    'hp_left' => $pikachu->hp,
                    'is_in_team' => false,
                    'is_favorite' => false,
                    'obtained_at' => now()
                ]);
            }
        }
    }

    private function seedAdminBulbizarre(User $admin)
    {
        $bulbasaur = Pokemon::where('name', 'Bulbizarre')->first();

        if (!$bulbasaur) {
            throw new \Exception('Bulbizarre introuvable dans la base de données.');
        }

        $bulbasaurStars = [
            0 => 4,
            1 => 4,
            2 => 4,
            3 => 4,
            4 => 6,
            5 => 6,
        ];

        foreach ($bulbasaurStars as $star => $count) {
            $existingCount = Pokedex::where('user_id', $admin->id)
                ->where('pokemon_id', $bulbasaur->id)
                ->where('star', $star)
                ->count();

            $toCreate = $count - $existingCount;

            for ($i = 0; $i < $toCreate; $i++) {
                Pokedex::create([
                    'user_id' => $admin->id,
                    'pokemon_id' => $bulbasaur->id,
                    'nickname' => null,
                    'level' => 1,
                    'star' => $star,
                    'hp_left' => $bulbasaur->hp,
                    'is_in_team' => false,
                    'is_favorite' => false,
                    'obtained_at' => now()
                ]);
            }
        }
    }

    private function seedAdminPokemonByType(User $admin)
    {
        $types = ['Normal', 'Feu', 'Eau', 'Électrique', 'Plante', 'Glace', 'Combat', 'Poison', 'Sol', 'Vol', 'Psy', 'Insecte', 'Roche', 'Spectre', 'Dragon', 'Ténèbres', 'Acier', 'Fée'];

        foreach ($types as $type) {
            $pokemonsOfType = Pokemon::where('type1', $type)
                ->orWhere('type2', $type)
                ->take(2)
                ->get();

            foreach ($pokemonsOfType as $pokemon) {
                $existingCount = Pokedex::where('user_id', $admin->id)
                    ->where('pokemon_id', $pokemon->id)
                    ->where('star', 0)
                    ->count();

                if ($existingCount < 2) {
                    $toCreate = 2 - $existingCount;

                    for ($i = 0; $i < $toCreate; $i++) {
                        Pokedex::create([
                            'user_id' => $admin->id,
                            'pokemon_id' => $pokemon->id,
                            'nickname' => null,
                            'level' => 1,
                            'star' => 0,
                            'hp_left' => $pokemon->hp,
                            'is_in_team' => false,
                            'is_favorite' => false,
                            'obtained_at' => now()
                        ]);
                    }
                }
            }
        }
    }

    private function seedOtherUsersPokedex()
    {
        $otherUsers = User::where('username', '!=', 'admin')->get();

        foreach ($otherUsers as $user) {
            if (!Pokedex::where('user_id', $user->id)->exists()) {
                $this->seedRandomPokemonForUser($user);
            }
        }
    }

    private function seedRandomPokemonForUser(User $user)
    {
        $pokemonCount = rand(10, 30);
        $randomPokemons = Pokemon::inRandomOrder()->take($pokemonCount)->get();

        foreach ($randomPokemons as $pokemon) {
            $existingCount = Pokedex::where('user_id', $user->id)
                ->where('pokemon_id', $pokemon->id)
                ->where('star', 0)
                ->count();

            if ($existingCount === 0) {
                Pokedex::create([
                    'user_id' => $user->id,
                    'pokemon_id' => $pokemon->id,
                    'nickname' => null,
                    'level' => 1,
                    'star' => 0,
                    'hp_left' => $pokemon->hp,
                    'is_in_team' => false,
                    'is_favorite' => false,
                    'obtained_at' => now()
                ]);
            }
        }
    }
}
