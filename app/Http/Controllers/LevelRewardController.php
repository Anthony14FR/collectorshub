<?php

namespace App\Http\Controllers;

use App\Services\LevelRewardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class LevelRewardController extends Controller
{
    protected LevelRewardService $levelRewardService;

    public function __construct(LevelRewardService $levelRewardService)
    {
        $this->levelRewardService = $levelRewardService;
    }

    public function index()
    {
        $user = Auth::user();
        
        $availableRewards = $this->levelRewardService->getAvailableRewards($user);
        $claimedRewards = $this->levelRewardService->getClaimedRewards($user);
        $nextRewards = $this->levelRewardService->getNextRewards($user);

        return Inertia::render('LevelRewards/Index', [
            'user' => $user,
            'availableRewards' => $availableRewards,
            'claimedRewards' => $claimedRewards,
            'nextRewards' => $nextRewards,
        ]);
    }

    public function claim(Request $request)
    {
        $request->validate([
            'level' => 'required|integer|min:1',
            'type' => 'required|string'
        ]);

        $user = Auth::user();
        $level = $request->level;
        $type = $request->type;

        if ($user->level < $level) {
            return back()->withErrors([
                'message' => 'Vous devez atteindre le niveau ' . $level . ' pour réclamer cette récompense'
            ]);
        }

        $claimedRewards = $user->claimed_level_rewards ?? [];
        $rewardKey = $type . '_' . $level;
        
        if (in_array($rewardKey, $claimedRewards)) {
            return back()->withErrors([
                'message' => 'Cette récompense a déjà été réclamée'
            ]);
        }

        $milestones = $this->levelRewardService->getMilestonesForLevel($level);
        $milestone = null;
        
        foreach ($milestones as $m) {
            if ($m['type'] === $type && $m['level'] === $level) {
                $milestone = $m;
                break;
            }
        }

        if (!$milestone) {
            return back()->withErrors([
                'message' => 'Récompense invalide'
            ]);
        }

        $reward = $this->levelRewardService->distributeSpecificReward($user, $level, $type);

        if (!$reward) {
            return back()->withErrors([
                'message' => 'Impossible de distribuer la récompense'
            ]);
        }

        $user->refresh();
        
        Auth::setUser($user);

        $message = 'Récompense réclamée avec succès ! Vous avez reçu ' . $reward['cash'] . '$';
        if ($reward['pokeballs'] > 0) {
            $message .= ' et ' . $reward['pokeballs'] . ' Pokeballs';
        }
        if ($reward['masterballs'] > 0) {
            $message .= ' et ' . $reward['masterballs'] . ' Masterballs';
        }
        $message .= ' !';

        return back()->with('success', $message);
    }

    public function getAvailableRewards()
    {
        $user = Auth::user();
        $availableRewards = $this->levelRewardService->getAvailableRewards($user);

        return response()->json([
            'success' => true,
            'rewards' => $availableRewards
        ]);
    }

    public function getNextRewards()
    {
        $user = Auth::user();
        $nextRewards = $this->levelRewardService->getNextRewards($user);

        return response()->json([
            'success' => true,
            'rewards' => $nextRewards
        ]);
    }
}
