<?php

namespace App\Services;

use App\Models\InfernalTowerLevel;
use App\Models\Item;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class InfernalTowerService
{
    public function getTowerLevels(User $user)
    {
        $currentLevel = $user->infernal_tower_current_level;
        $team = $user->pokedex()->where('is_in_team', true)->orderBy('team_position')->get();
        $userTeamCp = $team->sum('cp');

        $levels = InfernalTowerLevel::where('level', '>=', $currentLevel)
                                    ->orderBy('level')
                                    ->take(5)
                                    ->get();

        $levelsWithSuccessRate = $levels->map(function ($level) use ($userTeamCp) {
            $successRate = $this->calculateSuccessChance($userTeamCp, $level->team_cp);
            $level->success_rate = $successRate;
            return $level;
        });

        return [
            'current_level' => $currentLevel,
            'levels' => $levelsWithSuccessRate,
            'user_team_cp' => $userTeamCp,
            'user_team' => $team->values(),
        ];
    }

    public function attemptLevel(User $user, int $levelToAttempt)
    {
        $lockKey = "infernal_tower_attempt_{$user->id}";

        if (Cache::has($lockKey)) {
            return ['success' => false, 'message' => 'Une tentative est déjà en cours. Veuillez patienter...'];
        }

        Cache::put($lockKey, true, 10);

        try {
            $towerLevel = InfernalTowerLevel::where('level', $levelToAttempt)->firstOrFail();

            $user->resetInfernalTowerDailyDefeats();
            $user->refresh();

            $remainingDefeats = $user->getRawDailyDefeats();

            if ($remainingDefeats <= 0) {
                return ['success' => false, 'message' => 'Vous avez épuisé toutes vos défaites quotidiennes. Revenez demain !'];
            }

            $userTeamCp = $user->pokedex()->where('is_in_team', true)->sum('cp');

            $chanceOfSuccess = $this->calculateSuccessChance($userTeamCp, $towerLevel->team_cp);

            $isSuccess = (rand(1, 100) <= $chanceOfSuccess);

            if ($isSuccess) {
                if ($levelToAttempt >= $user->infernal_tower_current_level) {
                    $user->infernal_tower_current_level = $levelToAttempt + 1;
                }
                $user->save();

                $this->applyRewards($user, $towerLevel->rewards);

                return ['success' => true, 'message' => 'Félicitations, vous avez vaincu ce niveau !', 'rewards' => $towerLevel->rewards];
            } else {
                $newDefeats = $remainingDefeats - 1;
                $user->infernal_tower_daily_defeats = $newDefeats;
                $user->save();

                return ['success' => false, 'message' => 'Échec. Essayez encore !', 'daily_defeats' => $newDefeats];
            }
        } finally {
            Cache::forget($lockKey);
        }
    }

    private function calculateSuccessChance(int $userCp, int $towerCp): int
    {
        if ($userCp <= 0) {
            return 0;
        }

        $k = 0.00015;
        $difference = $userCp - $towerCp;
        $rate = 100 / (1 + exp(-$k * $difference));

        return (int) round($rate);
    }

    private function applyRewards(User $user, array $rewards)
    {
        if (!is_array($rewards)) {
            return;
        }

        $pokeball = Item::where('name', 'Pokeball')->first();
        $masterball = Item::where('name', 'Masterball')->first();

        if (isset($rewards['pokeballs']) && is_numeric($rewards['pokeballs']) && $rewards['pokeballs'] > 0 && $pokeball) {
            $inventory = $user->inventory()->firstOrCreate(['item_id' => $pokeball->id], ['quantity' => 0]);
            $inventory->increment('quantity', (int) $rewards['pokeballs']);
        }

        if (isset($rewards['masterballs']) && is_numeric($rewards['masterballs']) && $rewards['masterballs'] > 0 && $masterball) {
            $inventory = $user->inventory()->firstOrCreate(['item_id' => $masterball->id], ['quantity' => 0]);
            $inventory->increment('quantity', (int) $rewards['masterballs']);
        }

        if (isset($rewards['money']) && is_numeric($rewards['money']) && $rewards['money'] > 0) {
            $user->increment('cash', (int) $rewards['money']);
        }

        if (isset($rewards['exp']) && is_numeric($rewards['exp']) && $rewards['exp'] > 0) {
            $user->addXp((int) $rewards['exp']);
        }

        $user->save();
    }
}
