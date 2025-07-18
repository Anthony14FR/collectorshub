<?php

namespace Database\Seeders;

use App\Models\Expedition;
use App\Models\ExpeditionRequirement;
use Illuminate\Database\Seeder;

class ExpeditionSeeder extends Seeder
{
    private $availableTypes = [
        'Normal', 'Feu', 'Eau', 'Electrik', 'Plante', 'Glace', 'Combat', 'Poison',
        'Sol', 'Vol', 'Psy', 'Insecte', 'Roche', 'Spectre', 'Dragon', 'Acier', 'Fee'
    ];

    private $expeditionTemplates = [
        'normal' => [
            'names' => [
                'Promenade dans les Bois',
                'Exploration du Lac',
                'Patrouille Matinale',
                'Collecte de Baies',
                'Reconnaissance Locale'
            ],
            'descriptions' => [
                'Une expédition tranquille pour débuter',
                'Explorez les environs avec vos Pokémons',
                'Une mission simple de reconnaissance',
                'Collectez des ressources dans la nature',
                'Patrouille de routine dans la région'
            ],
            'duration' => 0.5,
            'pokemon_requirements' => [
                'normal' => [1, 5],
                'rare' => [0, 2],
                'epic' => [0, 0],
                'legendary' => [0, 0]
            ],
            'type_requirements' => [0, 1],
            'rewards' => [
                'pokeball' => [1, 5],
                'cash' => [100, 2500],
                'xp' => [1000, 1000]
            ]
        ],
        'rare' => [
            'names' => [
                'Expédition des Grottes',
                'Chasse aux Trésors',
                'Mission Spéciale',
                'Exploration Avancée',
                'Raid Coordonné'
            ],
            'descriptions' => [
                'Une expédition plus dangereuse nécessitant des Pokémons expérimentés',
                'Explorez des zones inconnues à la recherche de trésors',
                'Mission spéciale nécessitant des Pokémons variés',
                'Exploration avancée de territoires hostiles',
                'Raid coordonné contre des Pokémons sauvages'
            ],
            'duration' => 1,
            'pokemon_requirements' => [
                'normal' => [3, 5],
                'rare' => [1, 4],
                'epic' => [0, 1],
                'legendary' => [0, 0]
            ],
            'type_requirements' => [1, 3],
            'rewards' => [
                'pokeball' => [5, 10],
                'cash' => [2500, 5000],
                'xp' => [2500, 2500]
            ]
        ],
        'epic' => [
            'names' => [
                'Expédition Épique',
                'Assaut sur la Forteresse',
                'Mission Légendaire',
                'Conquête Territoriale',
                'Opération Spéciale'
            ],
            'descriptions' => [
                'Une expédition épique nécessitant une équipe puissante',
                'Assaut coordonné sur une forteresse ennemie',
                'Mission de la plus haute importance',
                'Conquête d\'un territoire stratégique',
                'Opération spéciale de grande envergure'
            ],
            'duration' => 1.5,
            'pokemon_requirements' => [
                'normal' => [5, 10],
                'rare' => [3, 6],
                'epic' => [1, 3],
                'legendary' => [0, 1]
            ],
            'type_requirements' => [3, 5],
            'rewards' => [
                'pokeball' => [8, 10],
                'cash' => [5000, 7500],
                'masterball' => [1, 3],
                'xp' => [5000, 5000]
            ]
        ],
        'legendary' => [
            'names' => [
                'Quête Légendaire',
                'Expédition des Maîtres',
                'Mission Ultime',
                'Conquête Légendaire',
                'Défi Suprême'
            ],
            'descriptions' => [
                'La quête ultime réservée aux dresseurs légendaires',
                'Expédition des maîtres Pokémon les plus expérimentés',
                'Mission ultime nécessitant les meilleurs Pokémons',
                'Conquête légendaire d\'une importance capitale',
                'Le défi suprême pour les dresseurs d\'élite'
            ],
            'duration' => 2,
            'pokemon_requirements' => [
                'normal' => [1, 5],
                'rare' => [5, 10],
                'epic' => [5, 10],
                'legendary' => [1, 2]
            ],
            'type_requirements' => [5, 5],
            'rewards' => [
                'pokeball' => [15, 20],
                'cash' => [10000, 15000],
                'masterball' => [5, 10],
                'xp' => [10000, 10000]
            ]
        ]
    ];

    public function run()
    {
        $this->generateExpeditions();
    }

    private function generateExpeditions()
    {
        $expeditionsToGenerate = [
            'normal' => 10,
            'rare' => 10,
            'epic' => 10,
            'legendary' => 10
        ];

        foreach ($expeditionsToGenerate as $rarity => $count) {
            for ($i = 0; $i < $count; $i++) {
                $expedition = $this->createExpedition($rarity);
                if ($expedition) {
                    $this->createRequirements($expedition, $rarity);
                }
            }
        }
    }

    private function createExpedition($rarity)
    {
        $template = $this->expeditionTemplates[$rarity];

        $name = $template['names'][array_rand($template['names'])];
        $description = $template['descriptions'][array_rand($template['descriptions'])];
        $duration = $template['duration'];

        $rewards = $this->generateRewards($rarity);

        return Expedition::firstOrCreate(
            [
                'name' => $name,
                'rarity' => $rarity
            ],
            [
                'description' => $description,
                'duration_minutes' => $duration,
                'rewards' => $rewards,
                'is_active' => true
            ]
        );
    }

    private function generateRewards($rarity)
    {
        $template = $this->expeditionTemplates[$rarity]['rewards'];
        $rewards = [];

        foreach ($template as $type => $range) {
            $amount = rand($range[0], $range[1]);

            if ($type === 'cash') {
                $amount = ceil($amount / 100) * 100;
            }

            $rewards[] = [
                'type' => $type,
                'amount' => $amount
            ];
        }

        return $rewards;
    }

    private function createRequirements($expedition, $rarity)
    {
        $template = $this->expeditionTemplates[$rarity];

        foreach ($template['pokemon_requirements'] as $pokemonRarity => $range) {
            if ($range[1] > 0) {
                $quantity = rand($range[0], $range[1]);
                if ($quantity > 0) {
                    ExpeditionRequirement::firstOrCreate(
                        [
                            'expedition_id' => $expedition->id,
                            'type' => 'rarity',
                            'value' => $pokemonRarity
                        ],
                        [
                            'quantity' => $quantity
                        ]
                    );
                }
            }
        }

        $typeRequirements = $template['type_requirements'];
        $numTypes = rand($typeRequirements[0], $typeRequirements[1]);

        if ($numTypes > 0) {
            $selectedTypes = $this->selectRandomTypes($numTypes);

            foreach ($selectedTypes as $type) {
                ExpeditionRequirement::firstOrCreate(
                    [
                        'expedition_id' => $expedition->id,
                        'type' => 'type',
                        'value' => $type
                    ],
                    [
                        'quantity' => 1
                    ]
                );
            }
        }
    }

    private function selectRandomTypes($numTypes)
    {
        $availableTypes = $this->availableTypes;

        if (count($availableTypes) < $numTypes) {
            $numTypes = count($availableTypes);
        }

        $selectedTypes = [];
        for ($i = 0; $i < $numTypes; $i++) {
            $randomIndex = array_rand($availableTypes);
            $selectedTypes[] = $availableTypes[$randomIndex];
            unset($availableTypes[$randomIndex]);
            $availableTypes = array_values($availableTypes);
        }

        return $selectedTypes;
    }
}
