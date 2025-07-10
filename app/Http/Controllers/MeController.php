<?php

namespace App\Http\Controllers;

use App\Models\Pokemon;
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
        $all_pokemons = Pokemon::all();

        return Inertia::render('Me', [
            'user' => $user,
            'pokedex' => $pokedex,
            'all_pokemons' => $all_pokemons,
            'inventory' => $inventory,
            'leaderboards' => $leaderboards,
        ]);
    }
}
