<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ShopController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $items = Item::all();
        $inventory = $user->inventory()->with('item')->get();

        return Inertia::render('Shop/Index', [
            'user' => $user,
            'items' => $items,
            'inventory' => $inventory
        ]);
    }

    public function buyItem(Request $request)
    {
        $validated = $request->validate([
            'item_id' => 'required|integer|exists:items,id',
            'quantity' => 'required|integer|min:1|max:100',
        ]);

        $user = Auth::user();
        $item = Item::findOrFail($validated['item_id']);
        $quantity = $validated['quantity'];
        $totalCost = $item->price * $quantity;

        if ($user->cash < $totalCost) {
            return back()->withErrors([
                'message' => 'Vous n\'avez pas assez d\'argent pour acheter cet item'
            ]);
        }

        try {
            $inventoryItem = Inventory::firstOrNew([
                'user_id' => $user->id,
                'item_id' => $item->id
            ]);

            if ($inventoryItem->exists) {
                if ($inventoryItem->quantity + $quantity > 999) {
                    return back()->withErrors([
                        'message' => 'Vous ne pouvez pas avoir plus de 999 exemplaires de cet item'
                    ]);
                }
                $inventoryItem->quantity += $quantity;
            } else {
                $inventoryItem->quantity = $quantity;
            }

            $user->cash -= $totalCost;
            $user->save();
            $inventoryItem->save();

            return redirect()->route('shop.index')->with('success', "Achat de {$quantity} {$item->name} réussi");
        } catch (\Exception $e) {
            return back()->withErrors([
                'message' => 'Erreur lors de l\'achat : ' . $e->getMessage()
            ]);
        }
    }
}
