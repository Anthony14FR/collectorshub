<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\Pokemon;
use App\Models\User;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();

        if (!$user) {
            $this->command->error('Aucun utilisateur trouvé. Exécutez d\'abord UserSeeder.');
            return;
        }

        $announcements = [
            [
                'title' => 'Nouveau Event Légendaire !',
                'message' => 'Mew apparaît maintenant dans les invocations ! Taux d\'apparition augmenté de 300% pendant une semaine. Ne ratez pas cette chance unique d\'attraper ce Pokémon mythique !',
                'is_read' => false,
                'created_at' => now()->subHours(2)
            ],
            [
                'title' => 'Pokémon Shiny Event',
                'message' => 'Les chances d\'obtenir un Pokémon Shiny sont doublées ce week-end ! Tous vos Pokémon favoris brillent d\'un éclat particulier. Tentez votre chance dans le gacha !',
                'is_read' => false,
                'created_at' => now()->subHours(8)
            ],
            [
                'title' => 'Nouvelle fonctionnalité : Expéditions',
                'message' => 'Partez à l\'aventure avec vos Pokémon ! Les expéditions vous permettent d\'explorer de nouveaux territoires et de découvrir des objets rares. Disponible dès maintenant !',
                'is_read' => false,
                'created_at' => now()->subHours(15)
            ],
            [
                'title' => 'Maintenance terminée',
                'message' => 'La maintenance est terminée ! De nouvelles améliorations ont été apportées au système de combat et à l\'interface. Merci pour votre patience.',
                'is_read' => true,
                'created_at' => now()->subHours(36)
            ]
        ];

        foreach ($announcements as $announcement) {
            Notification::create([
                'user_id' => $user->id,
                'type' => 'announcement',
                'title' => $announcement['title'],
                'message' => $announcement['message'],
                'is_read' => $announcement['is_read'],
                'created_at' => $announcement['created_at'],
                'updated_at' => $announcement['created_at']
            ]);
        }

        $charizard = Pokemon::where('name', 'Dracaufeu')->first();
        $blastoise = Pokemon::where('name', 'Tortank')->first();
        $venusaur = Pokemon::where('name', 'Florizarre')->first();

        if (!$charizard) {
            $charizard = Pokemon::find(6);
        }
        if (!$blastoise) {
            $blastoise = Pokemon::find(9);
        }
        if (!$venusaur) {
            $venusaur = Pokemon::find(3);
        }

        $traders = [
            'FireMaster2024',
            'AquaTrainer',
            'PlantCollector'
        ];

        $marketplaceHistory = [
            [
                'type' => 'marketplace_sell',
                'title' => 'Vente réussie - ' . ($charizard ? $charizard->name : 'Dracaufeu'),
                'message' => 'Votre ' . ($charizard ? $charizard->name : 'Dracaufeu') . ' s\'est vendu pour 8500 ₽',
                'data' => [
                    'pokemon_name' => $charizard ? $charizard->name : 'Dracaufeu',
                    'pokemon_id' => $charizard ? $charizard->id : 6,
                    'price' => 8500,
                    'buyer_name' => $traders[0],
                    'rarity' => $charizard ? $charizard->rarity : 'epic',
                    'types' => $charizard ? collect($charizard->types)->pluck('name')->toArray() : ['Feu', 'Vol'],
                    'stars' => 5,
                    'cp' => 2845,
                    'is_shiny' => false
                ],
                'is_read' => false,
                'created_at' => now()->subHours(4)
            ],
            [
                'type' => 'marketplace_sell',
                'title' => 'Vente réussie - ' . ($blastoise ? $blastoise->name : 'Tortank'),
                'message' => 'Votre ' . ($blastoise ? $blastoise->name : 'Tortank') . ' s\'est vendu pour 7200 ₽',
                'data' => [
                    'pokemon_name' => $blastoise ? $blastoise->name : 'Tortank',
                    'pokemon_id' => $blastoise ? $blastoise->id : 9,
                    'price' => 7200,
                    'buyer_name' => $traders[1],
                    'rarity' => $blastoise ? $blastoise->rarity : 'epic',
                    'types' => $blastoise ? collect($blastoise->types)->pluck('name')->toArray() : ['Eau'],
                    'stars' => 4,
                    'cp' => 2456,
                    'is_shiny' => false
                ],
                'is_read' => true,
                'created_at' => now()->subHours(12)
            ],
            [
                'type' => 'marketplace_buy',
                'title' => 'Achat réussi - ' . ($venusaur ? $venusaur->name : 'Florizarre'),
                'message' => 'Vous avez acheté un ' . ($venusaur ? $venusaur->name : 'Florizarre') . ' pour 6800 ₽',
                'data' => [
                    'pokemon_name' => $venusaur ? $venusaur->name : 'Florizarre',
                    'pokemon_id' => $venusaur ? $venusaur->id : 3,
                    'price' => 6800,
                    'seller_name' => $traders[2],
                    'rarity' => $venusaur ? $venusaur->rarity : 'epic',
                    'types' => $venusaur ? collect($venusaur->types)->pluck('name')->toArray() : ['Plante', 'Poison'],
                    'stars' => 4,
                    'cp' => 2198,
                    'is_shiny' => false
                ],
                'is_read' => false,
                'created_at' => now()->subHours(20)
            ]
        ];

        foreach ($marketplaceHistory as $history) {
            Notification::create([
                'user_id' => $user->id,
                'type' => $history['type'],
                'title' => $history['title'],
                'message' => $history['message'],
                'data' => $history['data'],
                'is_read' => $history['is_read'],
                'created_at' => $history['created_at'],
                'updated_at' => $history['created_at']
            ]);
        }

        $this->command->info('NotificationSeeder exécuté avec succès !');
        $this->command->info('- 4 annonces créées (3 non lues, 1 lue)');
        $this->command->info('- 3 notifications d\'historique créées (2 ventes, 1 achat)');
    }
}
