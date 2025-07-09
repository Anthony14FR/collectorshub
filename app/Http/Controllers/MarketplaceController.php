<?php

namespace App\Http\Controllers;

use App\Models\Marketplace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class MarketplaceController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $listings = Marketplace::with(['pokemon.pokemon', 'seller'])
            ->where('status', 'active')
            ->get();

        $myListings = $listings->where('seller_id', $user->id)->values();
        $otherListings = $listings->where('seller_id', '!=', $user->id)->values();

        return Inertia::render('Marketplace/Index', [
            'myListings' => $myListings,
            'otherListings' => $otherListings
        ]);
    }

    public function sell()
    {
        $user = auth()->user();
        $userPokemons = $user->pokedex()
            ->with('pokemon')
            ->get();

        $myListings = $user->marketplaceSales()
            ->with(['pokemon.pokemon'])
            ->where('status', 'active')
            ->get();

        return Inertia::render('Marketplace/Sell', [
            'userPokemons' => $userPokemons,
            'myListings' => $myListings
        ]);
    }

    public function listPokemon(Request $request)
    {
        $validated = $request->validate([
            'pokemon_id' => 'required|integer|exists:pokedex,id',
            'price' => 'required|integer|min:10|max:1000000',
        ]);

        $user = auth()->user();
        $pokemonId = $validated['pokemon_id'];
        $price = $validated['price'];

        $pokedexEntry = $user->pokedex()
            ->with('pokemon')
            ->where('id', $pokemonId)
            ->first();

        if (!$pokedexEntry) {
            return back()->withErrors([
                'message' => 'Pokemon non trouvé dans votre collection'
            ]);
        }

        $existingListing = Marketplace::where('pokemon_id', $pokemonId)
            ->where('status', 'active')
            ->exists();

        if ($existingListing) {
            return back()->withErrors([
                'message' => 'Ce Pokemon est déjà en vente'
            ]);
        }

        if ($pokedexEntry->is_in_team) {
            return back()->withErrors([
                'message' => 'Impossible de vendre un Pokemon de votre équipe'
            ]);
        }

        $priceRange = Marketplace::getPriceRange($pokedexEntry->pokemon->rarity);
        if ($price < $priceRange['min'] || $price > $priceRange['max']) {
            return back()->withErrors([
                'message' => "Le prix doit être entre {$priceRange['min']} et {$priceRange['max']} pour un Pokemon {$pokedexEntry->pokemon->rarity}"
            ]);
        }

        $user->marketplaceSales()->create([
            'pokemon_id' => $pokemonId,
            'price' => $price,
            'status' => 'active'
        ]);

        return redirect()->route('marketplace.sell')->with('success', 'Pokémon mis en vente avec succès');
    }

    public function cancelListing($listingId)
    {
        $user = auth()->user();
        $listing = $user->marketplaceSales()
            ->where('id', $listingId)
            ->where('status', 'active')
            ->first();

        if (!$listing) {
            return back()->withErrors([
                'message' => 'Annonce non trouvée ou vous n\'êtes pas autorisé à la retirer'
            ]);
        }

        $listing->update([
            'status' => 'cancelled'
        ]);

        return redirect()->route('marketplace.sell')->with('success', 'Annonce retirée avec succès');
    }

    public function buyPokemon(Request $request, $listingId)
    {
        $user = auth()->user();
        $listing = Marketplace::with(['pokemon.pokemon', 'seller'])
            ->where('id', $listingId)
            ->where('status', 'active')
            ->first();

        if (!$listing) {
            return back()->withErrors([
                'message' => 'Annonce non trouvée'
            ]);
        }

        if ($user->cash < $listing->price) {
            return back()->withErrors([
                'message' => 'Fonds insuffisants'
            ]);
        }

        if ($user->id === $listing->seller_id) {
            return back()->withErrors([
                'message' => 'Vous ne pouvez pas acheter votre propre Pokemon'
            ]);
        }

        try {
            DB::transaction(function () use ($user, $listing) {
                $seller = $listing->seller;

                $user->cash -= $listing->price;
                $user->save();

                $seller->cash += $listing->price;
                $seller->save();

                $pokemon = $listing->pokemon;
                $pokemon->user_id = $user->id;
                $pokemon->save();

                $listing->status = 'sold';
                $listing->sold_at = now();
                $listing->buyer_id = $user->id;
                $listing->save();
            });
        } catch (\Throwable $e) {
            return back()->withErrors([
                'message' => 'Erreur lors de l\'achat : ' . $e->getMessage()
            ]);
        }

        return redirect()->route('marketplace.index')->with('success', 'Achat réussi');
    }

    public function getListings(Request $request)
    {
        $user = auth()->user();
        $query = Marketplace::with(['pokemon.pokemon', 'seller'])
            ->where('status', 'active');

        if ($request->has('myListings') && $request->myListings === 'true') {
            $query->where('seller_id', $user->id);
        }

        if ($request->has('rarity')) {
            $query->whereHas('pokemon.pokemon', function ($q) use ($request) {
                $q->where('rarity', $request->rarity);
            });
        }

        if ($request->has('type')) {
            $query->whereHas('pokemon.pokemon', function ($q) use ($request) {
                $q->whereJsonContains('types', $request->type);
            });
        }

        if ($request->has('minPrice')) {
            $query->where('price', '>=', $request->minPrice);
        }

        if ($request->has('maxPrice')) {
            $query->where('price', '<=', $request->maxPrice);
        }

        if ($request->has('isShiny')) {
            $isShiny = filter_var($request->isShiny, FILTER_VALIDATE_BOOLEAN);
            $query->whereHas('pokemon.pokemon', function ($q) use ($isShiny) {
                $q->where('is_shiny', $isShiny);
            });
        }

        $listings = $query->get();

        return response()->json($listings);
    }
}
