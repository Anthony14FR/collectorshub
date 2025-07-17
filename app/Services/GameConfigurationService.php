<?php

namespace App\Services;

use App\Models\GameConfiguration;
use Illuminate\Support\Facades\Cache;

class GameConfigurationService
{
    private const CACHE_TTL = 3600;

    public function getLevelRewards(): array
    {
        return Cache::remember('game_config.level_rewards', self::CACHE_TTL, function () {
            return GameConfiguration::getValue('level_rewards', 'milestones', [
                'milestone_5' => ['cash' => 1500, 'pokeballs' => 10, 'masterballs' => 0],
                'milestone_10' => ['cash' => 3000, 'pokeballs' => 0, 'masterballs' => 5],
                'milestone_25' => ['cash' => 5000, 'pokeballs' => 10, 'masterballs' => 5],
                'milestone_50' => ['cash' => 10000, 'pokeballs' => 20, 'masterballs' => 15],
                'regular_level' => ['cash' => 2500, 'pokeballs' => 2, 'masterballs' => 0]
            ]);
        });
    }

    public function getRarityProbabilities(): array
    {
        return Cache::remember('game_config.rarity_probabilities', self::CACHE_TTL, function () {
            $value = GameConfiguration::getValue('rarity_probabilities', 'ball_types', null);
            if (is_array($value) && isset($value['Pokeball']) && isset($value['Masterball'])) {
                return ['ball_types' => $value];
            }
            return [
                'ball_types' => [
                    'Pokeball' => [
                        'normal' => 70,
                        'rare' => 27,
                        'epic' => 2.7,
                        'legendary' => 0.3
                    ],
                    'Masterball' => [
                        'normal' => 34,
                        'rare' => 60,
                        'epic' => 5,
                        'legendary' => 1
                    ]
                ]
            ];
        });
    }

    public function getXpRewards(): array
    {
        return Cache::remember('game_config.xp_rewards', self::CACHE_TTL, function () {
            $default = [
                'rarity_bonuses' => [
                    'normal' => [
                        'base' => 1,
                        'shiny' => 5,
                        'first_catch_bonus' => 1
                    ],
                    'rare' => [
                        'base' => 5,
                        'shiny' => 10,
                        'first_catch_bonus' => 5
                    ],
                    'epic' => [
                        'base' => 10,
                        'shiny' => 20,
                        'first_catch_bonus' => 10
                    ],
                    'legendary' => [
                        'base' => 35,
                        'shiny' => 70,
                        'first_catch_bonus' => 15
                    ]
                ]
            ];
            $value = GameConfiguration::getValue('xp_rewards', 'rarity_bonuses', null);
            if (is_array($value) && array_key_exists('rarity_bonuses', $value)) {
                return $value;
            }
            if (is_array($value)) {
                return ['rarity_bonuses' => $value];
            }
            return $default;
        });
    }

    public function getShinyRate(): int
    {
        return Cache::remember('game_config.shiny_rate', self::CACHE_TTL, function () {
            return GameConfiguration::getValue('shiny_rate', 'chance', 1);
        });
    }

    public function clearCache(): void
    {
        Cache::forget('game_config.level_rewards');
        Cache::forget('game_config.rarity_probabilities');
        Cache::forget('game_config.xp_rewards');
        Cache::forget('game_config.shiny_rate');
    }
}
