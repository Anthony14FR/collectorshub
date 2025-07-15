<?php

namespace App\Services;

use App\Models\Expedition;
use App\Models\ExpeditionPokemon;
use App\Models\Item;
use App\Models\Pokedex;
use App\Models\User;
use App\Models\UserExpedition;
use Illuminate\Support\Facades\DB;

class ExpeditionService
{
    public function getDailyExpeditions(User $user): array
    {
        $existingUserExpeditions = UserExpedition::where('user_id', $user->id)
            ->with('expedition.requirements')
            ->get();

        $completedExpeditionIds = $existingUserExpeditions
            ->where('status', 'completed')
            ->pluck('expedition_id')
            ->toArray();

        $activeExpeditions = $existingUserExpeditions
            ->whereNotIn('status', ['completed'])
            ->values();

        $neededExpeditions = 3 - $activeExpeditions->count();

        if ($neededExpeditions > 0) {
            $excludedIds = $activeExpeditions->pluck('expedition_id')
                ->merge($completedExpeditionIds)
                ->toArray();

            $newExpeditions = $this->selectExpeditionsByRarity($excludedIds, $neededExpeditions);

            foreach ($newExpeditions as $expedition) {
                $userExpedition = UserExpedition::create([
                    'user_id' => $user->id,
                    'expedition_id' => $expedition->id,
                    'date' => now()->toDateString(),
                    'status' => 'available'
                ]);

                $userExpedition->setRelation('expedition', $expedition);

                $activeExpeditions->push($userExpedition);
            }
        }

        return $activeExpeditions->map(function ($userExpedition) {
            $canBeClaimed = $userExpedition->status === 'in_progress' &&
                           $userExpedition->ends_at &&
                           now()->greaterThanOrEqualTo($userExpedition->ends_at);

            return [
                'id' => $userExpedition->id,
                'expedition' => $userExpedition->expedition,
                'status' => $userExpedition->status,
                'started_at' => $userExpedition->started_at,
                'ends_at' => $userExpedition->ends_at,
                'can_be_claimed' => $canBeClaimed
            ];
        })->toArray();
    }

    private function selectExpeditionsByRarity(array $excludedIds, int $neededExpeditions): array
    {
        $rarityWeights = [
            'normal' => 50,
            'rare' => 30,
            'epic' => 15,
            'legendary' => 5
        ];

        $selectedExpeditions = [];

        for ($i = 0; $i < $neededExpeditions; $i++) {
            $selectedRarity = $this->selectWeightedRarity($rarityWeights);

            $expedition = Expedition::where('is_active', true)
                ->where('rarity', $selectedRarity)
                ->whereNotIn('id', array_merge($excludedIds, collect($selectedExpeditions)->pluck('id')->toArray()))
                ->with('requirements')
                ->inRandomOrder()
                ->first();

            if ($expedition) {
                $selectedExpeditions[] = $expedition;
            } else {
                $expedition = Expedition::where('is_active', true)
                    ->whereNotIn('id', array_merge($excludedIds, collect($selectedExpeditions)->pluck('id')->toArray()))
                    ->with('requirements')
                    ->inRandomOrder()
                    ->first();

                if ($expedition) {
                    $selectedExpeditions[] = $expedition;
                }
            }
        }

        return $selectedExpeditions;
    }

    private function selectWeightedRarity(array $weights): string
    {
        $totalWeight = array_sum($weights);
        $random = rand(1, $totalWeight);

        $currentWeight = 0;
        foreach ($weights as $rarity => $weight) {
            $currentWeight += $weight;
            if ($random <= $currentWeight) {
                return $rarity;
            }
        }

        return 'normal';
    }

    public function getEligiblePokemons(User $user, Expedition $expedition): array
    {
        $pokemons = $user->pokedex()
            ->with('pokemon')
            ->whereDoesntHave('expeditionPokemons', function ($query) {
                $query->where('claimed_at', null)
                      ->where('ends_at', '>', now());
            })
            ->where('is_in_team', false)
            ->get();

        return $pokemons->toArray();
    }

    public function startExpeditionWithDetails(User $user, Expedition $expedition, array $pokemonIds): array
    {
        return DB::transaction(function () use ($user, $expedition, $pokemonIds) {
            $userExpedition = UserExpedition::where('user_id', $user->id)
                ->where('expedition_id', $expedition->id)
                ->first();

            if (!$userExpedition) {
                return ['success' => false, 'message' => 'Expédition non trouvée pour cet utilisateur'];
            }

            if ($userExpedition->status !== 'available') {
                return ['success' => false, 'message' => 'Cette expédition n\'est pas disponible (statut: ' . $userExpedition->status . ')'];
            }

            $selectedPokemons = Pokedex::whereIn('id', $pokemonIds)
                ->where('user_id', $user->id)
                ->with('pokemon')
                ->get();

            if ($selectedPokemons->count() !== count($pokemonIds)) {
                return ['success' => false, 'message' => 'Certains Pokémons sélectionnés ne vous appartiennent pas'];
            }

            foreach ($selectedPokemons as $pokemon) {
                if ($pokemon->isInExpedition()) {
                    return ['success' => false, 'message' => $pokemon->nickname . ' est déjà en expédition'];
                }
                if ($pokemon->is_in_team) {
                    return ['success' => false, 'message' => $pokemon->nickname . ' est dans votre équipe'];
                }
            }

            if (!$expedition->canUserParticipate($user, $selectedPokemons)) {
                $requirements = $expedition->requirements;
                $missingRequirements = [];

                foreach ($requirements as $requirement) {
                    $count = 0;
                    foreach ($selectedPokemons as $pokedexEntry) {
                        $pokemon = $pokedexEntry->pokemon;

                        switch ($requirement->type) {
                            case 'rarity':
                                if ($pokemon->rarity === $requirement->value) {
                                    $count++;
                                }
                                break;
                            case 'type':
                                $types = is_array($pokemon->types) ? $pokemon->types : json_decode($pokemon->types, true);
                                foreach ($types as $type) {
                                    $typeName = is_array($type) ? $type['name'] : $type;
                                    if (strtolower($typeName) === strtolower($requirement->value)) {
                                        $count++;
                                        break;
                                    }
                                }
                                break;
                        }
                    }

                    if ($count < $requirement->quantity) {
                        $missingRequirements[] = "Il faut {$requirement->quantity} Pokémon de {$requirement->type} {$requirement->value} (vous en avez {$count})";
                    }
                }

                return ['success' => false, 'message' => 'Exigences non respectées: ' . implode(', ', $missingRequirements)];
            }

            $startTime = now();
            $endTime = $startTime->copy()->addMinutes($expedition->duration_minutes);

            $userExpedition->update([
                'status' => 'in_progress',
                'started_at' => $startTime,
                'ends_at' => $endTime
            ]);

            foreach ($selectedPokemons as $pokemon) {
                ExpeditionPokemon::create([
                    'expedition_id' => $expedition->id,
                    'pokedex_id' => $pokemon->id,
                    'started_at' => $startTime,
                    'ends_at' => $endTime
                ]);
            }

            return ['success' => true, 'message' => 'Expédition commencée avec succès'];
        });
    }

    public function claimExpedition(User $user, int $userExpeditionId): array
    {
        return DB::transaction(function () use ($user, $userExpeditionId) {
            $userExpedition = UserExpedition::where('id', $userExpeditionId)
                ->where('user_id', $user->id)
                ->with('expedition')
                ->first();

            if (!$userExpedition || !$userExpedition->canBeClaimed()) {
                return ['success' => false, 'message' => 'Expédition non disponible pour réclamation'];
            }

            $claimedAt = now();

            ExpeditionPokemon::where('expedition_id', $userExpedition->expedition_id)
                ->where('started_at', $userExpedition->started_at)
                ->whereHas('pokedex', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->update(['claimed_at' => $claimedAt]);

            $rewards = $this->processRewards($user, $userExpedition->expedition->rewards);

            $userExpedition->delete();

            return [
                'success' => true,
                'message' => 'Expédition terminée avec succès',
                'rewards' => $rewards
            ];
        });
    }

    private function processRewards(User $user, array $rewards): array
    {
        $processedRewards = [];

        foreach ($rewards as $reward) {
            switch ($reward['type']) {
                case 'cash':
                    $user->increment('cash', $reward['amount']);
                    $processedRewards[] = [
                        'type' => 'cash',
                        'amount' => $reward['amount']
                    ];
                    break;

                case 'xp':
                    $user->addXp($reward['amount']);
                    $processedRewards[] = [
                        'type' => 'xp',
                        'amount' => $reward['amount']
                    ];
                    break;

                case 'pokeball':
                    $this->addItemToInventory($user, 'pokeball', $reward['amount']);
                    $processedRewards[] = [
                        'type' => 'pokeball',
                        'amount' => $reward['amount']
                    ];
                    break;

                case 'masterball':
                    $this->addItemToInventory($user, 'masterball', $reward['amount']);
                    $processedRewards[] = [
                        'type' => 'masterball',
                        'amount' => $reward['amount']
                    ];
                    break;

                case 'item':
                    $processedRewards[] = [
                        'type' => 'item',
                        'item_id' => $reward['item_id'],
                        'quantity' => $reward['quantity']
                    ];
                    break;
            }
        }

        return $processedRewards;
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

    public function getExpeditionStatus(User $user, int $expeditionId): ?array
    {
        $userExpedition = UserExpedition::where('user_id', $user->id)
            ->where('expedition_id', $expeditionId)
            ->with('expedition')
            ->first();

        if (!$userExpedition) {
            return null;
        }

        $expeditionPokemons = ExpeditionPokemon::where('expedition_id', $expeditionId)
            ->where('started_at', $userExpedition->started_at)
            ->whereHas('pokedex', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with('pokedex.pokemon')
            ->get();

        return [
            'status' => $userExpedition->status,
            'started_at' => $userExpedition->started_at,
            'ends_at' => $userExpedition->ends_at,
            'claimed_at' => $userExpedition->claimed_at,
            'can_be_claimed' => $userExpedition->canBeClaimed(),
            'pokemons' => $expeditionPokemons->toArray()
        ];
    }
}
