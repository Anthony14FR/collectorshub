<?php

namespace Database\Seeders;

use App\Models\GameConfiguration;
use Illuminate\Database\Seeder;

class GameConfigurationSeeder extends Seeder
{
    public function run(): void
    {
        $configurations = [
            [
                'category' => 'level_rewards',
                'key' => 'milestones',
                'value' => [
                    'milestone_5' => ['cash' => 1500, 'pokeballs' => 10, 'masterballs' => 0],
                    'milestone_10' => ['cash' => 3000, 'pokeballs' => 0, 'masterballs' => 5],
                    'milestone_25' => ['cash' => 5000, 'pokeballs' => 10, 'masterballs' => 5],
                    'milestone_50' => ['cash' => 10000, 'pokeballs' => 20, 'masterballs' => 15],
                    'regular_level' => ['cash' => 2500, 'pokeballs' => 2, 'masterballs' => 0]
                ],
                'description' => 'Récompenses de niveau par palier'
            ],
            [
                'category' => 'rarity_probabilities',
                'key' => 'ball_types',
                'value' => [
                    'Pokeball' => [
                        'normal' => 70,
                        'rare' => 27,
                        'epic' => 2.7,
                        'legendary' => 0.3
                    ],
                    'Masterball' => [
                        'normal' => 34,
                        'rare' => 60,
                        'epic' => 5,
                        'legendary' => 1
                    ]
                ],
                'description' => 'Probabilités de rareté par type de ball'
            ],
            [
                'category' => 'xp_rewards',
                'key' => 'rarity_bonuses',
                'value' => [
                    'normal' => [
                        'base' => 1,
                        'shiny' => 5,
                        'first_catch_bonus' => 1
                    ],
                    'rare' => [
                        'base' => 5,
                        'shiny' => 10,
                        'first_catch_bonus' => 5
                    ],
                    'epic' => [
                        'base' => 10,
                        'shiny' => 20,
                        'first_catch_bonus' => 10
                    ],
                    'legendary' => [
                        'base' => 35,
                        'shiny' => 70,
                        'first_catch_bonus' => 15
                    ]
                ],
                'description' => 'Gains d\'XP par rareté et bonus'
            ],
            [
                'category' => 'shiny_rate',
                'key' => 'chance',
                'value' => 1,
                'description' => 'Taux de chance d\'obtenir un Pokémon shiny (en pourcentage)'
            ]
        ];

        foreach ($configurations as $config) {
            GameConfiguration::updateOrCreate(
                ['category' => $config['category'], 'key' => $config['key']],
                $config
            );
        }
    }
}
