<?php

namespace Database\Seeders;

use App\Models\Item;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'name' => 'Potion',
                'description' => 'Un item qui restaure 20 PV d\'un Pokémon.',
                'type' => 'heal',
                'image' => "images/items/potion.png",
                'effect' => ['amount' => 20],
                'price' => 300,
                'rarity' => 'normal',
            ],
            [
                'name' => 'Super Potion',
                'description' => 'Un item qui restaure 50 PV d\'un Pokémon.',
                'type' => 'heal',
                'image' => "images/items/super_potion.png",
                'effect' => ['amount' => 50],
                'price' => 700,
                'rarity' => 'rare',
            ],
            [
                'name' => 'Hyper Potion',
                'description' => 'Un item qui restaure 200 PV d\'un Pokémon.',
                'type' => 'heal',
                'image' => "images/items/hyper_potion.png",
                'effect' => ['amount' => 200],
                'price' => 1500,
                'rarity' => 'rare',
            ],
            [
                'name' => 'Pokeball',
                'description' => 'Boh c\'est une pokeball quoi',
                'type' => 'ball',
                'image' => "images/items/pokeball.png",
                'effect' => ['opening'],
                'price' => 100,
                'rarity' => 'normal',
            ],
            [
                'name' => 'Masterball',
                'description' => 'Ca c\'est dla pokemon mon gaté',
                'type' => 'ball',
                'image' => "images/items/masterball.png",
                'effect' => ['opening'],
                'price' => 200,
                'rarity' => 'epic',
            ],
        ];

        foreach ($items as $item) {
            Item::create($item);
        }
    }
}
