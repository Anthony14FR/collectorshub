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
                'name' => 'Kelpsy',
                'description' => 'Un item qui augmente l\'attaque d\'un Pokémon.',
                'type' => 'boost',
                'effect' => ['stat' => 'attack', 'amount' => 1],
                'price' => 500,
                'rarity' => 'normal',
            ],
            [
                'name' => 'Tamato',
                'description' => 'Un item qui augmente la vitesse d\'un Pokémon.',
                'type' => 'boost',
                'effect' => ['stat' => 'speed', 'amount' => 1],
                'price' => 500,
                'rarity' => 'normal',
            ],
            [
                'name' => 'Pomeg',
                'description' => 'Un item qui augmente les points de vie d\'un Pokémon.',
                'type' => 'boost',
                'effect' => ['stat' => 'hp', 'amount' => 1],
                'price' => 500,
                'rarity' => 'normal',
            ],
            [
                'name' => 'Grepa',
                'description' => 'Un item qui augmente l\'attaque spéciale d\'un Pokémon.',
                'type' => 'boost',
                'effect' => ['stat' => 'special_attack', 'amount' => 1],
                'price' => 500,
                'rarity' => 'normal',
            ],
            [
                'name' => 'Qualot',
                'description' => 'Un item qui augmente la défense d\'un Pokémon.',
                'type' => 'boost',
                'effect' => ['stat' => 'defense', 'amount' => 1],
                'price' => 500,
                'rarity' => 'normal',
            ],
            [
                'name' => 'Hondew',
                'description' => 'Un item qui augmente la défense spéciale d\'un Pokémon.',
                'type' => 'boost',
                'effect' => ['stat' => 'special_defense', 'amount' => 1],
                'price' => 500,
                'rarity' => 'normal',
            ],
            [
                'name' => 'Super Kelpsy',
                'description' => 'Un item rare qui augmente considérablement l\'attaque d\'un Pokémon.',
                'type' => 'boost',
                'effect' => ['stat' => 'attack', 'amount' => 5],
                'price' => 2500,
                'rarity' => 'rare',
            ],
            [
                'name' => 'Super Tamato',
                'description' => 'Un item rare qui augmente considérablement la vitesse d\'un Pokémon.',
                'type' => 'boost',
                'effect' => ['stat' => 'speed', 'amount' => 5],
                'price' => 2500,
                'rarity' => 'rare',
            ],
            [
                'name' => 'Super Pomeg',
                'description' => 'Un item rare qui augmente considérablement les points de vie d\'un Pokémon.',
                'type' => 'boost',
                'effect' => ['stat' => 'hp', 'amount' => 5],
                'price' => 2500,
                'rarity' => 'rare',
            ],
            [
                'name' => 'Super Grepa',
                'description' => 'Un item rare qui augmente considérablement l\'attaque spéciale d\'un Pokémon.',
                'type' => 'boost',
                'effect' => ['stat' => 'special_attack', 'amount' => 5],
                'price' => 2500,
                'rarity' => 'rare',
            ],
            [
                'name' => 'Super Qualot',
                'description' => 'Un item rare qui augmente considérablement la défense d\'un Pokémon.',
                'type' => 'boost',
                'effect' => ['stat' => 'defense', 'amount' => 5],
                'price' => 2500,
                'rarity' => 'rare',
            ],
            [
                'name' => 'Super Hondew',
                'description' => 'Un item rare qui augmente considérablement la défense spéciale d\'un Pokémon.',
                'type' => 'boost',
                'effect' => ['stat' => 'special_defense', 'amount' => 5],
                'price' => 2500,
                'rarity' => 'rare',
            ],
            [
                'name' => 'Potion',
                'description' => 'Un item qui restaure 20 PV d\'un Pokémon.',
                'type' => 'heal',
                'effect' => ['amount' => 20],
                'price' => 300,
                'rarity' => 'normal',
            ],
            [
                'name' => 'Super Potion',
                'description' => 'Un item qui restaure 50 PV d\'un Pokémon.',
                'type' => 'heal',
                'effect' => ['amount' => 50],
                'price' => 700,
                'rarity' => 'rare',
            ],
            [
                'name' => 'Hyper Potion',
                'description' => 'Un item qui restaure 200 PV d\'un Pokémon.',
                'type' => 'heal',
                'effect' => ['amount' => 200],
                'price' => 1500,
                'rarity' => 'rare',
            ],
            [
                'name' => 'Pokeball',
                'description' => 'Boh c\'est une pokeball quoi',
                'type' => 'ball',
                'effect' => ['opening'],
                'price' => 100,
                'rarity' => 'normal',
            ],
            [
                'name' => 'Masterball',
                'description' => 'Ca c\'est dla pokemon mon gaté',
                'type' => 'ball',
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
