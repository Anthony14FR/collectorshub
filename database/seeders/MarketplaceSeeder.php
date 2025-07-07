<?php

namespace Database\Seeders;

use App\Models\Marketplace;
use App\Models\Pokedex;
use App\Models\User;
use Illuminate\Database\Seeder;

class MarketplaceSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        
        if ($users->isEmpty()) {
            $this->command->error('Aucun utilisateur trouvé. Exécutez d\'abord UserSeeder.');
            return;
        }
        
        $this->command->info('Création des annonces marketplace...');
        
        foreach ($users as $user) {
            $availablePokemons = $user->pokedex()
                ->where('is_in_team', false)
                ->inRandomOrder()
                ->limit(2)
                ->get();
            
            if ($availablePokemons->isEmpty()) {
                $this->command->info("Aucun Pokémon disponible pour l'utilisateur {$user->username}");
                continue;
            }
            
            foreach ($availablePokemons as $pokemon) {
                $rarity = $pokemon->pokemon->rarity;
                $priceRange = Marketplace::getPriceRange($rarity);
                
                $price = rand($priceRange['min'], $priceRange['max']);
                
                Marketplace::create([
                    'seller_id' => $user->id,
                    'pokemon_id' => $pokemon->id,
                    'price' => $price,
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
        
        $listingsCount = Marketplace::count();
        $this->command->info('Annonces marketplace créées avec succès. ' . $listingsCount . ' Pokémons ont été mis en vente.');
    }
} 