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
        $request->validate([
            'position' => 'required|integer|min:1|max:6',
        ]);

        $userId = Auth::id();
        $user = Auth::user();

        $teamCount = Pokedex::where('user_id', $userId)
            ->where('is_in_team', true)
            ->count();

        if ($teamCount >= 6) {
            return back()->withErrors(['message' => 'Vous ne pouvez avoir que 6 Pokémon dans votre équipe.']);
        }

        $pokemon = Pokedex::where('user_id', $userId)
            ->where('id', $id)
            ->firstOrFail();

        $pokemon->update([
            'is_in_team' => true,
            'team_position' => $request->position,
        ]);

        if ($user->isInClub()) {
            $user->club()->first()->updateTotalCp();
        }

        return redirect()->back()->with('success', 'Pokémon ajouté à l\'équipe !');
    }

    public function removeFromTeam(Request $request, $id)
    {
        $userId = Auth::id();
        $user = Auth::user();

        $pokemon = Pokedex::where('user_id', $userId)
            ->where('id', $id)
            ->where('is_in_team', true)
            ->firstOrFail();

        $pokemon->update([
            'is_in_team' => false,
            'team_position' => null,
        ]);

        if ($user->isInClub()) {
            $user->club()->first()->updateTotalCp();
        }

        return redirect()->back()->with('success', 'Pokémon retiré de l\'équipe.');
    }
}
