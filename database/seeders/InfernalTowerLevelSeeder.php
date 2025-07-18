<?php

namespace Database\Seeders;

use App\Models\InfernalTowerLevel;
use Illuminate\Database\Seeder;

class InfernalTowerLevelSeeder extends Seeder
{
    public function run(): void
    {
        $trainerAvatars = [];
        for ($i = 3; $i <= 258; $i++) {
            if (file_exists(public_path("images/trainer/{$i}.png"))) {
                $trainerAvatars[] = "{$i}.png";
            }
        }

        $json = file_get_contents(storage_path('app/private/data/tower-seeder.json'));
        $levels = json_decode($json, true);

        foreach ($levels as $level) {
            $level['trainer_avatar'] = $trainerAvatars[array_rand($trainerAvatars)];
            InfernalTowerLevel::firstOrCreate(['level' => $level['level']], $level);
        }
    }
}
