<?php

namespace App\Http\Controllers;

use App\Models\Pokedex;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PokedexController extends Controller
{
    public function getUserPokemons()
    {
        $user = Auth::user();
        $userPokemons = $user->pokedex()->with('pokemon')->get();

        if ($userPokemons->isEmpty()) {
            return response()->json(['error' => 'No Pokémon found for this user'], 404);
        }

        return Inertia::render('Pokedex/UserPokemons', [
            'user' => $user,
            'pokemons' => $userPokemons
        ]);
    }

    public function addToTeam(Request $request, $id)
    {
        $userId = $request->user()->id;

        $teamCount = Pokedex::where('user_id', $userId)
            ->where('is_in_team', true)
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

        $pokemon->update(['is_in_team' => true]);

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

        if (!$pokemon->is_in_team) {
            return response()->json(['error' => 'Pokemon is not in your team.'], 400);
        }

        $teamCount = Pokedex::where('user_id', $userId)
            ->where('is_in_team', true)
            ->count();

        if ($teamCount <= 1) {
            return response()->json(['error' => 'You cannot have an empty team.'], 400);
        }

        $pokemon->update(['is_in_team' => false]);

        return Inertia::render('Pokedex/Team', [
            'message' => 'Pokemon removed from your team.',
            'pokemon' => $pokemon
        ]);
    }
}
