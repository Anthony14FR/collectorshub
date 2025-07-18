<?php

namespace Database\Seeders;

use App\Models\Pokemon;
use Illuminate\Database\Seeder;

class PokemonSeeder extends Seeder
{
    private function normalizeTypes($types)
    {
        $typeMapping = [
            'Électrik' => 'Electrik',
            'Électrique' => 'Electrik',
            'Fée' => 'Fee',
            'Ténèbres' => 'Tenebres',
            'Acier' => 'Acier',
            'Eau' => 'Eau',
            'Feu' => 'Feu',
            'Plante' => 'Plante',
            'Normal' => 'Normal',
            'Vol' => 'Vol',
            'Poison' => 'Poison',
            'Sol' => 'Sol',
            'Roche' => 'Roche',
            'Insecte' => 'Insecte',
            'Spectre' => 'Spectre',
            'Combat' => 'Combat',
            'Psy' => 'Psy',
            'Glace' => 'Glace',
            'Dragon' => 'Dragon'
        ];

        $normalizedTypes = [];
        foreach ($types as $type) {
            $typeName = is_array($type) ? $type['name'] : $type;

            $normalizedName = $typeMapping[$typeName] ?? $typeName;

            if (is_array($type)) {
                $type['name'] = $normalizedName;
                $normalizedTypes[] = $type;
            } else {
                $normalizedTypes[] = $normalizedName;
            }
        }

        return $normalizedTypes;
    }

    private function normalizeResistances($resistances)
    {
        $normalizedResistances = [];
        $validRelations = ['neutral', 'resistant', 'vulnerable', 'twice_resistant'];

        foreach ($resistances as $resistance) {
            $relation = strtolower(str_replace(' ', '_', $resistance['damage_relation']));
            if (!in_array($relation, $validRelations)) {
                $relation = 'neutral';
            }
            $resistance['damage_relation'] = $relation;
            $normalizedResistances[] = $resistance;
        }

        return $normalizedResistances;
    }

    public function run()
    {
        $jsonPath = storage_path('app/private/data/pokemon_test.json');
        $jsonData = json_decode(file_get_contents($jsonPath), true);

        foreach ($jsonData as $pokemon) {
            $normalizedTypes = $this->normalizeTypes($pokemon['apiTypes']);
            $normalizedResistances = $this->normalizeResistances($pokemon['apiResistances']);

            Pokemon::firstOrCreate(
                ['id' => $pokemon['id']],
                [
                    'pokedex_id' => $pokemon['pokedexId'],
                    'name' => $pokemon['name'],
                    'types' => $normalizedTypes,
                    'resistances' => $normalizedResistances,
                    'evolution_id' => $pokemon['apiEvolutions'][0]['pokedexId'] ?? null,
                    'pre_evolution_id' => $pokemon['apiPreEvolution']['pokedexId'] ?? null,
                    'description' => $pokemon['description'],
                    'height' => $pokemon['height'],
                    'weight' => $pokemon['weight'],
                    'rarity' => $pokemon['rarity'],
                    'is_shiny' => $pokemon['isShiny'],
                    'hp' => $pokemon['stats']['HP'],
                    'attack' => $pokemon['stats']['attack'],
                    'defense' => $pokemon['stats']['defense'],
                    'speed' => $pokemon['stats']['speed'],
                    'special_attack' => $pokemon['stats']['special_attack'],
                    'special_defense' => $pokemon['stats']['special_defense'],
                    'generation' => $pokemon['apiGeneration'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
        }
    }
}
