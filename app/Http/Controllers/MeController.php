<?php

namespace App\Http\Controllers;

use App\Services\LeaderboardService;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class MeController extends Controller
{
    protected LeaderboardService $leaderboardService;

    public function __construct(LeaderboardService $leaderboardService)
    {
        $this->leaderboardService = $leaderboardService;
    }

    public function index()
    {
        $user = Auth::user();
        $pokedex = $user->pokedex()->with('pokemon')->get();
        $inventory = $user->inventory()->with('item')->get();
        $leaderboards = $this->leaderboardService->getAllLeaderboards($user, 100);

        return Inertia::render('Me', [
            'user' => $user,
            'pokedex' => $pokedex,
            'inventory' => $inventory,
            'leaderboards' => $leaderboards,
        ]);
    }
}
