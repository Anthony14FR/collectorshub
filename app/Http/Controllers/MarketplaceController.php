<?php

namespace App\Http\Controllers;

use App\Models\Marketplace;
use App\Models\Pokedex;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class MarketplaceController extends Controller
{
    private function getPriceRange(string $rarity): array
    {
        $ranges = [
            'normal' => ['min' => 10, 'max' => 1000000],
            'rare' => ['min' => 100, 'max' => 1000000],
            'epic' => ['min' => 1000, 'max' => 1000000],
            'legendary' => ['min' => 10000, 'max' => 1000000],
        ];

        return $ranges[$rarity] ?? $ranges['normal'];
    }

    public function index()
    {
        $userId = Auth::id();
        $listings = Marketplace::with(['pokemon', 'seller'])
            ->where('status', 'active')
            ->get();

        $myListings = $listings->filter(function ($listing) use ($userId) {
            return $listing->seller_id === $userId;
        });

        $otherListings = $listings->filter(function ($listing) use ($userId) {
            return $listing->seller_id !== $userId;
        });

        return Inertia::render('Marketplace/Index', [
            'myListings' => $myListings,
            'otherListings' => $otherListings
        ]);
    }

    public function sell()
    {
        $userId = Auth::id();
        $userPokemons = Pokedex::where('user_id', $userId)->get();
        
        $myListings = Marketplace::with(['pokemon'])
            ->where('seller_id', $userId)
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

        $userId = Auth::id();
        $pokemonId = $validated['pokemon_id'];
        $price = $validated['price'];

        $pokemon = Pokedex::where('id', $pokemonId)
            ->where('user_id', $userId)
            ->first();

        if (!$pokemon) {
            return back()->withErrors([
                'message' => 'Pokemon non trouvé dans votre collection'
            ]);
        }

        $existingListing = Marketplace::where('pokemon_id', $pokemonId)
            ->where('status', 'active')
            ->first();

        if ($existingListing) {
            return back()->withErrors([
                'message' => 'Ce Pokemon est déjà en vente'
            ]);
        }

        if ($pokemon->is_in_team) {
            return back()->withErrors([
                'message' => 'Impossible de vendre un Pokemon de votre équipe'
            ]);
        }

        $priceRange = $this->getPriceRange($pokemon->rarity);
        if ($price < $priceRange['min'] || $price > $priceRange['max']) {
            return back()->withErrors([
                'message' => "Le prix doit être entre {$priceRange['min']} et {$priceRange['max']} pour un Pokemon {$pokemon->rarity}"
            ]);
        }

        $listing = Marketplace::create([
            'seller_id' => $userId,
            'pokemon_id' => $pokemonId,
            'price' => $price,
            'status' => 'active'
        ]);

        return redirect()->route('marketplace.sell')->with('success', 'Pokémon mis en vente avec succès');
    }

    public function cancelListing($listingId)
    {
        $userId = Auth::id();
        
        $listing = Marketplace::where('id', $listingId)
            ->where('seller_id', $userId)
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
        $buyerId = Auth::id();

        $listing = Marketplace::with(['pokemon'])
            ->where('id', $listingId)
            ->where('status', 'active')
            ->first();

        if (!$listing) {
            return back()->withErrors([
                'message' => 'Annonce non trouvée'
            ]);
        }

        $buyer = User::find($buyerId);
        if (!$buyer) {
            return back()->withErrors([
                'message' => 'Acheteur non trouvé'
            ]);
        }

        if ($buyer->cash < $listing->price) {
            return back()->withErrors([
                'message' => 'Fonds insuffisants'
            ]);
        }

        $seller = User::find($listing->seller_id);
        if (!$seller) {
            return back()->withErrors([
                'message' => 'Vendeur non trouvé'
            ]);
        }

        if ($buyerId === $listing->seller_id) {
            return back()->withErrors([
                'message' => 'Vous ne pouvez pas acheter votre propre Pokemon'
            ]);
        }

        $buyer->update(['cash' => $buyer->cash - $listing->price]);
        $seller->update(['cash' => $seller->cash + $listing->price]);

        $pokemon = Pokedex::find($listing->pokemon_id);
        $pokemon->update(['user_id' => $buyerId]);

        $listing->update([
            'status' => 'sold',
            'sold_at' => now(),
            'buyer_id' => $buyerId,
        ]);

        return redirect()->route('marketplace.index')->with('success', 'Achat réussi');
    }

    public function getListings(Request $request)
    {
        $query = Marketplace::with(['pokemon', 'seller'])
            ->where('status', 'active');
            
        if ($request->has('myListings') && $request->myListings === 'true') {
            $query->where('seller_id', Auth::id());
        }

        if ($request->has('rarity')) {
            $query->whereHas('pokemon', function ($q) use ($request) {
                $q->where('rarity', $request->rarity);
            });
        }

        if ($request->has('type')) {
            $query->whereHas('pokemon', function ($q) use ($request) {
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
            $query->whereHas('pokemon', function ($q) use ($isShiny) {
                $q->where('is_shiny', $isShiny);
            });
        }

        $listings = $query->get();

        return response()->json($listings);
    }
} 