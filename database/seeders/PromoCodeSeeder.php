<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PromoCode;
use App\Models\Item;

class PromoCodeSeeder extends Seeder
{
    public function run(): void
    {
        PromoCode::create([
            'code' => 'UNIQUE100',
            'is_active' => true,
            'expires_at' => now()->addMonths(1),
            'is_global' => false,
            'cash' => 100
        ]);

        PromoCode::create([
            'code' => 'GLOBAL150',
            'is_active' => true,
            'expires_at' => now()->addMonths(3),
            'is_global' => true,
            'cash' => 150
        ]);

        $promoWithItem = PromoCode::create([
            'code' => 'POKEBALL5',
            'is_active' => true,
            'expires_at' => now()->addMonths(2),
            'is_global' => true,
            'cash' => 0
        ]);

        $pokeball = Item::firstOrCreate(
            ['name' => 'Poké Ball'],
            [
                'description' => 'Boh c\'est une pokeball quoi',
                'cost' => 200,
                'image_path' => '/images/items/pokeball.png',
                'type' => 'ball',
                'pokemon_per_ball' => 1
            ]
        );

        $promoWithItem->items()->attach($pokeball->id, ['quantity' => 5]);
    }
} 