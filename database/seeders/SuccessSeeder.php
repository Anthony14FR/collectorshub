<?php

namespace Database\Seeders;

use App\Models\Success;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SuccessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $successes = [
            [
                'key' => '1',
                'title' => 'Premier pas',
                'description' => 'Ajouter ton premier Pokémon au Pokédex.',
                'type' => 'pokedex',
                'requirements' => ['count' => 1]
            ],
            [
                'key' => '2',
                'title' => 'Collectionneur débutant',
                'description' => 'Ajouter 25 Pokémon différents au Pokédex.',
                'type' => 'pokedex',
                'requirements' => ['count' => 25]
            ],
            [
                'key' => '3',
                'title' => 'Collectionneur confirmé',
                'description' => 'Ajouter 75 Pokémon différents au Pokédex.',
                'type' => 'pokedex',
                'requirements' => ['count' => 75]
            ],
            [
                'key' => '4',
                'title' => 'Maître du Pokédex',
                'description' => 'Ajouter les 151 Pokémon normaux au Pokédex.',
                'type' => 'pokedex',
                'requirements' => ['count' => 151, 'shiny' => false]
            ],
            [
                'key' => '5',
                'title' => 'Chasseur de Shiny',
                'description' => 'Ajouter 25 Pokémon shiny différents au Pokédex.',
                'type' => 'pokedex',
                'requirements' => ['count' => 25, 'shiny' => true]
            ],
            [
                'key' => '6',
                'title' => 'Expert Shiny',
                'description' => 'Ajouter 75 Pokémon shiny différents au Pokédex.',
                'type' => 'pokedex',
                'requirements' => ['count' => 75, 'shiny' => true]
            ],
            [
                'key' => '7',
                'title' => 'Légende Shiny',
                'description' => 'Ajouter les 151 Pokémon shiny au Pokédex.',
                'type' => 'pokedex',
                'requirements' => ['count' => 151, 'shiny' => true]
            ],
            [
                'key' => '8',
                'title' => 'Pokédex ultime',
                'description' => 'Compléter le Pokédex avec les 302 Pokémon (normaux + shiny).',
                'type' => 'pokedex',
                'requirements' => ['count' => 302]
            ],
            [
                'key' => '9',
                'title' => 'Première centaine',
                'description' => 'Capturer 100 Pokémon (doublons inclus).',
                'type' => 'capture',
                'requirements' => ['count' => 100]
            ],
            [
                'key' => '10',
                'title' => 'Chasseur expérimenté',
                'description' => 'Capturer 500 Pokémon.',
                'type' => 'capture',
                'requirements' => ['count' => 500]
            ],
            [
                'key' => '11',
                'title' => 'Chasseur légendaire',
                'description' => 'Capturer 1000 Pokémon.',
                'type' => 'capture',
                'requirements' => ['count' => 1000]
            ],
            [
                'key' => '12',
                'title' => 'Maître chasseur',
                'description' => 'Capturer 5000 Pokémon.',
                'type' => 'capture',
                'requirements' => ['count' => 5000]
            ],
            [
                'key' => '13',
                'title' => 'Collectionneur normal',
                'description' => 'Capturer les 47 Pokémon de rareté normale.',
                'type' => 'rarity',
                'requirements' => ['count' => 47, 'rarity' => 'normal']
            ],
            [
                'key' => '14',
                'title' => 'Chasseur rare',
                'description' => 'Capturer les 56 Pokémon rares.',
                'type' => 'rarity',
                'requirements' => ['count' => 56, 'rarity' => 'rare']
            ],
            [
                'key' => '15',
                'title' => 'Chasseur épique',
                'description' => 'Capturer les 41 Pokémon épiques.',
                'type' => 'rarity',
                'requirements' => ['count' => 41, 'rarity' => 'epic']
            ],
            [
                'key' => '16',
                'title' => 'Dompteur de légendes',
                'description' => 'Capturer les 7 Pokémon légendaires.',
                'type' => 'rarity',
                'requirements' => ['count' => 7, 'rarity' => 'legendary']
            ],
            [
                'key' => '17',
                'title' => 'Shiny normal',
                'description' => 'Capturer les 47 Pokémon shiny de rareté normale.',
                'type' => 'rarity',
                'requirements' => ['count' => 47, 'rarity' => 'normal', 'shiny' => true]
            ],
            [
                'key' => '18',
                'title' => 'Shiny rare',
                'description' => 'Capturer les 56 Pokémon shiny rares.',
                'type' => 'rarity',
                'requirements' => ['count' => 56, 'rarity' => 'rare', 'shiny' => true]
            ],
            [
                'key' => '19',
                'title' => 'Shiny épique',
                'description' => 'Capturer les 41 Pokémon shiny épiques.',
                'type' => 'rarity',
                'requirements' => ['count' => 41, 'rarity' => 'epic', 'shiny' => true]
            ],
            [
                'key' => '20',
                'title' => 'Shiny légendaire',
                'description' => 'Capturer les 7 Pokémon shiny légendaires.',
                'type' => 'rarity',
                'requirements' => ['count' => 7, 'rarity' => 'legendary', 'shiny' => true]
            ]
        ];

        foreach ($successes as $success) {
            Success::create($success);
        }
    }
}
