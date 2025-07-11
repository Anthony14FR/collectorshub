<?php

namespace Database\Seeders;

use App\Models\Item;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

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

        $avatarDir = public_path('images/trainer');
        $files = collect(File::files($avatarDir))
            ->reject(function ($file) {
                $basename = pathinfo($file, PATHINFO_FILENAME);
                return in_array($basename, ['1', '2']);
            });
        $files->each(function ($file) {
            $basename = pathinfo($file, PATHINFO_FILENAME);
            $relativePath = '/images/trainer/' . basename($file);
            Item::create([
                'name' => 'Avatar ' . $basename,
                'description' => "Débloque l'avatar $basename pour votre profil.",
                'type' => 'avatar',
                'image' => $relativePath,
                'effect' => [],
                'price' => 1000,
                'rarity' => 'normal',
            ]);
        });
    }
}
