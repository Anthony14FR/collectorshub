<?php

namespace App\Http\Controllers;

use App\Services\LevelRewardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LevelRewardController extends Controller
{
    protected LevelRewardService $levelRewardService;

    public function __construct(LevelRewardService $levelRewardService)
    {
        $this->levelRewardService = $levelRewardService;
    }

    public function claim(Request $request)
    {
        $request->validate([
            'level' => 'required|integer|min:1',
            'type' => 'required|string'
        ]);

        $user = Auth::user();
        $level = $request->level;

        if ($user->level < $level) {
            return back()->withErrors([
                'message' => 'Vous devez atteindre le niveau ' . $level . ' pour réclamer cette récompense'
            ]);
        }

        $claimedRewards = $user->claimed_level_rewards ?? [];
        $milestones = $this->levelRewardService->getMilestonesForLevel($level);

        $alreadyClaimed = true;
        $claimedAny = false;

        foreach ($milestones as $milestone) {
            $rewardKey = $milestone['type'] . '_' . $milestone['level'];
            if (!in_array($rewardKey, $claimedRewards)) {
                $reward = $this->levelRewardService->distributeSpecificReward($user, $level, $milestone['type']);
                if ($reward) {
                    $claimedAny = true;
                }
                $alreadyClaimed = false;
            }
        }

        $user->refresh();
        Auth::setUser($user);

        if ($alreadyClaimed) {
            return back()->withErrors([
                'message' => 'Cette récompense a déjà été réclamée'
            ]);
        }

        if (!$claimedAny) {
            return back()->withErrors([
                'message' => 'Impossible de distribuer la récompense'
            ]);
        }

        return back();
    }

    public function claimAll()
    {
        $user = Auth::user();
        $availableRewards = $this->levelRewardService->getAvailableRewards($user);
        foreach ($availableRewards as $reward) {
            $milestones = $this->levelRewardService->getMilestonesForLevel($reward['level']);
            foreach ($milestones as $milestone) {
                $this->levelRewardService->distributeSpecificReward($user, $reward['level'], $milestone['type']);
            }
        }
        $user->refresh();
        Auth::setUser($user);
        return back();
    }
}