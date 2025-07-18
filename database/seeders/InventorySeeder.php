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
        $admin = User::whereHas('roles', function ($query) {
            $query->where('name', 'admin');
        })->first();

        $users = User::whereHas('roles', function ($query) {
            $query->where('name', 'user');
        })->get();

        if (!$admin || $users->isEmpty()) {
            $this->command->error('Utilisateurs admin ou user non trouvés. Exécutez d\'abord UserSeeder.');
            return;
        }

        $pokemons = Pokemon::orderBy('id')->limit(20)->get();
        $items = Item::orderBy('id')->limit(10)->get();

        if ($pokemons->isEmpty() || $items->isEmpty()) {
            $this->command->error('Aucun Pokémon ou item trouvé. Exécutez d\'abord PokemonSeeder et ItemSeeder.');
            return;
        }

        $pokedexCount = 0;
        $inventoryCount = 0;

        foreach ($pokemons->take(15) as $pokemon) {
            if (!Pokedex::where('user_id', $admin->id)->where('pokemon_id', $pokemon->id)->exists()) {
                Pokedex::create([
                    'user_id' => $admin->id,
                    'pokemon_id' => $pokemon->id,
                    'nickname' => null,
                    'level' => rand(1, 50),
                    'star' => rand(1, 5),
                    'hp_left' => $pokemon->hp,
                    'is_in_team' => false,
                    'is_favorite' => (rand(1, 100) <= 20),
                    'obtained_at' => now()
                ]);
                $pokedexCount++;
            }
        }

        foreach ($users as $user) {
            foreach ($pokemons->take(8) as $pokemon) {
                if (!Pokedex::where('user_id', $user->id)->where('pokemon_id', $pokemon->id)->exists()) {
                    Pokedex::create([
                        'user_id' => $user->id,
                        'pokemon_id' => $pokemon->id,
                        'nickname' => null,
                        'level' => rand(1, 30),
                        'star' => rand(1, 3),
                        'hp_left' => $pokemon->hp,
                        'is_in_team' => false,
                        'is_favorite' => (rand(1, 100) <= 20),
                        'obtained_at' => now()
                    ]);
                    $pokedexCount++;
                }
            }
        }

        foreach ($items->take(5) as $item) {
            if ($this->addItemToInventory($admin->id, $item->id, rand(1, 10))) {
                $inventoryCount++;
            }
        }

        $pokeball = Item::where('name', 'Pokeball')->first();
        $masterball = Item::where('name', 'Masterball')->first();

        if ($pokeball) {
            if ($this->addItemToInventory($admin->id, $pokeball->id, 100)) {
                $inventoryCount++;
            }
        }

        if ($masterball) {
            if ($this->addItemToInventory($admin->id, $masterball->id, 100)) {
                $inventoryCount++;
            }
        }

        foreach ($users as $user) {
            foreach ($items->take(3) as $item) {
                if ($this->addItemToInventory($user->id, $item->id, rand(1, 5))) {
                    $inventoryCount++;
                }
            }
        }

        $this->command->info("{$pokedexCount} Pokemons ajouter aléatoirement | {$inventoryCount} Items ajouter aléatoirement");
    }

    private function addItemToInventory(int $userId, int $itemId, int $quantity): bool
    {
        $existingInventory = Inventory::where('user_id', $userId)
            ->where('item_id', $itemId)
            ->first();

        if ($existingInventory) {
            return false;
        } else {
            Inventory::create([
                'user_id' => $userId,
                'item_id' => $itemId,
                'quantity' => min($quantity, 100)
            ]);
            return true;
        }
    }
}
