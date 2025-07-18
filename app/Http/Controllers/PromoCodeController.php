<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Item;
use App\Models\PromoCode;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PromoCodeController extends Controller
{
    public function index()
    {
        $promoCodes = PromoCode::with(['items', 'users'])->get();
        return Inertia::render('PromoCodes/Index', [
            'promoCodes' => $promoCodes,
        ]);
    }

    public function create()
    {
        $users = User::all();
        $items = Item::all();
        return Inertia::render('PromoCodes/Create', [
            'users' => $users,
            'items' => $items
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:promo_codes',
            'isGlobal' => 'required|boolean',
            'cash' => 'nullable|integer|min:0',
            'expiresAt' => 'nullable|date',
            'targetUserIds' => 'nullable|array',
            'items' => 'nullable|array',
        ]);

        try {
            $promoCode = PromoCode::withoutEvents(function () use ($request) {
                $promoCode = PromoCode::create([
                    'code' => $request->code,
                    'is_active' => true,
                    'expires_at' => $request->expiresAt,
                    'is_global' => $request->isGlobal,
                    'cash' => $request->cash ?? 0
                ]);

                if (!empty($request->items)) {
                    foreach ($request->items as $item) {
                        $promoCode->items()->attach($item['id'], ['quantity' => $item['quantity']]);
                    }
                }

                if (!$request->isGlobal && !empty($request->targetUserIds)) {
                    $promoCode->users()->attach($request->targetUserIds);
                }

                return $promoCode;
            });

            $createdPromoCode = PromoCode::with(['items', 'users'])->find($promoCode->id);
            return response()->json($createdPromoCode, 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error creating promo code', 'error' => $e->getMessage()], 500);
        }
    }

    public function useCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:20'
        ]);

        $user = auth()->user();
        $code = strtoupper(trim($request->code));

        try {
            $rewards = DB::transaction(function () use ($user, $code) {
                $promoCode = PromoCode::where('code', $code)
                    ->where('is_active', true)
                    ->where(function ($query) {
                        $query->where('start_date', '<=', now())
                            ->where('end_date', '>=', now());
                    })
                    ->first();

                if (!$promoCode) {
                    throw new \Exception('Code promo invalide ou expiré');
                }

                if ($promoCode->max_uses > 0 && $promoCode->current_uses >= $promoCode->max_uses) {
                    throw new \Exception('Ce code promo a atteint sa limite d\'utilisation');
                }

                if ($promoCode->is_global) {
                    $alreadyUsed = $promoCode->users()
                        ->where('user_id', $user->id)
                        ->where('is_used', true)
                        ->exists();
                } else {
                    $alreadyUsed = $promoCode->users()
                        ->where('user_id', $user->id)
                        ->exists();
                }

                if ($alreadyUsed) {
                    throw new \Exception('Vous avez déjà utilisé ce code promo');
                }

                $rewards = $this->processRewards($promoCode, $user->id);

                if ($promoCode->is_global) {
                    $promoCode->users()->attach($user->id, [
                        'is_used' => true,
                        'used_at' => now()
                    ]);
                } else {
                    $promoCode->users()->attach($user->id, [
                        'is_used' => false,
                        'used_at' => null
                    ]);
                }

                $promoCode->increment('current_uses');

                return $rewards;
            });

            return redirect()->route('promocodes.index')->with('flash', [
                'type' => 'success',
                'title' => 'Félicitations !',
                'message' => 'Vos récompenses ont été ajoutées à votre compte.',
                'rewards' => $rewards
            ]);
        } catch (\Exception $e) {
            return redirect()->route('promocodes.index')->with('flash', [
                'type' => 'error',
                'title' => 'Erreur',
                'message' => $e->getMessage()
            ]);
        }
    }

    private function processRewards(PromoCode $promoCode, int $userId)
    {
        $user = User::findOrFail($userId);
        $rewards = [
            'cash' => $promoCode->cash,
            'items' => []
        ];

        if ($promoCode->cash > 0) {
            $user->cash += $promoCode->cash;
            $user->save();
        }

        foreach ($promoCode->items as $item) {
            $quantity = $item->pivot->quantity;

            $rewards['items'][] = [
                'id' => $item->id,
                'name' => $item->name,
                'description' => $item->description,
                'type' => $item->type,
                'rarity' => $item->rarity,
                'image_url' => $item->image_url,
                'effect' => $item->effect,
                'price' => $item->price,
                'quantity' => $quantity
            ];

            $inventoryItem = Inventory::firstOrNew([
                'user_id' => $userId,
                'item_id' => $item->id
            ]);

            if ($inventoryItem->exists) {
                $inventoryItem->quantity += $quantity;
            } else {
                $inventoryItem->quantity = $quantity;
            }

            $inventoryItem->save();
        }

        return $rewards;
    }

    public function destroy($id)
    {
        try {
            $promoCode = PromoCode::findOrFail($id);
            $promoCode->delete();

            return response()->json(['message' => 'Promo code deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Promo code not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error deleting promo code', 'error' => $e->getMessage()], 500);
        }
    }
}
