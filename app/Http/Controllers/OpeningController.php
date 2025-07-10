<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Pokemon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class OpeningController extends Controller
{
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
                $pokemon = $this->drawRandomPokemon($ballType);
                $pokemons[] = $this->addPokemonToPokedex($user, $pokemon);
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
        $rarityChances = [];

        switch ($ballType) {
            case 'Pokeball':
                $rarityChances = [
                    'normal' => 70, // 70%
                    'rare' => 27, // 27%
                    'epic' => 2.7, // 2.7%
                    'legendary' => 0.3 // 0.3%
                ];
                break;

            case 'Masterball':
                $rarityChances = [
                    'normal' => 34, // 34%
                    'rare' => 60, // 60%
                    'epic' => 5, // 5%
                    'legendary' => 100 // 1%
                ];
                break;

            default:
                throw new \Exception('Type de ball non pris en charge: ' . $ballType);
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

        return Pokemon::where('rarity', $selectedRarity)
            ->inRandomOrder()
            ->firstOrFail();
    }

    private function addPokemonToPokedex($user, $pokemon)
    {
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
            'is_shiny' => $pokemon->is_shiny
        ];
    }
}
