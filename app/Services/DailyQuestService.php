<?php

namespace App\Services;

use App\Models\DailyQuest;
use App\Models\Item;
use App\Models\User;
use App\Models\UserDailyQuestProgress;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DailyQuestService
{
    public function __construct(
        private SuccessService $successService
    ) {
    }

    public function getUserTodayProgress(User $user): Collection
    {
        $quests = DailyQuest::active()->ordered()->get();
        $today = today();

        return $quests->map(function ($quest) use ($user, $today) {
            $progress = UserDailyQuestProgress::where('user_id', $user->id)
                ->where('daily_quest_id', $quest->id)
                ->where('date', $today)
                ->first();

            return [
                'id' => $quest->id,
                'key' => $quest->key,
                'name' => $quest->name,
                'description' => $quest->description,
                'target_count' => $quest->target_count,
                'rewards' => $quest->rewards,
                'current_count' => $progress ? $progress->current_count : 0,
                'is_completed' => $progress ? $progress->is_completed : false,
                'is_claimed' => $progress ? $progress->is_claimed : false,
                'sort_order' => $quest->sort_order
            ];
        });
    }

    public function incrementQuestProgress(User $user, string $questKey, int $count = 1): void
    {
        $quest = DailyQuest::where('key', $questKey)->where('is_active', true)->first();

        if (!$quest) {
            return;
        }

        $today = today();

        $progress = UserDailyQuestProgress::firstOrCreate([
            'user_id' => $user->id,
            'daily_quest_id' => $quest->id,
            'date' => $today
        ], [
            'current_count' => 0,
            'is_completed' => false,
            'is_claimed' => false
        ]);

        if ($progress->is_completed) {
            return;
        }

        $progress->current_count += $count;

        if ($progress->current_count >= $quest->target_count) {
            $progress->current_count = $quest->target_count;
            $progress->is_completed = true;
            $progress->completed_at = now();
        }

        $progress->save();
    }

    public function claimQuestReward(User $user, int $questId): array
    {
        return DB::transaction(function () use ($user, $questId) {
            $progress = UserDailyQuestProgress::where('user_id', $user->id)
                ->where('daily_quest_id', $questId)
                ->where('date', today())
                ->with('dailyQuest')
                ->first();

            if (!$progress || !$progress->canBeClaimed()) {
                return ['success' => false, 'message' => 'Quête non disponible pour réclamation'];
            }

            $quest = $progress->dailyQuest;
            $rewards = $quest->rewards;

            if (isset($rewards['xp']) && $rewards['xp'] > 0) {
                $user->addXp($rewards['xp']);
            }

            if (isset($rewards['cash']) && $rewards['cash'] > 0) {
                $user->cash += $rewards['cash'];
            }

            if (isset($rewards['pokeballs']) && $rewards['pokeballs'] > 0) {
                $this->addItemToInventory($user, 'Pokeball', $rewards['pokeballs']);
            }

            if (isset($rewards['masterballs']) && $rewards['masterballs'] > 0) {
                $this->addItemToInventory($user, 'Masterball', $rewards['masterballs']);
            }

            $user->save();

            $progress->is_claimed = true;
            $progress->claimed_at = now();
            $progress->save();

            $this->successService->checkAndUnlockSuccesses($user);

            return [
                'success' => true,
                'message' => 'Récompense réclamée avec succès !',
                'rewards' => $rewards
            ];
        });
    }

    public function canClaimBonusReward(User $user): bool
    {
        if (!$user->canClaimDailyBonus()) {
            return false;
        }

        $today = today();
        $totalQuests = DailyQuest::active()->count();
        $completedQuests = UserDailyQuestProgress::where('user_id', $user->id)
            ->where('date', $today)
            ->where('is_claimed', true)
            ->count();

        return $completedQuests >= $totalQuests;
    }

    public function claimBonusReward(User $user): array
    {
        if (!$this->canClaimBonusReward($user)) {
            return ['success' => false, 'message' => 'Bonus non disponible'];
        }

        return DB::transaction(function () use ($user) {
            $user->addXp(2500);
            $user->cash += 3000;
            $user->daily_quest_bonus_claimed_date = today();
            $user->save();

            $this->addItemToInventory($user, 'Pokeball', 10);

            $this->successService->checkAndUnlockSuccesses($user);

            return [
                'success' => true,
                'message' => 'Bonus quotidien réclamé avec succès !',
                'rewards' => [
                    'xp' => 2500,
                    'cash' => 3000,
                    'pokeballs' => 10
                ]
            ];
        });
    }

    public function getCompletionStats(User $user): array
    {
        $today = today();
        $totalQuests = DailyQuest::active()->count();

        $userProgress = UserDailyQuestProgress::where('user_id', $user->id)
            ->where('date', $today)
            ->get();

        $completedCount = $userProgress->where('is_completed', true)->count();
        $claimedCount = $userProgress->where('is_claimed', true)->count();

        return [
            'total' => $totalQuests,
            'completed' => $completedCount,
            'claimed' => $claimedCount,
            'can_claim_bonus' => $this->canClaimBonusReward($user),
            'completion_percentage' => $totalQuests > 0 ? ($completedCount / $totalQuests) * 100 : 0
        ];
    }

    private function addItemToInventory(User $user, string $itemName, int $quantity): void
    {
        $item = Item::where('name', $itemName)->first();

        if ($item) {
            $inventory = $user->inventory()->where('item_id', $item->id)->first();

            if ($inventory) {
                $inventory->increment('quantity', $quantity);
            } else {
                $user->inventory()->create([
                    'item_id' => $item->id,
                    'quantity' => $quantity
                ]);
            }
        }
    }
}
