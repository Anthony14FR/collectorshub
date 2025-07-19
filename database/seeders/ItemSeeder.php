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
                'name' => 'Pokeball',
                'description' => 'Boh c\'est une pokeball quoi',
                'type' => 'ball',
                'image' => "images/items/pokeball.png",
                'effect' => ['opening'],
                'price' => 5000,
                'rarity' => 'normal',
            ],
            [
                'name' => 'Masterball',
                'description' => 'Ca c\'est dla pokemon mon gaté',
                'type' => 'ball',
                'image' => "images/items/masterball.png",
                'effect' => ['opening'],
                'price' => 10000,
                'rarity' => 'epic',
            ],
        ];

        foreach ($items as $item) {
            Item::firstOrCreate(['name' => $item['name']], $item);
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
            Item::firstOrCreate(['image' => $relativePath], [
                'name' => 'Avatar ' . $basename,
                'description' => "Débloque l'avatar $basename pour votre profil.",
                'type' => 'avatar',
                'image' => $relativePath,
                'effect' => [],
                'price' => 50000,
                'rarity' => 'normal',
            ]);
        });

        $backgrounds = [
            [
                'name' => 'Bots City',
                'description' => 'Une ville stylée',
                'image' => '/images/background/1.gif',
                'price' => 80000
            ],
            [
                'name' => 'Sunset Love',
                'description' => 'Un coucher de soleil romantique à l\'horizon',
                'image' => '/images/background/2.gif',
                'price' => 75000
            ],
            [
                'name' => 'Ocean Stream',
                'description' => 'Horizon de l\'océan',
                'image' => '/images/background/3.gif',
                'price' => 30000
            ],
            [
                'name' => 'Simple Dark Room',
                'description' => 'Une chambre la nuit',
                'image' => '/images/background/4.gif',
                'price' => 25000
            ],
            [
                'name' => 'Pokemon Party',
                'description' => 'Une chambre avec des pokémon',
                'image' => '/images/background/5.gif',
                'price' => 50000
            ],
            [
                'name' => 'Witch Room',
                'description' => 'La chambre mystérieuse d\'une sorcière',
                'image' => '/images/background/6.gif',
                'price' => 75000
            ],
        ];

        foreach ($backgrounds as $background) {
            Item::firstOrCreate(['name' => $background['name']], [
                'name' => $background['name'],
                'description' => $background['description'],
                'type' => 'background',
                'image' => $background['image'],
                'effect' => [],
                'price' => $background['price'],
                'rarity' => 'normal',
            ]);
        }
    }
}
