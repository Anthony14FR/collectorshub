<?php

namespace Database\Seeders;

use App\Models\Club;
use App\Models\User;
use Illuminate\Database\Seeder;

class ClubSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::take(10)->get();

        if ($users->count() < 3) {
            return;
        }

        $clubs = [
            [
                'name' => 'Team Rocket',
                'description' => 'Un club de goat',
                'icon' => '/images/types/Feu.png',
                'leader_id' => $users[0]->id,
            ],
        ];

        foreach ($clubs as $clubData) {
            $club = Club::create($clubData);
            $club->members()->attach($clubData['leader_id'], ['role' => 'leader']);
            $club->updateTotalCp();
        }
    }
}
