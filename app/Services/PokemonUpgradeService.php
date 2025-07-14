<?php

namespace App\Services;

use App\Models\Pokedex;
use App\Models\User;
use Illuminate\Support\Collection;

class PokemonUpgradeService
{
    public function getUpgradeRequirements(Pokedex $pokedexEntry): array
    {
        $currentStar = $pokedexEntry->star;
        
        if ($currentStar >= 6) {
            throw new \Exception('Ce Pokémon a déjà atteint le niveau maximum (6★)');
        }

        return match ($currentStar) {
            0 => [
                'main_requirement' => [
                    'star' => 0,
                    'pokemon_id' => $pokedexEntry->pokemon_id,
                    'quantity' => 1,
                    'description' => '1 doublon 0★ identique'
                ]
            ],
            1 => [
                'main_requirement' => [
                    'star' => 0,
                    'pokemon_id' => $pokedexEntry->pokemon_id,
                    'quantity' => 2,
                    'description' => '2 Pokémon 0★ identiques'
                ]
            ],
            2 => [
                'main_requirement' => [
                    'star' => 1,
                    'pokemon_id' => $pokedexEntry->pokemon_id,
                    'quantity' => 2,
                    'description' => '2 Pokémon 1★ identiques'
                ]
            ],
            3 => [
                'main_requirement' => [
                    'star' => 2,
                    'pokemon_id' => $pokedexEntry->pokemon_id,
                    'quantity' => 2,
                    'description' => '2 Pokémon 2★ identiques'
                ],
                'secondary_requirement' => [
                    'star' => 1,
                    'pokemon_id' => null,
                    'quantity' => 1,
                    'description' => '1 Pokémon 1★ (n\'importe lequel)'
                ]
            ],
            4 => [
                'main_requirement' => [
                    'star' => 3,
                    'pokemon_id' => $pokedexEntry->pokemon_id,
                    'quantity' => 2,
                    'description' => '2 Pokémon 3★ identiques'
                ],
                'secondary_requirement' => [
                    'star' => 3,
                    'pokemon_id' => null,
                    'quantity' => 1,
                    'description' => '1 Pokémon 3★ (n\'importe lequel)'
                ]
            ],
            5 => [
                'main_requirement' => [
                    'star' => 4,
                    'pokemon_id' => $pokedexEntry->pokemon_id,
                    'quantity' => 2,
                    'description' => '2 Pokémon 4★ identiques'
                ],
                'secondary_requirement' => [
                    'star' => 3,
                    'pokemon_id' => null,
                    'quantity' => 2,
                    'description' => '2 Pokémon 3★ (n\'importe lesquels)'
                ]
            ]
        };
    }

    public function getAvailablePokemons(User $user, array $requirements, int $excludePokedexId = null, bool $isShiny = false): array
    {
        $available = [];

        foreach ($requirements as $key => $requirement) {
            $query = $user->pokedex()
                ->with('pokemon')
                ->where('star', $requirement['star'])
                ->whereHas('pokemon', function($q) use ($isShiny) {
                    $q->where('is_shiny', $isShiny);
                });

            if ($requirement['pokemon_id']) {
                $query->where('pokemon_id', $requirement['pokemon_id']);
            }

            if ($excludePokedexId) {
                $query->where('id', '!=', $excludePokedexId);
            }

            $available[$key] = $query->get();
        }

        return $available;
    }

    public function getAvailablePokemonsForSlot(User $user, array $requirement, int $excludePokedexId, bool $isShiny, array $alreadySelectedIds = []): Collection
    {
        $query = $user->pokedex()
            ->with('pokemon')
            ->where('star', $requirement['star'])
            ->whereHas('pokemon', function($q) use ($isShiny) {
                $q->where('is_shiny', $isShiny);
            });

        if ($requirement['pokemon_id']) {
            $query->where('pokemon_id', $requirement['pokemon_id']);
        }

        $query->where('id', '!=', $excludePokedexId);

        if (!empty($alreadySelectedIds)) {
            $query->whereNotIn('id', $alreadySelectedIds);
        }

        return $query->get();
    }

    public function canUpgrade(Pokedex $pokedexEntry, User $user): bool
    {
        if ($pokedexEntry->star >= 6) {
            return false;
        }

        try {
            $requirements = $this->getUpgradeRequirements($pokedexEntry);
            $availablePokemons = $this->getAvailablePokemons($user, $requirements, $pokedexEntry->id, $pokedexEntry->pokemon->is_shiny);

            foreach ($requirements as $key => $requirement) {
                if (count($availablePokemons[$key]) < $requirement['quantity']) {
                    return false;
                }
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function upgradePokemon(Pokedex $pokedexEntry, array $materials, User $user): Pokedex
    {
        if (!$this->canUpgrade($pokedexEntry, $user)) {
            throw new \Exception('Conditions d\'amélioration non remplies');
        }

        $this->validateMaterials($pokedexEntry, $materials, $user);

        foreach ($materials as $materialId) {
            $material = Pokedex::where('id', $materialId)
                ->where('user_id', $user->id)
                ->firstOrFail();
            $material->delete();
        }

        $pokedexEntry->star += 1;
        $pokedexEntry->save();

        return $pokedexEntry;
    }

    private function validateMaterials(Pokedex $pokedexEntry, array $materials, User $user): void
    {
        $requirements = $this->getUpgradeRequirements($pokedexEntry);
        $totalRequired = 0;

        foreach ($requirements as $requirement) {
            $totalRequired += $requirement['quantity'];
        }

        if (count($materials) !== $totalRequired) {
            throw new \Exception('Nombre de matériaux incorrect');
        }

        foreach ($materials as $materialId) {
            $material = Pokedex::where('id', $materialId)
                ->where('user_id', $user->id)
                ->first();

            if (!$material) {
                throw new \Exception('Matériau invalide');
            }

            if ($material->id === $pokedexEntry->id) {
                throw new \Exception('Vous ne pouvez pas utiliser le Pokémon à améliorer comme matériau');
            }

            if ($pokedexEntry->pokemon->is_shiny !== $material->pokemon->is_shiny) {
                throw new \Exception('Un Pokémon Shiny ne peut être amélioré qu\'avec des matériaux Shiny');
            }
        }
    }
}