<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            GameConfigurationSeeder::class,
            UserSeeder::class,
            PokemonSeeder::class,
            SuccessSeeder::class,
            PokedexSeeder::class,
            ItemSeeder::class,
            PromoCodeSeeder::class,
            InventorySeeder::class,
            MarketplaceSeeder::class,
            ExpeditionSeeder::class,
            InfernalTowerLevelSeeder::class,
            ClubSeeder::class,
        ]);
    }
}
