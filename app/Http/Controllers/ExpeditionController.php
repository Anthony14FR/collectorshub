<?php

namespace App\Http\Controllers;

use App\Models\Expedition;
use App\Services\DailyQuestService;
use App\Services\ExpeditionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ExpeditionController extends Controller
{
    protected $expeditionService;

    public function __construct(
        ExpeditionService $expeditionService,
        DailyQuestService $dailyQuestService
    ) {
        $this->expeditionService = $expeditionService;
        $this->dailyQuestService = $dailyQuestService;
    }

    public function index()
    {
        $user = Auth::user();
        $expeditions = $this->expeditionService->getDailyExpeditions($user);

        return Inertia::render('Expeditions/Index', [
            'expeditions' => $expeditions
        ]);
    }

    public function show(Expedition $expedition)
    {
        $user = Auth::user();
        $eligiblePokemons = $this->expeditionService->getEligiblePokemons($user, $expedition);
        $expeditionStatus = $this->expeditionService->getExpeditionStatus($user, $expedition->id);

        if (request()->expectsJson()) {
            return response()->json([
                'expedition' => $expedition->load('requirements'),
                'eligiblePokemons' => $eligiblePokemons,
                'expeditionStatus' => $expeditionStatus
            ]);
        }

        return Inertia::render('Expeditions/Show', [
            'expedition' => $expedition->load('requirements'),
            'eligiblePokemons' => $eligiblePokemons,
            'expeditionStatus' => $expeditionStatus
        ]);
    }

    public function start(Request $request, Expedition $expedition)
    {
        $request->validate([
            'pokemon_ids' => 'required|array|min:1',
            'pokemon_ids.*' => 'integer|exists:pokedex,id'
        ]);

        $user = Auth::user();

        try {
            $result = $this->expeditionService->startExpeditionWithDetails($user, $expedition, $request->pokemon_ids);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message']
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du démarrage : ' . $e->getMessage()
            ], 500);
        }
    }

    public function claim(Request $request)
    {
        $request->validate([
            'user_expedition_id' => 'required|integer|exists:user_expeditions,id'
        ]);

        $user = Auth::user();

        try {
            $result = $this->expeditionService->claimExpedition($user, $request->user_expedition_id);

            if ($result['success']) {
                $this->dailyQuestService->incrementQuestProgress($user, 'complete_expedition');
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'rewards' => $result['rewards']
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la réclamation : ' . $e->getMessage()
            ], 500);
        }
    }
}
