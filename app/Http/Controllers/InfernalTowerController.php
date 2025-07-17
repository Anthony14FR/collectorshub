<?php

namespace App\Http\Controllers;

use App\Services\DailyQuestService;
use App\Services\InfernalTowerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class InfernalTowerController extends Controller
{
    protected $infernalTowerService;

    public function __construct(
        InfernalTowerService $infernalTowerService,
        DailyQuestService $dailyQuestService
    ) {
        $this->infernalTowerService = $infernalTowerService;
        $this->dailyQuestService = $dailyQuestService;
    }

    public function index()
    {
        $user = Auth::user();
        $user->resetInfernalTowerDailyDefeats();
        $user->refresh();

        $pokedex = $user->pokedex()->with('pokemon')->get();
        $teamPokemons = $pokedex->where('is_in_team', true)->sortBy('team_position')->values();
        $data = $this->infernalTowerService->getTowerLevels($user);

        return Inertia::render('InfernalTower/Index', [
            'user' => $user,
            'pokedex' => $pokedex,
            'teamPokemons' => $teamPokemons,
            'currentLevel' => $data['current_level'],
            'levels' => $data['levels'],
            'dailyDefeats' => $user->getRawDailyDefeats(),
            'userTeamCp' => $data['user_team_cp'],
        ]);
    }

    public function attempt(Request $request)
    {
        $request->validate([
            'level' => 'required|integer|min:1',
        ]);

        $user = Auth::user();

        if ($request->level < $user->infernal_tower_current_level) {
            return response()->json([
                'success' => false,
                'message' => 'Vous avez déjà vaincu ce niveau !'
            ]);
        }

        $result = $this->infernalTowerService->attemptLevel($user, $request->level);

        $this->dailyQuestService->incrementQuestProgress($user, 'use_tower_attempts');

        return response()->json($result);
    }
}
