<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Item;
use App\Services\DailyQuestService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ShopController extends Controller
{
    protected DailyQuestService $dailyQuestService;

    public function __construct()
    {
        $this->dailyQuestService = app(DailyQuestService::class);
    }

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

        try {
            if ($item->type === 'avatar') {
                $unlocked = $user->unlocked_avatars;
                if (is_string($unlocked)) {
                    $unlocked = json_decode($unlocked, true);
                }
                $unlocked = $unlocked ?? [];
                if (in_array($item->image, $unlocked)) {
                    return back()->withErrors([
                        'message' => 'Avatar déjà débloqué'
                    ]);
                }
                if ($user->cash < $item->price) {
                    return back()->withErrors([
                        'message' => 'Vous n\'avez pas assez d\'argent pour acheter cet avatar'
                    ]);
                }
                $unlocked[] = $item->image;
                $user->unlocked_avatars = $unlocked;
                $user->cash -= $item->price;
                $user->save();
                return redirect()->route('shop.index')->with('success', "Avatar débloqué !");
            }

            if ($item->type === 'background') {
                $unlocked = $user->unlocked_backgrounds;
                if (is_string($unlocked)) {
                    $unlocked = json_decode($unlocked, true);
                }
                $unlocked = $unlocked ?? [];
                if (in_array($item->image, $unlocked)) {
                    return back()->withErrors([
                        'message' => 'Background déjà débloqué'
                    ]);
                }
                if ($user->cash < $item->price) {
                    return back()->withErrors([
                        'message' => 'Vous n\'avez pas assez d\'argent pour acheter ce background'
                    ]);
                }
                $unlocked[] = $item->image;
                $user->unlocked_backgrounds = $unlocked;
                $user->cash -= $item->price;
                $user->save();
                return redirect()->route('shop.index')->with('success', "Background débloqué !");
            }

            if ($user->cash < $totalCost) {
                return back()->withErrors([
                    'message' => 'Vous n\'avez pas assez d\'argent pour acheter cet item'
                ]);
            }

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

            $this->dailyQuestService->incrementQuestProgress($user, 'buy_shop_item');

            return redirect()->route('shop.index')->with('success', "Achat de {$quantity} {$item->name} réussi");
        } catch (\Exception $e) {
            return back()->withErrors([
                'message' => 'Erreur lors de l\'achat : ' . $e->getMessage()
            ]);
        }
    }
}
