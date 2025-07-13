<?php

namespace App\Services;

use App\Models\User;
use App\Models\Item;
use App\Models\Inventory;
use Illuminate\Support\Facades\DB;

class LevelRewardService
{
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
        
        if ($level % 5 === 0) {
            $milestones[] = [
                'type' => 'milestone_5',
                'level' => $level,
                'cash' => 1500,
                'pokeballs' => 10,
                'masterballs' => 0
            ];
        }
        
        if ($level % 10 === 0) {
            $milestones[] = [
                'type' => 'milestone_10',
                'level' => $level,
                'cash' => 3000,
                'pokeballs' => 0,
                'masterballs' => 5
            ];
        }
        
        if ($level % 25 === 0) {
            $milestones[] = [
                'type' => 'milestone_25',
                'level' => $level,
                'cash' => 5000,
                'pokeballs' => 10,
                'masterballs' => 5
            ];
        }
        
        if ($level % 50 === 0) {
            $milestones[] = [
                'type' => 'milestone_50',
                'level' => $level,
                'cash' => 10000,
                'pokeballs' => 20,
                'masterballs' => 15
            ];
        }
        
        if ($level % 5 !== 0 && $level % 10 !== 0 && $level % 25 !== 0 && $level % 50 !== 0) {
            $seed = $level * 12345;
            srand($seed);
            $cash = rand(1000, 5000);
            $pokeballs = rand(1, 5);
            srand();
            
            $milestones[] = [
                'type' => 'regular_level',
                'level' => $level,
                'cash' => $cash,
                'pokeballs' => $pokeballs,
                'masterballs' => 0
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
            
            foreach ($milestones as $milestone) {
                $rewardKey = $milestone['type'] . '_' . $milestone['level'];
                
                if (!in_array($rewardKey, $claimedRewards)) {
                    $availableRewards[] = [
                        'type' => $milestone['type'],
                        'level' => $milestone['level'],
                        'cash' => $milestone['cash'],
                        'pokeballs' => $milestone['pokeballs'],
                        'masterballs' => $milestone['masterballs'],
                        'is_available' => true
                    ];
                }
            }
        }
        
        return $availableRewards;
    }

    public function getAvailableRewardsForLevel(User $user, int $level): array
    {
        $availableRewards = [];
        $claimedRewards = $user->claimed_level_rewards ?? [];
        $milestones = $this->getMilestonesForLevel($level);
        
        foreach ($milestones as $milestone) {
            $rewardKey = $milestone['type'] . '_' . $milestone['level'];
            
            if (!in_array($rewardKey, $claimedRewards)) {
                $availableRewards[] = [
                    'type' => $milestone['type'],
                    'level' => $milestone['level'],
                    'cash' => $milestone['cash'],
                    'pokeballs' => $milestone['pokeballs'],
                    'masterballs' => $milestone['masterballs'],
                    'is_available' => true
                ];
            }
        }
        
        return $availableRewards;
    }

    public function getClaimedRewards(User $user): array
    {
        $claimedRewards = [];
        $userClaimedRewards = $user->claimed_level_rewards ?? [];
        
        foreach ($userClaimedRewards as $rewardKey) {
            $parts = explode('_', $rewardKey);
            $type = $parts[0];
            $level = (int) $parts[1];
            
            $milestones = $this->getMilestonesForLevel($level);
            
            foreach ($milestones as $milestone) {
                if ($milestone['type'] === $type && $milestone['level'] === $level) {
                    $claimedRewards[] = [
                        'type' => $milestone['type'],
                        'level' => $milestone['level'],
                        'cash' => $milestone['cash'],
                        'pokeballs' => $milestone['pokeballs'],
                        'masterballs' => $milestone['masterballs'],
                        'is_available' => false
                    ];
                    break;
                }
            }
        }
        
        return $claimedRewards;
    }

    public function getNextRewards(User $user): array
    {
        $nextRewards = [];
        $claimedRewards = $user->claimed_level_rewards ?? [];
        
        for ($level = $user->level + 1; $level <= $user->level + 10; $level++) {
            $milestones = $this->getMilestonesForLevel($level);
            
            foreach ($milestones as $milestone) {
                $rewardKey = $milestone['type'] . '_' . $milestone['level'];
                
                if (!in_array($rewardKey, $claimedRewards)) {
                    $nextRewards[] = [
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
        
        return $nextRewards;
    }
} 