<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GameConfiguration;
use App\Services\GameConfigurationService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GameConfigurationController extends Controller
{
    public function __construct(
        private GameConfigurationService $configService
    ) {
    }

    public function index()
    {
        $configurations = GameConfiguration::orderBy('category')
            ->orderBy('key')
            ->get()
            ->groupBy('category');

        return Inertia::render('Admin/GameConfiguration/Index', [
            'configurations' => $configurations,
            'categories' => [
                'level_rewards' => 'Récompenses de niveau',
                'rarity_probabilities' => 'Probabilités de rareté',
                'xp_rewards' => 'Gains d\'XP'
            ]
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'category' => 'required|string',
            'key' => 'required|string',
            'value' => 'required|array'
        ]);

        GameConfiguration::setValue(
            $request->category,
            $request->key,
            $request->value
        );

        $this->configService->clearCache();

        return redirect()->back()->with('success', 'Configuration mise à jour avec succès');
    }



    public function resetCategory(Request $request)
    {
        $request->validate([
            'category' => 'required|string'
        ]);

        $defaults = [
            'level_rewards' => [
                'milestones' => [
                    'milestone_5' => ['cash' => 1500, 'pokeballs' => 10, 'masterballs' => 0],
                    'milestone_10' => ['cash' => 3000, 'pokeballs' => 0, 'masterballs' => 5],
                    'milestone_25' => ['cash' => 5000, 'pokeballs' => 10, 'masterballs' => 5],
                    'milestone_50' => ['cash' => 10000, 'pokeballs' => 20, 'masterballs' => 15],
                    'regular_level' => ['cash' => 2500, 'pokeballs' => 2, 'masterballs' => 0]
                ]
            ],
            'rarity_probabilities' => [
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
            ],
            'xp_rewards' => [
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
            ]
        ];

        $categoryDefaults = $defaults[$request->category] ?? null;

        if ($categoryDefaults) {
            foreach ($categoryDefaults as $key => $value) {
                GameConfiguration::setValue($request->category, $key, $value);
            }

            $this->configService->clearCache();

            return redirect()->back()->with('success', 'Catégorie réinitialisée avec succès');
        }

        return redirect()->back()->with('error', 'Catégorie non trouvée');
    }
}
