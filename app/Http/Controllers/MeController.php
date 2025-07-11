<?php

namespace App\Http\Controllers;

use App\Models\Pokemon;
use App\Models\Success;
use App\Services\LeaderboardService;
use App\Services\SuccessService;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class MeController extends Controller
{
    protected LeaderboardService $leaderboardService;
    protected SuccessService $successService;

    public function __construct(LeaderboardService $leaderboardService, SuccessService $successService)
    {
        $this->leaderboardService = $leaderboardService;
        $this->successService = $successService;
    }

    public function index()
    {
        $user = Auth::user();
        $pokedex = $user->pokedex()->with('pokemon')->get();
        $inventory = $user->inventory()->with('item')->get();
        $leaderboards = $this->leaderboardService->getAllLeaderboards($user, 100);
        $allPokemons = Pokemon::all();

        $unclaimedSuccesses = $user->unclaimedSuccesses;
        $claimedSuccesses = $user->claimedSuccesses;
        $otherSuccesses = Success::whereNotIn('id', $user->userSuccesses()->pluck('success_id'))->get();
        $progress = $this->successService->getSuccessProgress($user);

        return Inertia::render('Me', [
            'user' => $user,
            'pokedex' => $pokedex,
            'all_pokemons' => $allPokemons,
            'inventory' => $inventory,
            'leaderboards' => $leaderboards,
            'successes' => $otherSuccesses,
            'unclaimed_successes' => $unclaimedSuccesses,
            'claimed_successes' => $claimedSuccesses,
            'progress' => $progress,
        ]);
    }
}
