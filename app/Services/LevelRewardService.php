<?php

namespace App\Services;

use App\Models\Item;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class LevelRewardService
{
    public function __construct(
        private GameConfigurationService $configService
    ) {
    }

    public function checkAndDistributeRewards(User $user, int $newLevel): array
    {
        $rewards = [];
        $milestones = $this->getMilestonesForLevel($newLevel);
        foreach ($milestones as $milestone) {
            if ($this->shouldGiveReward($user, $milestone)) {
                $reward = $this->distributeReward($user, $milestone);
                if ($reward) {
                    $rewards[] = $reward;
                }
            }
        }
        return $rewards;
    }

    public function distributeSpecificReward(User $user, int $level, string $type): ?array
    {
        $milestones = $this->getMilestonesForLevel($level);
        foreach ($milestones as $milestone) {
            if ($milestone['type'] === $type && $milestone['level'] === $level) {
                if ($this->shouldGiveReward($user, $milestone)) {
                    $reward = $this->distributeReward($user, $milestone);
                    $user->refresh();
                    return $reward;
                }
                break;
            }
        }
        return null;
    }

    public function getMilestonesForLevel(int $level): array
    {
        $milestones = [];
        $levelRewards = $this->configService->getLevelRewards();

        if ($level % 5 === 0) {
            $milestones[] = [
                'type' => 'milestone_5',
                'level' => $level,
                'cash' => $levelRewards['milestone_5']['cash'],
                'pokeballs' => $levelRewards['milestone_5']['pokeballs'],
                'masterballs' => $levelRewards['milestone_5']['masterballs']
            ];
        }
        if ($level % 10 === 0) {
            $milestones[] = [
                'type' => 'milestone_10',
                'level' => $level,
                'cash' => $levelRewards['milestone_10']['cash'],
                'pokeballs' => $levelRewards['milestone_10']['pokeballs'],
                'masterballs' => $levelRewards['milestone_10']['masterballs']
            ];
        }
        if ($level % 25 === 0) {
            $milestones[] = [
                'type' => 'milestone_25',
                'level' => $level,
                'cash' => $levelRewards['milestone_25']['cash'],
                'pokeballs' => $levelRewards['milestone_25']['pokeballs'],
                'masterballs' => $levelRewards['milestone_25']['masterballs']
            ];
        }
        if ($level % 50 === 0) {
            $milestones[] = [
                'type' => 'milestone_50',
                'level' => $level,
                'cash' => $levelRewards['milestone_50']['cash'],
                'pokeballs' => $levelRewards['milestone_50']['pokeballs'],
                'masterballs' => $levelRewards['milestone_50']['masterballs']
            ];
        }
        if ($level % 5 !== 0 && $level % 10 !== 0 && $level % 25 !== 0 && $level % 50 !== 0) {
            $milestones[] = [
                'type' => 'regular_level',
                'level' => $level,
                'cash' => $levelRewards['regular_level']['cash'],
                'pokeballs' => $levelRewards['regular_level']['pokeballs'],
                'masterballs' => $levelRewards['regular_level']['masterballs']
            ];
        }
        return $milestones;
    }

    private function shouldGiveReward(User $user, array $milestone): bool
    {
        $claimedRewards = $user->claimed_level_rewards ?? [];
        $rewardKey = $milestone['type'] . '_' . $milestone['level'];
        return !in_array($rewardKey, $claimedRewards);
    }

    private function distributeReward(User $user, array $milestone): ?array
    {
        return DB::transaction(function () use ($user, $milestone) {
            $reward = [
                'type' => $milestone['type'],
                'level' => $milestone['level'],
                'cash' => $milestone['cash'],
                'pokeballs' => $milestone['pokeballs'],
                'masterballs' => $milestone['masterballs']
            ];
            $user->cash += $milestone['cash'];
            if ($milestone['pokeballs'] > 0) {
                $this->addItemToInventory($user, 'Pokeball', $milestone['pokeballs']);
            }
            if ($milestone['masterballs'] > 0) {
                $this->addItemToInventory($user, 'Masterball', $milestone['masterballs']);
            }
            $claimedRewards = $user->claimed_level_rewards ?? [];
            $rewardKey = $milestone['type'] . '_' . $milestone['level'];
            $claimedRewards[] = $rewardKey;
            $user->claimed_level_rewards = $claimedRewards;
            $user->save();
            return $reward;
        });
    }

    private function addItemToInventory(User $user, string $itemName, int $quantity): void
    {
        $item = Item::where('name', $itemName)->first();
        if (!$item) {
            return;
        }
        $inventory = $user->inventory()->where('item_id', $item->id)->first();
        if ($inventory) {
            $inventory->quantity += $quantity;
            $inventory->save();
        } else {
            $user->inventory()->create([
                'item_id' => $item->id,
                'quantity' => $quantity
            ]);
        }
    }

    public function getAvailableRewards(User $user): array
    {
        $availableRewards = [];
        $claimedRewards = $user->claimed_level_rewards ?? [];

        for ($level = 1; $level <= $user->level; $level++) {
            $milestones = $this->getMilestonesForLevel($level);

            $unclaimed = [];
            foreach ($milestones as $milestone) {
                $rewardKey = $milestone['type'] . '_' . $milestone['level'];
                if (!in_array($rewardKey, $claimedRewards)) {
                    $unclaimed[] = $milestone;
                }
            }

            if (count($unclaimed) === 1) {
                $milestone = $unclaimed[0];
                $availableRewards[] = [
                    'types' => [$milestone['type']],
                    'level' => $milestone['level'],
                    'cash' => $milestone['cash'],
                    'pokeballs' => $milestone['pokeballs'],
                    'masterballs' => $milestone['masterballs'],
                    'is_available' => true
                ];
            } elseif (count($unclaimed) > 1) {
                $availableRewards[] = [
                    'types' => array_column($unclaimed, 'type'),
                    'level' => $level,
                    'cash' => array_sum(array_column($unclaimed, 'cash')),
                    'pokeballs' => array_sum(array_column($unclaimed, 'pokeballs')),
                    'masterballs' => array_sum(array_column($unclaimed, 'masterballs')),
                    'is_available' => true
                ];
            }
        }

        return $availableRewards;
    }

    public function getClaimedRewards(User $user): array
    {
        $claimed = [];
        $claimedRewards = $user->claimed_level_rewards ?? [];
        for ($level = 1; $level <= $user->level; $level++) {
            $milestones = $this->getMilestonesForLevel($level);
            foreach ($milestones as $milestone) {
                $rewardKey = $milestone['type'] . '_' . $milestone['level'];
                if (in_array($rewardKey, $claimedRewards)) {
                    $claimed[] = [
                        'type' => $milestone['type'],
                        'level' => $milestone['level'],
                        'cash' => $milestone['cash'],
                        'pokeballs' => $milestone['pokeballs'],
                        'masterballs' => $milestone['masterballs'],
                        'is_available' => false
                    ];
                }
            }
        }
        return $claimed;
    }


    public function getPreviewRewards(User $user): array
    {
        $currentLevel = $user->level;
        $claimedRewards = $user->claimed_level_rewards ?? [];

        $previousRewards = [];
        for ($level = max(1, $currentLevel - 5); $level < $currentLevel; $level++) {
            $milestones = $this->getMilestonesForLevel($level);
            foreach ($milestones as $milestone) {
                $rewardKey = $milestone['type'] . '_' . $milestone['level'];
                if (in_array($rewardKey, $claimedRewards)) {
                    $previousRewards[] = array_merge($milestone, ['is_claimed' => true]);
                }
            }
        }

        $nextRewards = [];
        for ($level = $currentLevel + 1; $level <= $currentLevel + 5; $level++) {
            $milestones = $this->getMilestonesForLevel($level);
            foreach ($milestones as $milestone) {
                $nextRewards[] = array_merge($milestone, ['is_claimed' => false]);
            }
        }

        return [
            'previous' => $previousRewards,
            'next' => $nextRewards
        ];
    }
}
