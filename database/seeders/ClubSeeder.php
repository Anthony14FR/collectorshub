<?php

namespace Database\Seeders;

use App\Models\Club;
use App\Models\User;
use Illuminate\Database\Seeder;

class ClubSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('username', 'admin')->first();
        $regularUsers = User::where('username', '!=', 'admin')
            ->whereBetween('id', [2, 10])
            ->take(8)
            ->get();

        if (!$admin || $regularUsers->count() < 8) {
            $this->command->info('Pas assez d\'utilisateurs pour créer les clubs. Skipping...');
            return;
        }

        if (Club::exists()) {
            $this->command->info('Les clubs existent déjà. Skipping...');
            return;
        }

        $clubs = [
            [
                'name' => 'Team Rocket',
                'description' => 'Un club de goat',
                'icon' => '/images/club-icons/1.png',
                'leader_id' => $admin->id,
                'members' => [$regularUsers[0]->id, $regularUsers[1]->id]
            ],
            [
                'name' => 'La Tcheam Vroom',
                'description' => 'Que gens faire vroom vroom',
                'icon' => '/images/club-icons/2.png',
                'leader_id' => $regularUsers[2]->id,
                'members' => [$regularUsers[3]->id, $regularUsers[4]->id]
            ],
            [
                'name' => 'Les motards',
                'description' => 'En y comme Jul',
                'icon' => '/images/club-icons/4.png',
                'leader_id' => $regularUsers[5]->id,
                'members' => [$regularUsers[6]->id, $regularUsers[7]->id]
            ]
        ];

        foreach ($clubs as $clubData) {
            $members = $clubData['members'];
            unset($clubData['members']);

            $club = Club::create($clubData);
            $club->members()->attach($clubData['leader_id'], ['role' => 'leader']);

            foreach ($members as $memberId) {
                if ($memberId !== $clubData['leader_id']) {
                    $club->members()->attach($memberId, ['role' => 'member']);
                }
            }

            $club->updateTotalCp();
        }
    }
}
