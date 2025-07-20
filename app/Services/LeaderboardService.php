<?php

namespace App\Services;

use App\Models\User;

class LeaderboardService
{
    public function getCashLeaderboard(User $currentUser, $limit = 100): array
    {
        $query = User::where('status', 'active')
            ->orderBy('cash', 'desc');

        if ($limit !== 'all') {
            $query->limit($limit);
        }

        $topUsers = $query->get(['id', 'username', 'cash', 'level', 'avatar', 'background'])
            ->map(function ($user, $index) {
                return [
                    'rank' => $index + 1,
                    'id' => $user->id,
                    'username' => $user->username,
                    'value' => $user->cash,
                    'level' => $user->level,
                    'avatar' => $user->avatar,
                    'background' => $user->background
                ];
            });

        if ($limit === 'all') {
            return ['top' => $topUsers];
        }

        $userRank = $this->getUserRankByCash($currentUser);

        return [
            'top' => $topUsers,
            'current_user' => $userRank
        ];
    }

    public function getExperienceLeaderboard(User $currentUser, $limit = 100): array
    {
        $query = User::where('status', 'active')
            ->orderBy('level', 'desc')
            ->orderBy('experience', 'desc');

        if ($limit !== 'all') {
            $query->limit($limit);
        }

        $topUsers = $query->get(['id', 'username', 'experience', 'level', 'avatar', 'background'])
            ->map(function ($user, $index) {
                return [
                    'rank' => $index + 1,
                    'id' => $user->id,
                    'username' => $user->username,
                    'value' => $user->level,
                    'experience' => $user->experience,
                    'level' => $user->level,
                    'avatar' => $user->avatar,
                    'background' => $user->background
                ];
            });

        if ($limit === 'all') {
            return ['top' => $topUsers];
        }

        $userRank = $this->getUserRankByLevel($currentUser);

        return [
            'top' => $topUsers,
            'current_user' => $userRank
        ];
    }

    private function getUserRankByLevel(User $user): array
    {
        $rank = User::where('status', 'active')
            ->where(function ($query) use ($user) {
                $query->where('level', '>', $user->level)
                    ->orWhere(function ($q) use ($user) {
                        $q->where('level', '=', $user->level)
                            ->where('experience', '>', $user->experience);
                    });
            })
            ->count() + 1;

        return [
            'rank' => $rank,
            'id' => $user->id,
            'username' => $user->username,
            'value' => $user->level,
            'level' => $user->level,
            'avatar' => $user->avatar,
            'background' => $user->background
        ];
    }

    public function getPokemonCountLeaderboard(User $currentUser, $limit = 100): array
    {
        $query = User::where('status', 'active')
            ->withCount('pokedex')
            ->orderBy('pokedex_count', 'desc');

        if ($limit !== 'all') {
            $query->limit($limit);
        }

        $topUsers = $query->get(['id', 'username', 'level', 'pokedex_count', 'avatar', 'background'])
            ->map(function ($user, $index) {
                return [
                    'rank' => $index + 1,
                    'id' => $user->id,
                    'username' => $user->username,
                    'value' => $user->pokedex_count,
                    'level' => $user->level,
                    'avatar' => $user->avatar,
                    'background' => $user->background
                ];
            });

        if ($limit === 'all') {
            return ['top' => $topUsers];
        }

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
            'level' => $user->level,
            'avatar' => $user->avatar,
            'background' => $user->background
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
            'level' => $user->level,
            'avatar' => $user->avatar,
            'background' => $user->background
        ];
    }

    public function getAllLeaderboards(User $currentUser, $limit = 100): array
    {
        return [
            'cash' => $this->getCashLeaderboard($currentUser, $limit),
            'experience' => $this->getExperienceLeaderboard($currentUser, $limit),
            'pokemon_count' => $this->getPokemonCountLeaderboard($currentUser, $limit)
        ];
    }

    public function getTeamCPLeaderboard(User $currentUser, $limit = 100): array
    {
        $query = User::where('status', 'active')
            ->with(['pokedex' => function ($q) {
                $q->where('is_in_team', true)
                ->with(['pokemon'])
                ->orderBy('team_position');
            }])
            ->get(['id', 'username', 'level', 'avatar', 'background'])
            ->map(function ($user) {
                $teamCP = $user->pokedex->sum('cp');
                return [
                    'id' => $user->id,
                    'username' => $user->username,
                    'level' => $user->level,
                    'avatar' => $user->avatar,
                    'background' => $user->background,
                    'team_cp' => $teamCP,
                    'team_pokemons' => $user->pokedex->values()->toArray()
                ];
            })
            ->sortByDesc('team_cp')
            ->values();

        if ($limit !== 'all') {
            $topUsers = $query->take($limit)->map(function ($user, $index) {
                return array_merge($user, ['rank' => $index + 1]);
            });
        } else {
            $topUsers = $query->map(function ($user, $index) {
                return array_merge($user, ['rank' => $index + 1]);
            });
        }

        $currentUserData = $query->where('id', $currentUser->id)->first();
        if ($currentUserData) {
            $currentUserRank = $query->search(function ($user) use ($currentUser) {
                return $user['id'] === $currentUser->id;
            }) + 1;

            $userRank = array_merge($currentUserData, ['rank' => $currentUserRank]);
        } else {
            $userTeamCP = $currentUser->pokedex()->where('is_in_team', true)->sum('cp');
            $rank = $query->where('team_cp', '>', $userTeamCP)->count() + 1;

            $userRank = [
                'rank' => $rank,
                'id' => $currentUser->id,
                'username' => $currentUser->username,
                'value' => $userTeamCP,
                'level' => $currentUser->level,
                'avatar' => $currentUser->avatar,
                'background' => $currentUser->background,
                'team_cp' => $userTeamCP
            ];
        }

        return [
            'top' => $topUsers,
            'current_user' => $userRank
        ];
    }

    public function getClubLeaderboard(User $currentUser, $limit = 100): array
    {
        $query = \App\Models\Club::with(['leader'])
            ->orderBy('total_cp', 'desc');

        if ($limit !== 'all') {
            $query->limit($limit);
        }

        $topClubs = $query->get(['id', 'name', 'leader_id', 'total_cp', 'icon'])
            ->map(function ($club, $index) {
                return [
                    'rank' => $index + 1,
                    'id' => $club->id,
                    'username' => $club->name,
                    'name' => $club->name,
                    'value' => $club->total_cp,
                    'total_cp' => $club->total_cp,
                    'level' => $club->leader ? $club->leader->level : 1,
                    'avatar' => $club->leader ? $club->leader->avatar : null,
                    'background' => $club->leader ? $club->leader->background : null,
                    'leader_id' => $club->leader_id,
                    'icon' => $club->icon
                ];
            });

        if ($limit === 'all') {
            return ['top' => $topClubs];
        }

        $userClub = $currentUser->club()->first();
        $userRank = null;

        if ($userClub) {
            $rank = \App\Models\Club::where('total_cp', '>', $userClub->total_cp)->count() + 1;

            $userRank = [
                'rank' => $rank,
                'id' => $userClub->id,
                'username' => $userClub->name,
                'name' => $userClub->name,
                'value' => $userClub->total_cp,
                'total_cp' => $userClub->total_cp,
                'level' => $userClub->leader ? $userClub->leader->level : 1,
                'avatar' => $userClub->leader ? $userClub->leader->avatar : null,
                'background' => $userClub->leader ? $userClub->leader->background : null,
                'leader_id' => $userClub->leader_id,
                'icon' => $userClub->icon
            ];
        }

        return [
            'top' => $topClubs,
            'current_user' => $userRank
        ];
    }

    public function getInfernalTowerLeaderboard(User $currentUser, $limit = 100): array
    {
        $query = User::where('status', 'active')
            ->orderBy('infernal_tower_current_level', 'desc');

        if ($limit !== 'all') {
            $query->limit($limit);
        }

        $topUsers = $query->get(['id', 'username', 'level', 'avatar', 'background', 'infernal_tower_current_level'])
            ->map(function ($user, $index) {
                $towerLevel = \App\Models\InfernalTowerLevel::where('level', $user->infernal_tower_current_level)->first();

                return [
                    'rank' => $index + 1,
                    'id' => $user->id,
                    'username' => $user->username,
                    'value' => $user->infernal_tower_current_level,
                    'level' => $user->level,
                    'avatar' => $user->avatar,
                    'background' => $user->background,
                    'tower_level' => $user->infernal_tower_current_level,
                    'enemy_team' => $towerLevel ? $towerLevel->team : [],
                    'enemy_team_cp' => $towerLevel ? $towerLevel->team_cp : 0,
                ];
            });

        if ($limit === 'all') {
            return ['top' => $topUsers];
        }

        $userRank = $this->getUserRankByInfernalTowerLevel($currentUser);

        return [
            'top' => $topUsers,
            'current_user' => $userRank
        ];
    }

    private function getUserRankByInfernalTowerLevel(User $user): array
    {
        $rank = User::where('status', 'active')
            ->where('infernal_tower_current_level', '>', $user->infernal_tower_current_level)
            ->count() + 1;

        $towerLevel = \App\Models\InfernalTowerLevel::where('level', $user->infernal_tower_current_level)->first();

        return [
            'rank' => $rank,
            'id' => $user->id,
            'username' => $user->username,
            'value' => $user->infernal_tower_current_level,
            'level' => $user->level,
            'avatar' => $user->avatar,
            'background' => $user->background,
            'tower_level' => $user->infernal_tower_current_level,
            'enemy_team' => $towerLevel ? $towerLevel->team : [],
            'enemy_team_cp' => $towerLevel ? $towerLevel->team_cp : 0,
        ];
    }
}
