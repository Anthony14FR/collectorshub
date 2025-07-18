<?php

namespace App\Http\Controllers;

use App\Models\Pokemon;
use App\Models\Success;
use App\Services\DailyQuestService;
use App\Services\ExpeditionService;
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
    protected DailyQuestService $dailyQuestService;
    protected ExpeditionService $expeditionService;

    public function __construct(
        LeaderboardService $leaderboardService,
        SuccessService $successService,
        LevelRewardService $levelRewardService,
        FriendService $friendService,
        UserFriendGiftService $userFriendGiftService,
        DailyQuestService $dailyQuestService,
        ExpeditionService $expeditionService
    ) {
        $this->leaderboardService = $leaderboardService;
        $this->successService = $successService;
        $this->levelRewardService = $levelRewardService;
        $this->friendService = $friendService;
        $this->userFriendGiftService = $userFriendGiftService;
        $this->dailyQuestService = $dailyQuestService;
        $this->expeditionService = $expeditionService;
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

        $announcements = $user->notifications()->announcements()->limit(10)->get();
        $marketplaceHistory = $user->notifications()->marketplaceHistory()->limit(20)->get();
        $unreadNotificationsCount = $user->notifications()->unread()->count();

        $friendsGiftsToClaim = $user->friendGiftsToClaim()->get();

        $friends = $this->friendService->getFormattedFriends($user, $this->userFriendGiftService);
        $friendRequests = $this->friendService->getFormattedFriendRequests($user);
        $suggestions = $this->friendService->getFormattedFriendSuggestions($user);



        $dailyQuests = $this->dailyQuestService->getUserTodayProgress($user);
        $dailyQuestStats = $this->dailyQuestService->getCompletionStats($user);

        $expeditions = $this->expeditionService->getDailyExpeditions($user);
        $hasCompletedExpeditions = collect($expeditions)->where('can_be_claimed', true)->isNotEmpty();

        $user->resetInfernalTowerDailyDefeats();
        $user->refresh();
        $hasInfernalTowerAttempts = $user->getRawDailyDefeats() > 0;

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
            'announcements' => $announcements,
            'marketplace_history' => $marketplaceHistory,
            'unread_notifications_count' => $unreadNotificationsCount,
            'friend_gifts_to_claim' => $friendsGiftsToClaim,
            'friend_requests' => $friendRequests,
            'friends' => $friends,
            'suggestions' => $suggestions,
            'daily_quests' => $dailyQuests,
            'daily_quest_stats' => $dailyQuestStats,
            'expeditions' => $expeditions,
            'has_completed_expeditions' => $hasCompletedExpeditions,
            'has_infernal_tower_attempts' => $hasInfernalTowerAttempts
        ]);
    }
}
