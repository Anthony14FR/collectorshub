<?php

namespace App\Http\Controllers;

use App\Services\DailyQuestService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DailyQuestController extends Controller
{
    public function __construct(
        private DailyQuestService $dailyQuestService
    ) {
    }

    public function index()
    {
        $user = Auth::user();
        $quests = $this->dailyQuestService->getUserTodayProgress($user);
        $stats = $this->dailyQuestService->getCompletionStats($user);

        return response()->json([
            'quests' => $quests,
            'stats' => $stats
        ]);
    }

    public function claimQuest(Request $request)
    {
        $request->validate([
            'quest_id' => 'required|integer|exists:daily_quests,id'
        ]);

        $user = Auth::user();
        $result = $this->dailyQuestService->claimQuestReward($user, $request->quest_id);

        return response()->json($result);
    }

    public function claimBonusReward()
    {
        $user = Auth::user();
        $result = $this->dailyQuestService->claimBonusReward($user);

        return response()->json($result);
    }
}
