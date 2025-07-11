<?php

namespace App\Services;

use App\Models\Success;
use App\Models\User;
use App\Models\UserSuccess;
use Illuminate\Support\Facades\DB;

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
            $query->where('is_shiny', $requirements['shiny']);
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
        return UserSuccess::where('user_id', $user->id)
                              ->where('success_id', $success->id)
                              ->exists();
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

        event(new \App\Events\SuccessUnlocked($user, $success));
    }
}