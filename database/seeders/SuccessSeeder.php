<?php

namespace Database\Seeders;

use App\Models\Success;
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
                'image' => '1.png',
                'type' => 'pokedex',
                'requirements' => ['count' => 1],
                'cash_reward' => 100,
                'xp_reward' => 50
            ],
            [
                'key' => '2',
                'title' => 'Collectionneur débutant',
                'description' => 'Ajouter 25 Pokémon différents au Pokédex.',
                'image' => '2.png',
                'type' => 'pokedex',
                'requirements' => ['count' => 25],
                'cash_reward' => 250,
                'xp_reward' => 125
            ],
            [
                'key' => '3',
                'title' => 'Collectionneur confirmé',
                'description' => 'Ajouter 75 Pokémon différents au Pokédex.',
                'image' => '3.png',
                'type' => 'pokedex',
                'requirements' => ['count' => 75],
                'cash_reward' => 500,
                'xp_reward' => 250
            ],
            [
                'key' => '4',
                'title' => 'Maître du Pokédex',
                'description' => 'Ajouter les 151 Pokémon normaux au Pokédex.',
                'image' => '4.png',
                'type' => 'pokedex',
                'requirements' => ['count' => 151, 'shiny' => false],
                'cash_reward' => 1000,
                'xp_reward' => 500
            ],
            [
                'key' => '5',
                'title' => 'Chasseur de Shiny',
                'description' => 'Ajouter 25 Pokémon shiny différents au Pokédex.',
                'image' => '5.png',
                'type' => 'pokedex',
                'requirements' => ['count' => 25, 'shiny' => true],
                'cash_reward' => 1500,
                'xp_reward' => 750
            ],
            [
                'key' => '6',
                'title' => 'Expert Shiny',
                'description' => 'Ajouter 75 Pokémon shiny différents au Pokédex.',
                'image' => '6.png',
                'type' => 'pokedex',
                'requirements' => ['count' => 75, 'shiny' => true],
                'cash_reward' => 2500,
                'xp_reward' => 1250
            ],
            [
                'key' => '7',
                'title' => 'Légende Shiny',
                'description' => 'Ajouter les 151 Pokémon shiny au Pokédex.',
                'image' => '7.png',
                'type' => 'pokedex',
                'requirements' => ['count' => 151, 'shiny' => true],
                'cash_reward' => 5000,
                'xp_reward' => 2500
            ],
            [
                'key' => '8',
                'title' => 'Pokédex ultime',
                'description' => 'Compléter le Pokédex avec les 302 Pokémon (normaux + shiny).',
                'image' => '8.png',
                'type' => 'pokedex',
                'requirements' => ['count' => 302],
                'cash_reward' => 10000,
                'xp_reward' => 5000
            ],
            [
                'key' => '9',
                'title' => 'Première centaine',
                'description' => 'Capturer 100 Pokémon (doublons inclus).',
                'image' => '9.png',
                'type' => 'capture',
                'requirements' => ['count' => 100],
                'cash_reward' => 300,
                'xp_reward' => 150
            ],
            [
                'key' => '10',
                'title' => 'Chasseur expérimenté',
                'description' => 'Capturer 500 Pokémon.',
                'image' => '10.png',
                'type' => 'capture',
                'requirements' => ['count' => 500],
                'cash_reward' => 800,
                'xp_reward' => 400
            ],
            [
                'key' => '11',
                'title' => 'Chasseur légendaire',
                'description' => 'Capturer 1000 Pokémon.',
                'image' => '11.png',
                'type' => 'capture',
                'requirements' => ['count' => 1000],
                'cash_reward' => 2000,
                'xp_reward' => 1000
            ],
            [
                'key' => '12',
                'title' => 'Maître chasseur',
                'description' => 'Capturer 5000 Pokémon.',
                'image' => '12.png',
                'type' => 'capture',
                'requirements' => ['count' => 5000],
                'cash_reward' => 8000,
                'xp_reward' => 4000
            ],
            [
                'key' => '13',
                'title' => 'Collectionneur normal',
                'description' => 'Capturer les 47 Pokémon de rareté normale.',
                'image' => '13.png',
                'type' => 'rarity',
                'requirements' => ['count' => 47, 'rarity' => 'normal'],
                'cash_reward' => 500,
                'xp_reward' => 250
            ],
            [
                'key' => '14',
                'title' => 'Chasseur rare',
                'description' => 'Capturer les 56 Pokémon rares.',
                'image' => '14.png',
                'type' => 'rarity',
                'requirements' => ['count' => 56, 'rarity' => 'rare'],
                'cash_reward' => 1200,
                'xp_reward' => 600
            ],
            [
                'key' => '15',
                'title' => 'Chasseur épique',
                'description' => 'Capturer les 41 Pokémon épiques.',
                'image' => '15.png',
                'type' => 'rarity',
                'requirements' => ['count' => 41, 'rarity' => 'epic'],
                'cash_reward' => 3000,
                'xp_reward' => 1500
            ],
            [
                'key' => '16',
                'title' => 'Dompteur de légendes',
                'description' => 'Capturer les 7 Pokémon légendaires.',
                'image' => '16.png',
                'type' => 'rarity',
                'requirements' => ['count' => 7, 'rarity' => 'legendary'],
                'cash_reward' => 7000,
                'xp_reward' => 3500
            ],
            [
                'key' => '17',
                'title' => 'Shiny normal',
                'description' => 'Capturer les 47 Pokémon shiny de rareté normale.',
                'image' => '17.png',
                'type' => 'rarity',
                'requirements' => ['count' => 47, 'rarity' => 'normal', 'shiny' => true],
                'cash_reward' => 2500,
                'xp_reward' => 1250
            ],
            [
                'key' => '18',
                'title' => 'Shiny rare',
                'description' => 'Capturer les 56 Pokémon shiny rares.',
                'image' => '18.png',
                'type' => 'rarity',
                'requirements' => ['count' => 56, 'rarity' => 'rare', 'shiny' => true],
                'cash_reward' => 6000,
                'xp_reward' => 3000
            ],
            [
                'key' => '19',
                'title' => 'Shiny épique',
                'description' => 'Capturer les 41 Pokémon shiny épiques.',
                'image' => '19.png',
                'type' => 'rarity',
                'requirements' => ['count' => 41, 'rarity' => 'epic', 'shiny' => true],
                'cash_reward' => 15000,
                'xp_reward' => 7500
            ],
            [
                'key' => '20',
                'title' => 'Shiny légendaire',
                'description' => 'Capturer les 7 Pokémon shiny légendaires.',
                'image' => '20.png',
                'type' => 'rarity',
                'requirements' => ['count' => 7, 'rarity' => 'legendary', 'shiny' => true],
                'cash_reward' => 30000,
                'xp_reward' => 15000
            ],
            [
                'key' => '21',
                'title' => 'Premier ami',
                'description' => 'Ajouter ton premier ami.',
                'image' => '27.png',
                'type' => 'friend',
                'requirements' => ['count' => 1],
                'cash_reward' => 500,
                'xp_reward' => 250
            ],
            [
                'key' => '22',
                'title' => 'Grimpeur débutant',
                'description' => 'Terminer 50 étages dans la tour infernale.',
                'image' => '21.png',
                'type' => 'tower',
                'requirements' => ['level' => 50],
                'cash_reward' => 3500,
                'xp_reward' => 1500
            ],
            [
                'key' => '23',
                'title' => 'Grimpeur confirmé',
                'description' => 'Terminer 100 étages dans la tour infernale.',
                'image' => '28.png',
                'type' => 'tower',
                'requirements' => ['level' => 100],
                'cash_reward' => 25000,
                'xp_reward' => 5000
            ],
            [
                'key' => '24',
                'title' => 'Maître grimpeur',
                'description' => 'Terminer 200 étages dans la tour infernale.',
                'image' => '29.png',
                'type' => 'tower',
                'requirements' => ['level' => 200],
                'cash_reward' => 35000,
                'xp_reward' => 10000
            ],
            [
                'key' => '25',
                'title' => 'Conquérant de la tour',
                'description' => 'Terminer 300 étages dans la tour infernale.',
                'image' => '22.png',
                'type' => 'tower',
                'requirements' => ['level' => 300],
                'cash_reward' => 50000,
                'xp_reward' => 20000
            ],
            [
                'key' => '26',
                'title' => 'Légende de la tour',
                'description' => 'Terminer 450 étages dans la tour infernale.',
                'image' => '23.png',
                'type' => 'tower',
                'requirements' => ['level' => 450],
                'cash_reward' => 100000,
                'xp_reward' => 35000
            ],
            [
                'key' => '27',
                'title' => 'Explorateur débutant',
                'description' => 'Terminer 5 expéditions.',
                'image' => '24.png',
                'type' => 'expedition',
                'requirements' => ['count' => 5],
                'cash_reward' => 1500,
                'xp_reward' => 1500
            ],
            [
                'key' => '28',
                'title' => 'Explorateur confirmé',
                'description' => 'Terminer 25 expéditions.',
                'image' => '30.png',
                'type' => 'expedition',
                'requirements' => ['count' => 25],
                'cash_reward' => 5500,
                'xp_reward' => 6000
            ],
            [
                'key' => '29',
                'title' => 'Maître explorateur',
                'description' => 'Terminer 50 expéditions.',
                'image' => '25.png',
                'type' => 'expedition',
                'requirements' => ['count' => 50],
                'cash_reward' => 10000,
                'xp_reward' => 10000
            ],
            [
                'key' => '30',
                'title' => 'Légende de l\'exploration',
                'description' => 'Terminer 100 expéditions.',
                'image' => '26.png',
                'type' => 'expedition',
                'requirements' => ['count' => 100],
                'cash_reward' => 20000,
                'xp_reward' => 20000
            ]
        ];

        foreach ($successes as $success) {
            Success::updateOrCreate(['key' => $success['key']], $success);
        }
    }
}
