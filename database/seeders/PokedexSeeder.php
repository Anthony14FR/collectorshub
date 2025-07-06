<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pokemon;
use App\Models\Pokedex;
use App\Models\User;

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
            'is_in_team' => true,
            'level' => 1,
        ]);
    }
}
