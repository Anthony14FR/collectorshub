<?php

namespace Database\Seeders;

use App\Models\Pokedex;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserTeamSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::orderBy('id')->limit(15)->get();

        if ($users->isEmpty()) {
            $this->command->error('Aucun utilisateur trouvé. Exécutez d\'abord UserSeeder.');
            return;
        }

        $totalPokemonsAdded = 0;

        foreach ($users as $user) {
            $availablePokemons = Pokedex::where('user_id', $user->id)
                ->where('is_in_team', false)
                ->inRandomOrder()
                ->limit(6)
                ->get();

            if ($availablePokemons->isEmpty()) {
                $this->command->info("Aucun Pokémon disponible pour l'équipe de l'utilisateur {$user->username}");
                continue;
            }

            $pokemonsAdded = 0;
            foreach ($availablePokemons as $pokemon) {
                $pokemon->update(['is_in_team' => true]);
                $pokemonsAdded++;
                $totalPokemonsAdded++;
            }
        }

        $this->command->info("Total : {$totalPokemonsAdded} Pokémon(s) ajouté(s) aux équipes des utilisateurs.");
    }
}
