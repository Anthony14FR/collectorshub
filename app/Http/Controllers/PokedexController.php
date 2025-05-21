<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pokedex;
use Inertia\Inertia;

class PokedexController extends Controller
{
    public function getUserPokemons(Request $request)
    {
        $userId = $request->user()->id;
        $userPokemons = Pokedex::where('user_id', $userId)
            ->with('pokemon')
            ->get()
            ->map(function ($userPokemon) {
                return [
                    'id' => $userPokemon->id,
                    'name' => $userPokemon->pokemon->name,
                    'level' => $userPokemon->level,
                    'experience' => $userPokemon->experience,
                    'sprite_url' => $userPokemon->pokemon->sprite_url,
                    'types' => $userPokemon->pokemon->types,
                    'is_in_team' => $userPokemon->isInTeam,
                ];
            });

        return Inertia::render('Me/Pokedex/Index', [
            'pokemons' => $userPokemons
        ]);
    }

    public function addToTeam(Request $request, $id)
    {
        $userId = $request->user()->id;

        $teamCount = Pokedex::where('user_id', $userId)
            ->where('isInTeam', true)
            ->count();

        if ($teamCount >= 6) {
            return response()->json(['error' => 'You can only have up to 6 Pokémon in your team.'], 400);
        }

        $pokemon = Pokedex::where('user_id', $userId)
            ->where('id', $id)
            ->first();

        if (!$pokemon) {
            return response()->json(['error' => 'Pokemon not found in your collection.'], 404);
        }

        $pokemon->update(['isInTeam' => true]);

        return Inertia::render('Pokedex/Team', [
            'message' => 'Pokemon added to your team.',
            'pokemon' => $pokemon
        ]);
    }

    public function removeFromTeam(Request $request, $id)
    {
        $userId = $request->user()->id;
        $pokemon = Pokedex::where('user_id', $userId)
            ->where('id', $id)
            ->first();

        if (!$pokemon) {
            return response()->json(['error' => 'Pokemon not found in your collection.'], 404);
        }

        if (!$pokemon->isInTeam) {
            return response()->json(['error' => 'Pokemon is not in your team.'], 400);
        }

        $teamCount = Pokedex::where('user_id', $userId)
            ->where('isInTeam', true)
            ->count();

        if ($teamCount <= 1) {
            return response()->json(['error' => 'You cannot have an empty team.'], 400);
        }

        $pokemon->update(['isInTeam' => false]);

        return Inertia::render('Pokedex/Team', [
            'message' => 'Pokemon removed from your team.',
            'pokemon' => $pokemon
        ]);
    }
}
