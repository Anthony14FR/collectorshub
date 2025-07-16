<?php

namespace App\Http\Controllers;

use App\Models\Pokemon;
use App\Models\Success;
use App\Models\User;
use App\Services\LeaderboardService;
use App\Services\SuccessService;
use Inertia\Inertia;

class UserProfileController extends Controller
{
    protected LeaderboardService $leaderboardService;
    protected SuccessService $successService;

    public function __construct(LeaderboardService $leaderboardService, SuccessService $successService)
    {
        $this->leaderboardService = $leaderboardService;
        $this->successService = $successService;
    }

    public function show(User $user)
    {
        $pokedex = $user->pokedex()->with('pokemon')->get();
        $allPokemons = Pokemon::all();
        $claimedSuccesses = $user->claimedSuccesses;

        $leaderboards = $this->leaderboardService->getAllLeaderboards($user, 100);

        if (!isset($leaderboards['team_cp'])) {
            $leaderboards['team_cp'] = $this->leaderboardService->getTeamCPLeaderboard($user, 100);
        }

        $userTeam = $pokedex
            ->where('is_in_team', true)
            ->sortBy('team_position')
            ->take(6)
            ->values()
            ->toArray();

        $allSuccesses = Success::all();

        return Inertia::render('UserProfile/Show', [
            'profile_user' => $user,
            'pokedex' => $pokedex,
            'user_team' => $userTeam,
            'leaderboards' => $leaderboards,
            'all_pokemons' => $allPokemons,
            'claimed_successes' => $claimedSuccesses,
            'successes' => $allSuccesses,
            'progress' => [
                'total' => $allSuccesses->count(),
                'claimed' => $claimedSuccesses->count(),
                'unclaimed' => 0,
                'unlocked' => $claimedSuccesses->count(),
                'percentage' => $allSuccesses->count() > 0 ? ($claimedSuccesses->count() / $allSuccesses->count()) * 100 : 0,
            ]
        ]);
    }
}
