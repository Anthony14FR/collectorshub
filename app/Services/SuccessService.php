<?php

namespace App\Services;

use App\Models\Success;
use App\Models\User;
use App\Models\UserSuccess;

class SuccessService
{
    public function checkAndUnlockSuccesses(User $user)
    {
        $successes = Success::all();

        foreach ($successes as $success) {
            if (!$this->userHasSuccess($user, $success)) {
                if ($this->checkSuccessRequirements($user, $success)) {
                    $this->unlockSuccess($user, $success);
                }
            }
        }
    }

    public function checkSuccessRequirements(User $user, Success $success): bool
    {
        $requirements = $success->requirements;

        switch ($success->type) {
            case 'pokedex':
                return $this->checkPokedexRequirements($user, $requirements);
            case 'capture':
                return $this->checkCaptureRequirements($user, $requirements);
            case 'rarity':
                return $this->checkRarityRequirements($user, $requirements);
            default:
                return false;
        }
    }

    public function claimSuccess(User $user, int $successId): bool
    {
        $userSuccess = $user->userSuccesses()
                            ->where('success_id', $successId)
                            ->where('is_claimed', false)
                            ->first();

        if ($userSuccess) {
            $userSuccess->update([
                'is_claimed' => true,
                'claimed_at' => now()
            ]);

            $success = $userSuccess->success;
            if ($success) {
                $user->cash += $success->cash_reward;
                $user->experience += $success->xp_reward;
                $user->save();
                $this->checkLevelUp($user);
            }

            return true;
        }

        return false;
    }

    public function claimAllSuccesses(User $user): int
    {
        $unclaimedSuccesses = $user->userSuccesses()->with('success')->where('is_claimed', false)->get();
        if ($unclaimedSuccesses->isEmpty()) {
            return 0;
        }

        $totalCashReward = 0;
        $totalXpReward = 0;

        foreach ($unclaimedSuccesses as $userSuccess) {
            $totalCashReward += $userSuccess->success->cash_reward;
            $totalXpReward += $userSuccess->success->xp_reward;
        }

        $user->userSuccesses()->where('is_claimed', false)->update([
            'is_claimed' => true,
            'claimed_at' => now()
        ]);

        $user->cash += $totalCashReward;
        $user->experience += $totalXpReward;
        $user->save();
        $this->checkLevelUp($user);


        return $unclaimedSuccesses->count();
    }

    public function checkLevelUp(User $user): void
    {
        $experienceNeededForNextLevel = $this->getTotalExperienceForLevel($user->level + 1);

        if ($experienceNeededForNextLevel > 0 && $user->experience >= $experienceNeededForNextLevel) {
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

    public function getSuccessProgress(User $user): array
    {
        $totalSuccesses = Success::count();
        $unlockedSuccesses = $user->successes()->count();
        $claimedSuccesses = $user->userSuccesses()->where('is_claimed', true)->count();
        $unclaimedSuccesses = $user->userSuccesses()->where('is_claimed', false)->count();

        return [
            'total' => $totalSuccesses,
            'unlocked' => $unlockedSuccesses,
            'claimed' => $claimedSuccesses,
            'unclaimed' => $unclaimedSuccesses,
            'percentage' => $totalSuccesses > 0 ? round(($unlockedSuccesses / $totalSuccesses) * 100, 2) : 0
        ];
    }

    private function checkPokedexRequirements(User $user, array $requirements): bool
    {
        $pokedexCount = $this->getPokedexCount($user, $requirements);
        return $pokedexCount >= ($requirements['count'] ?? 0);
    }

    private function checkCaptureRequirements(User $user, array $requirements): bool
    {
        $captureCount = $this->getCaptureCount($user, $requirements);
        return $captureCount >= ($requirements['count'] ?? 0);
    }

    private function checkRarityRequirements(User $user, array $requirements): bool
    {
        $rarityCount = $this->getRarityCount($user, $requirements);
        return $rarityCount >= ($requirements['count'] ?? 0);
    }

    private function getPokedexCount(User $user, array $requirements): int
    {
        $query = $user->pokedex();

        if (isset($requirements['shiny'])) {
            $query->whereHas('pokemon', function ($q) use ($requirements) {
                $q->where('is_shiny', $requirements['shiny']);
            });
        }

        if (isset($requirements['rarity'])) {
            $query->whereHas('pokemon', function ($q) use ($requirements) {
                $q->where('rarity', $requirements['rarity']);
            });
        }

        return $query->distinct('pokemon_id')->count();
    }

    private function getCaptureCount(User $user, array $requirements): int
    {
        $query = $user->pokedex();

        if (isset($requirements['shiny'])) {
            $query->whereHas('pokemon', function ($q) use ($requirements) {
                $q->where('is_shiny', $requirements['shiny']);
            });
        }

        if (isset($requirements['rarity'])) {
            $query->whereHas('pokemon', function ($q) use ($requirements) {
                $q->where('rarity', $requirements['rarity']);
            });
        }

        return $query->count();
    }

    private function getRarityCount(User $user, array $requirements): int
    {
        $query = $user->pokedex()->whereHas('pokemon', function ($q) use ($requirements) {
            $q->where('rarity', $requirements['rarity']);

            if (isset($requirements['shiny'])) {
                $q->where('is_shiny', $requirements['shiny']);
            }
        });

        return $query->count();
    }

    private function userHasSuccess(User $user, Success $success): bool
    {
        return $user->successes()->where('success_id', $success->id)->exists();
    }

    private function unlockSuccess(User $user, Success $success)
    {
        UserSuccess::create([
            'user_id' => $user->id,
            'success_id' => $success->id,
            'unlocked_at' => now(),
            'is_claimed' => false,
            'claimed_at' => null
        ]);
    }
}
