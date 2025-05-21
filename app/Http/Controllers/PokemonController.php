<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pokemon;
use Inertia\Inertia;

class PokemonController extends Controller
{
    public function index()
    {
        $pokemons = Pokemon::all();
        if($pokemons->isEmpty()) {
            return response()->json(['error' => 'No pokemons found'], 404);
        }
        return Inertia::render('Pokemon/Index', [
            'pokemons' => $pokemons
        ]);
    }

    public function search(Request $request)
    {
        $query = Pokemon::query();

        if($request->type) {
            $query->where('types', 'like', "%{$request->type}%");
        }
        if($request->rarity) {
            $query->where('rarity', $request->rarity);
        }
        if($request->generation) {
            $query->where('generation', $request->generation);
        }

        $pokemons = $query->get();

        if($pokemons->isEmpty()) {
            return response()->json(['error' => 'No pokemons found'], 404);
        }
        return Inertia::render('Pokemon/Search', [
            'pokemons' => $pokemons,
            'filters' => $request->only(['type', 'rarity', 'generation'])
        ]);
    }

    public function show($idOrName)
    {
        $pokemon = is_numeric($idOrName)
            ? Pokemon::find($idOrName)
            : Pokemon::where('name', $idOrName)->first();

        if(!$pokemon) {
            return response()->json(['error' => 'Pokemon not found'], 404);
        }
        return Inertia::render('Pokemon/Show', [
            'pokemon' => $pokemon
        ]);
    }
}
