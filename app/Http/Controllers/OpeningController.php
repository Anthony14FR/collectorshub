<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Pokemon;
use App\Services\GameConfigurationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class OpeningController extends Controller
{
    public function __construct(
        private GameConfigurationService $configService
    ) {
    }

    public function index()
    {
        $inventory = Auth::user()->inventory()->with('item')->get();

        return Inertia::render('Opening/Index', [
            'inventory' => $inventory
        ]);
    }

    public function open(Request $request)
    {
        $request->validate([
            'ball_type' => 'required|string|in:Pokeball,Masterball',
            'quantity' => 'required|integer|in:1,10'
        ]);

        $user = Auth::user();
        $ballType = $request->ball_type;
        $quantity = $request->quantity;

        $ball = Item::where('name', $ballType)->where('type', 'ball')->first();

        if (!$ball) {
            return response()->json([
                'success' => false,
                'message' => 'Type de ball invalide'
            ], 400);
        }

        $inventory = $user->inventory()->where('item_id', $ball->id)->first();

        if (!$inventory || $inventory->quantity < $quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'avez pas assez de ' . $ballType . ' dans votre inventaire'
            ], 400);
        }

        return DB::transaction(function () use ($user, $ball, $ballType, $quantity, $inventory) {
            $pokemons = [];

            for ($i = 0; $i < $quantity; $i++) {
                $pokemonData = $this->drawRandomPokemon($ballType);
                $pokemons[] = $this->addPokemonToPokedex($user, $pokemonData);
            }

            $inventory->quantity -= $quantity;
            $inventory->save();

            return response()->json([
                'success' => true,
                'message' => $quantity === 1 ? 'Pokémon obtenu avec succès' : 'Pokémons obtenus avec succès',
                'pokemons' => $pokemons
            ]);
        });
    }

    private function drawRandomPokemon($ballType)
    {
        $rarityProbabilities = $this->configService->getRarityProbabilities();

        if (!isset($rarityProbabilities['ball_types'])) {
            throw new \Exception('Configuration des probabilités des balls non trouvée');
        }

        $rarityChances = $rarityProbabilities['ball_types'][$ballType] ?? null;

        if (!$rarityChances) {
            throw new \Exception('Type de ball non pris en charge: ' . $ballType . '. Types disponibles: ' . implode(',', array_keys($rarityProbabilities['ball_types'])));
        }

        $rand = rand(1, 100);
        $probability = 0;
        $selectedRarity = 'normal';

        foreach ($rarityChances as $rarity => $chance) {
            $probability += $chance;
            if ($rand <= $probability) {
                $selectedRarity = $rarity;
                break;
            }
        }

        $pokemon = Pokemon::where('rarity', $selectedRarity)
            ->where('is_shiny', false)
            ->inRandomOrder()
            ->first();

        if (!$pokemon) {
            throw new \Exception('Aucun Pokémon trouvé pour la rareté: ' . $selectedRarity);
        }

        $shinyRate = $this->configService->getShinyRate();
        $isShiny = rand(1, 100) <= $shinyRate;

        if ($isShiny) {
            $shinyPokemon = Pokemon::where('pokedex_id', $pokemon->pokedex_id)
                ->where('is_shiny', true)
                ->first();

            if ($shinyPokemon) {
                $pokemon = $shinyPokemon;
            }
        }

        return [
            'pokemon' => $pokemon,
            'is_shiny' => $isShiny
        ];
    }

    private function addPokemonToPokedex($user, $pokemonData)
    {
        $pokemon = $pokemonData['pokemon'];
        $isShiny = $pokemonData['is_shiny'];

        $pokedexEntry = $user->pokedex()->create([
            'pokemon_id' => $pokemon->id,
            'nickname' => null,
            'level' => 1,
            'star' => 0,
            'hp_left' => $pokemon->hp,
            'is_in_team' => false,
            'is_favorite' => false,
            'obtained_at' => now()
        ]);

        return [
            'id' => $pokedexEntry->id,
            'pokemon_id' => $pokemon->id,
            'name' => $pokemon->name,
            'types' => $pokemon->types,
            'rarity' => $pokemon->rarity,
            'is_shiny' => $isShiny
        ];
    }
}
