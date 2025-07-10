<?php

namespace App\Services;

use App\Models\User;

class LeaderboardService
{
    public function getCashLeaderboard(User $currentUser): array
    {
        $topUsers = User::where('status', 'active')
            ->orderBy('cash', 'desc')
            ->limit(100)
            ->get(['id', 'username', 'cash', 'level'])
            ->map(function ($user, $index) {
                return [
                    'rank' => $index + 1,
                    'id' => $user->id,
                    'username' => $user->username,
                    'value' => $user->cash,
                    'level' => $user->level
                ];
            });

        $userRank = $this->getUserRankByCash($currentUser);

        return [
            'top' => $topUsers,
            'current_user' => $userRank
        ];
    }

    public function getExperienceLeaderboard(User $currentUser): array
    {
        $topUsers = User::where('status', 'active')
            ->orderBy('experience', 'desc')
            ->limit(100)
            ->get(['id', 'username', 'experience', 'level'])
            ->map(function ($user, $index) {
                return [
                    'rank' => $index + 1,
                    'id' => $user->id,
                    'username' => $user->username,
                    'value' => $user->experience,
                    'level' => $user->level
                ];
            });

        $userRank = $this->getUserRankByExperience($currentUser);

        return [
            'top' => $topUsers,
            'current_user' => $userRank
        ];
    }

    public function getPokemonCountLeaderboard(User $currentUser): array
    {
        $topUsers = User::where('status', 'active')
            ->withCount('pokedex')
            ->orderBy('pokedex_count', 'desc')
            ->limit(100)
            ->get(['id', 'username', 'level', 'pokedex_count'])
            ->map(function ($user, $index) {
                return [
                    'rank' => $index + 1,
                    'id' => $user->id,
                    'username' => $user->username,
                    'value' => $user->pokedex_count,
                    'level' => $user->level
                ];
            });

        $userRank = $this->getUserRankByPokemonCount($currentUser);

        return [
            'top' => $topUsers,
            'current_user' => $userRank
        ];
    }

    private function getUserRankByCash(User $user): array
    {
        $rank = User::where('status', 'active')
            ->where('cash', '>', $user->cash)
            ->count() + 1;

        return [
            'rank' => $rank,
            'id' => $user->id,
            'username' => $user->username,
            'value' => $user->cash,
            'level' => $user->level
        ];
    }

    private function getUserRankByExperience(User $user): array
    {
        $rank = User::where('status', 'active')
            ->where('experience', '>', $user->experience)
            ->count() + 1;

        return [
            'rank' => $rank,
            'id' => $user->id,
            'username' => $user->username,
            'value' => $user->experience,
            'level' => $user->level
        ];
    }

    private function getUserRankByPokemonCount(User $user): array
    {
        $userPokemonCount = $user->pokedex()->count();

        $usersWithCounts = User::where('status', 'active')
            ->withCount('pokedex')
            ->get(['id', 'pokedex_count']);

        $rank = $usersWithCounts->where('pokedex_count', '>', $userPokemonCount)->count() + 1;

        return [
            'rank' => $rank,
            'id' => $user->id,
            'username' => $user->username,
            'value' => $userPokemonCount,
            'level' => $user->level
        ];
    }

    public function getAllLeaderboards(User $currentUser): array
    {
        return [
            'cash' => $this->getCashLeaderboard($currentUser),
            'experience' => $this->getExperienceLeaderboard($currentUser),
            'pokemon_count' => $this->getPokemonCountLeaderboard($currentUser)
        ];
    }
}
