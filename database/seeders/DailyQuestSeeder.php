<?php

namespace Database\Seeders;

use App\Models\DailyQuest;
use Illuminate\Database\Seeder;

class DailyQuestSeeder extends Seeder
{
    public function run(): void
    {
        $quests = [
            [
                'key' => 'invoke_pokemon',
                'name' => 'Invoquer des Pokémon',
                'description' => 'Invoquez 5 Pokémon',
                'target_count' => 5,
                'rewards' => ['xp' => 1500, 'cash' => 500],
                'sort_order' => 1,
                'is_active' => true
            ],
            [
                'key' => 'upgrade_pokemon',
                'name' => 'Améliorer un Pokémon',
                'description' => 'Améliorez 1 Pokémon',
                'target_count' => 1,
                'rewards' => ['xp' => 1500, 'cash' => 500],
                'sort_order' => 2,
                'is_active' => true
            ],
            [
                'key' => 'complete_expedition',
                'name' => 'Terminer une expédition',
                'description' => 'Terminez 1 expédition',
                'target_count' => 1,
                'rewards' => ['xp' => 1500, 'cash' => 500],
                'sort_order' => 3,
                'is_active' => true
            ],
            [
                'key' => 'use_tower_attempts',
                'name' => 'Utiliser la tour infernale',
                'description' => 'Utilisez les 10 tentatives de la tour infernale',
                'target_count' => 10,
                'rewards' => ['xp' => 2000, 'cash' => 1000],
                'sort_order' => 4,
                'is_active' => true
            ],
            [
                'key' => 'send_friend_gifts',
                'name' => 'Envoyer des cadeaux',
                'description' => 'Envoyez des cadeaux à vos amis',
                'target_count' => 1,
                'rewards' => ['xp' => 1500, 'cash' => 1000],
                'sort_order' => 5,
                'is_active' => true
            ],
            [
                'key' => 'buy_shop_item',
                'name' => 'Acheter un item',
                'description' => 'Achetez un item dans la boutique',
                'target_count' => 1,
                'rewards' => ['xp' => 2000, 'cash' => 2000],
                'sort_order' => 6,
                'is_active' => true
            ]
        ];

        foreach ($quests as $quest) {
            DailyQuest::create($quest);
        }

        $this->command->info('6 quêtes quotidiennes créées avec succès !');
    }
}
