<?php

namespace App\Http\Controllers;

use App\Services\LeaderboardService;
use Inertia\Inertia;

class LeaderboardController extends Controller
{
    protected $leaderboardService;

    public function __construct(LeaderboardService $leaderboardService)
    {
        $this->leaderboardService = $leaderboardService;
    }

    public function index()
    {
        $user = auth()->user();

        $levelLeaderboard = $this->leaderboardService->getExperienceLeaderboard($user, 100);
        $cpLeaderboard = $this->leaderboardService->getTeamCPLeaderboard($user, 100);
        $pokemonLeaderboard = $this->leaderboardService->getPokemonCountLeaderboard($user, 100);
        $clubLeaderboard = $this->leaderboardService->getClubLeaderboard($user, 100);
        $infernalTowerLeaderboard = $this->leaderboardService->getInfernalTowerLeaderboard($user, 100);

        return Inertia::render('Leaderboard/Index', [
            'levelLeaderboard' => $levelLeaderboard,
            'cpLeaderboard' => $cpLeaderboard,
            'pokemonLeaderboard' => $pokemonLeaderboard,
            'clubLeaderboard' => $clubLeaderboard,
            'infernalTowerLeaderboard' => $infernalTowerLeaderboard,
        ]);
    }
}
