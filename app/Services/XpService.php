<?php

namespace App\Services;

use App\Models\Pokedex;
use App\Models\Pokemon;
use App\Models\User;

class XpService
{
    public function __construct(
        private GameConfigurationService $configService
    ) {
    }

    public function addXp(User $user, int $xp): void
    {
        $user->experience += $xp;
        $user->save();

        $this->checkLevelUp($user);
    }

    public function addXpForPokedexEntry(User $user, Pokedex $pokedex): void
    {
        $pokemon = $pokedex->pokemon;
        if (!$pokemon) {
            $pokemon = Pokemon::find($pokedex->pokemon_id);
            if (!$pokemon) {
                return;
            }
        }

        $isShiny = isset($pokemon->is_shiny) && $pokemon->is_shiny;
        $rarity = $pokemon->rarity;
        $xpRewards = $this->configService->getXpRewards();
        $rarityConfig = $xpRewards['rarity_bonuses'][$rarity] ?? $xpRewards['rarity_bonuses']['normal'];

        $xp = $isShiny ? $rarityConfig['shiny'] : $rarityConfig['base'];

        $alreadyOwned = $user->pokedex()->where('pokemon_id', $pokemon->id)->count() > 1;
        if (!$alreadyOwned) {
            $xp += $rarityConfig['first_catch_bonus'];
        }

        $this->addXp($user, $xp);
    }

    public function checkLevelUp(User $user): void
    {
        $experienceNeededForNextLevel = $this->getTotalExperienceForLevel($user->level + 1);

        if ($experienceNeededForNextLevel > 0 && $user->experience >= $experienceNeededForNextLevel) {
            $oldLevel = $user->level;
            $user->level++;
            $user->save();

            $this->checkLevelUp($user);
        }
    }

    public function getTotalExperienceForLevel(int $level): int
    {
        if ($level <= 1) {
            return 0;
        }

        $totalExperience = 0;
        for ($i = 1; $i < $level; $i++) {
            $totalExperience += $this->getExperienceForLevelUp($i);
        }
        return $totalExperience;
    }

    private function getExperienceForLevelUp(int $level): int
    {
        $experience = 100 * pow($level, 1.5);

        if ($experience < 1000) {
            return (int) round($experience, -1);
        }

        return (int) round($experience, -2);
    }

    public function getExperiencePercentage(User $user): float
    {
        $xpForCurrentLevel = $this->getTotalExperienceForLevel($user->level);
        $xpForNextLevel = $this->getTotalExperienceForLevel($user->level + 1);

        $xpNeededForLevelUp = $xpForNextLevel - $xpForCurrentLevel;

        if ($xpNeededForLevelUp === 0) {
            return 100;
        }

        $currentLevelProgress = $user->experience - $xpForCurrentLevel;

        return round(($currentLevelProgress / $xpNeededForLevelUp) * 100, 2);
    }
}
