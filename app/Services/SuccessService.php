<?php

namespace App\Services;

use App\Models\Success;
use App\Models\User;

class SuccessService
{
    protected $xpService;

    public function __construct(XpService $xpService)
    {
        $this->xpService = $xpService;
    }

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
            case 'friend':
                return $this->checkFriendRequirements($user, $requirements);
            case 'tower':
                return $this->checkTowerRequirements($user, $requirements);
            case 'expedition':
                return $this->checkExpeditionRequirements($user, $requirements);
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
                $user->save();
                $user->addXp($success->xp_reward);
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
        $user->save();
        $user->addXp($totalXpReward);

        return $unclaimedSuccesses->count();
    }

    private function userHasSuccess(User $user, Success $success): bool
    {
        return $user->userSuccesses()->where('success_id', $success->id)->exists();
    }

    private function unlockSuccess(User $user, Success $success): void
    {
        $user->userSuccesses()->create([
            'success_id' => $success->id,
            'unlocked_at' => now(),
            'is_claimed' => false,
        ]);
    }

    private function checkPokedexRequirements(User $user, array $requirements): bool
    {
        if (isset($requirements['count'])) {
            $query = $user->pokedex();

            if (isset($requirements['shiny'])) {
                $query->whereHas('pokemon', function ($pokemonQuery) use ($requirements) {
                    $pokemonQuery->where('is_shiny', $requirements['shiny']);
                });
            }

            if (!empty($requirements['distinct'])) {
                $count = $query->distinct('pokemon_id')->count('pokemon_id');
            } else {
                $count = $query->count();
            }
            return $count >= $requirements['count'];
        }

        if (isset($requirements['types'])) {
            $typeCounts = [];
            foreach ($user->pokedex as $pokedexEntry) {
                $pokemon = $pokedexEntry->pokemon;
                if ($pokemon) {
                    $types = $pokemon->types;
                    foreach ($types as $type) {
                        if (!isset($typeCounts[$type])) {
                            $typeCounts[$type] = 0;
                        }
                        $typeCounts[$type]++;
                    }
                }
            }

            foreach ($requirements['types'] as $type => $requiredCount) {
                if (!isset($typeCounts[$type]) || $typeCounts[$type] < $requiredCount) {
                    return false;
                }
            }
            return true;
        }

        return false;
    }

    private function checkCaptureRequirements(User $user, array $requirements): bool
    {
        if (isset($requirements['pokemon_id'])) {
            return $user->pokedex()->where('pokemon_id', $requirements['pokemon_id'])->exists();
        }

        return false;
    }

    private function checkRarityRequirements(User $user, array $requirements): bool
    {
        if (isset($requirements['rarity']) && isset($requirements['count'])) {
            $query = $user->pokedex()
                ->whereHas('pokemon', function ($pokemonQuery) use ($requirements) {
                    $pokemonQuery->where('rarity', $requirements['rarity']);

                    if (isset($requirements['shiny'])) {
                        $pokemonQuery->where('is_shiny', $requirements['shiny']);
                    }
                });

            $count = $query->count();
            return $count >= $requirements['count'];
        }

        return false;
    }

    private function checkFriendRequirements(User $user, array $requirements): bool
    {
        if (isset($requirements['count'])) {
            $friendCount = $user->friends()->count();
            return $friendCount >= $requirements['count'];
        }

        return false;
    }

    private function checkTowerRequirements(User $user, array $requirements): bool
    {
        if (isset($requirements['level'])) {
            $completedLevels = $user->infernal_tower_current_level - 1;
            return $completedLevels >= $requirements['level'];
        }

        return false;
    }

    private function checkExpeditionRequirements(User $user, array $requirements): bool
    {
        if (isset($requirements['count'])) {
            $completedExpeditions = $user->userExpeditions()
                ->where('status', 'completed')
                ->count();
            return $completedExpeditions >= $requirements['count'];
        }

        return false;
    }

    public function getSuccessProgress(User $user): array
    {
        $totalSuccesses = Success::count();
        $unlockedSuccesses = $user->userSuccesses()->count();
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
}
