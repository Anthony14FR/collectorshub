<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pokemon;
use Illuminate\Support\Facades\Storage;
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

    public function getAllPokemons()
    {
        $jsonFile = storage_path('app/private/data/pokemon_test.json');
        
        if (!file_exists($jsonFile)) {
            return Inertia::render('Pokedex/Index', [
                'pokemons' => [],
                'error' => 'Fichier Pokemon introuvable'
            ]);
        }
        
        $jsonContent = file_get_contents($jsonFile);
        
        $pokemonData = json_decode($jsonContent, true);

        if (!$pokemonData || !is_array($pokemonData)) {
            return Inertia::render('Pokedex/Index', [
                'pokemons' => [],
                'error' => 'Erreur lors du décodage du fichier JSON'
            ]);
        }
        
        $formattedPokemons = collect($pokemonData)->map(function ($pokemon) {
            return [
                'id' => $pokemon['id'] ?? 0,
                'pokedexId' => $pokemon['pokedexId'] ?? 0,
                'name' => $pokemon['name'] ?? 'Inconnu',
                'description' => $pokemon['description'] ?? '',
                'sprite_url' => $pokemon['sprite'] ?? '',
                'image_url' => $pokemon['image'] ?? '',
                'types' => collect($pokemon['apiTypes'] ?? [])->pluck('name')->toArray(),
                'typeImages' => collect($pokemon['apiTypes'] ?? [])->pluck('image', 'name')->toArray(),
                'generation' => $pokemon['apiGeneration'] ?? 1,
                'rarity' => $pokemon['rarity'] ?? 'normal',
                'stats' => $pokemon['stats'] ?? [
                    'HP' => 0,
                    'attack' => 0,
                    'defense' => 0,
                    'special_attack' => 0,
                    'special_defense' => 0,
                    'speed' => 0
                ],
                'evolutions' => $pokemon['apiEvolutions'] ?? []
            ];
        });
        
        return Inertia::render('Pokedex/Index', [
            'pokemons' => $formattedPokemons
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
