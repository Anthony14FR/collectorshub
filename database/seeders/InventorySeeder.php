<?php

namespace Database\Seeders;

use App\Models\Inventory;
use App\Models\Item;
use App\Models\Pokedex;
use App\Models\Pokemon;
use App\Models\User;
use Illuminate\Database\Seeder;

class InventorySeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        $user = User::where('role', 'user')->first();
        
        if (!$admin || !$user) {
            $this->command->error('Utilisateurs admin ou user non trouvés. Exécutez d\'abord UserSeeder.');
            return;
        }
        
        $pokemons = Pokemon::inRandomOrder()->limit(20)->get();
        $items = Item::inRandomOrder()->limit(10)->get();
        
        if ($pokemons->isEmpty() || $items->isEmpty()) {
            $this->command->error('Aucun Pokémon ou item trouvé. Exécutez d\'abord PokemonSeeder et ItemSeeder.');
            return;
        }
        
        $this->command->info('Ajout de Pokémons à l\'admin...');
        foreach ($pokemons->take(15) as $index => $pokemon) {
            Pokedex::create([
                'user_id' => $admin->id,
                'pokemon_id' => $pokemon->id,
                'nickname' => null,
                'level' => rand(1, 50),
                'star' => rand(1, 5),
                'hp_left' => $pokemon->hp,
                'is_in_team' => ($index < 6),
                'is_favorite' => (rand(1, 100) <= 20),
                'obtained_at' => now()
            ]);
        }
        
        $this->command->info('Ajout de Pokémons à l\'utilisateur...');
        foreach ($pokemons->take(8) as $index => $pokemon) {
            Pokedex::create([
                'user_id' => $user->id,
                'pokemon_id' => $pokemon->id,
                'nickname' => null,
                'level' => rand(1, 30),
                'star' => rand(1, 3),
                'hp_left' => $pokemon->hp,
                'is_in_team' => ($index < 6),
                'is_favorite' => (rand(1, 100) <= 20),
                'obtained_at' => now()
            ]);
        }
        
        $this->command->info('Ajout d\'items à l\'admin...');
        foreach ($items->take(5) as $item) {
            Inventory::create([
                'user_id' => $admin->id,
                'item_id' => $item->id,
                'quantity' => rand(1, 10)
            ]);
        }
        
        $this->command->info('Ajout d\'items à l\'utilisateur...');
        foreach ($items->take(3) as $item) {
            Inventory::create([
                'user_id' => $user->id,
                'item_id' => $item->id,
                'quantity' => rand(1, 5)
            ]);
        }
        
        $this->command->info('Inventaires remplis avec succès.');
    }
} 