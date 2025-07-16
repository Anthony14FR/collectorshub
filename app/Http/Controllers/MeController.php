<?php

namespace App\Http\Controllers;

use App\Models\Pokemon;
use App\Models\Success;
use App\Services\FriendService;
use App\Services\LeaderboardService;
use App\Services\LevelRewardService;
use App\Services\SuccessService;
use App\Services\UserFriendGiftService;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class MeController extends Controller
{
    protected LeaderboardService $leaderboardService;
    protected SuccessService $successService;
    protected LevelRewardService $levelRewardService;
    protected FriendService $friendService;
    protected UserFriendGiftService $userFriendGiftService;

    public function __construct(LeaderboardService $leaderboardService, SuccessService $successService, LevelRewardService $levelRewardService, FriendService $friendService, UserFriendGiftService $userFriendGiftService)
    {
        $this->leaderboardService = $leaderboardService;
        $this->successService = $successService;
        $this->levelRewardService = $levelRewardService;
        $this->friendService = $friendService;
        $this->userFriendGiftService = $userFriendGiftService;
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

        $levelRewardsToClaim = $this->levelRewardService->getAvailableRewards($user);
        $levelRewardsPreview = $this->levelRewardService->getPreviewRewards($user);

        $friendsGiftsToClaim = $user->friendGiftsToClaim()->get();

        $userFriendGiftService = $this->userFriendGiftService;

        $friends = $user->friends()->get()->map(function ($friend) use ($user, $userFriendGiftService) {
            $hasSentGiftToday = $userFriendGiftService->hasSentToday($user, $friend);
            $gift = $friend->userFriendGiftsSent()
                ->where('receiver_id', $user->id)
                ->where('is_claimed', false)
                ->first();
            $hasGiftToClaim = $gift !== null;
            $giftId = $gift ? $gift->id : null;
            return [
                'id' => $friend->id,
                'username' => $friend->username,
                'level' => $friend->level,
                'hasSentGiftToday' => $hasSentGiftToday,
                'hasGiftToClaim' => $hasGiftToClaim,
                'giftId' => $giftId,
            ];
        });

        $friendRequests = $user->friendRequests()->with('user')->get()->map(function ($req) {
            return [
                'id' => $req->id,
                'user' => [
                    'id' => $req->user->id,
                    'username' => $req->user->username,
                    'email' => $req->user->email,
                    'level' => $req->user->level,
                ],
            ];
        });

        $suggestions = $this->friendService->getFriendSuggestions($user);

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
            'level_rewards_to_claim' => $levelRewardsToClaim,
            'level_rewards_preview' => $levelRewardsPreview,
            'friend_gifts_to_claim' => $friendsGiftsToClaim,
            'friend_requests' => $friendRequests,
            'friends' => $friends,
            'suggestions' => $suggestions
        ]);
    }
}
