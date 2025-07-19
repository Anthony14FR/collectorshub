<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\PromoCode;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CrudPromoCodeController extends Controller
{
    public function index()
    {
        $promoCodes = PromoCode::with(['items', 'users'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return Inertia::render('Admin/PromoCodes/Index', [
            'promoCodes' => $promoCodes
        ]);
    }

    public function create()
    {
        $users = User::select('id', 'username', 'email')->orderBy('username')->get();
        $items = Item::select('id', 'name', 'type', 'rarity')->orderBy('name')->get();

        return Inertia::render('Admin/PromoCodes/Create', [
            'users' => $users,
            'items' => $items
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:promo_codes,code',
            'is_global' => 'required|boolean',
            'is_active' => 'required|boolean',
            'cash' => 'required|integer|min:0',
            'expires_at' => 'nullable|date|after:now',
            'target_user_ids' => 'required_if:is_global,false|array',
            'target_user_ids.*' => 'exists:users,id',
            'items' => 'array',
            'items.*.id' => 'exists:items,id',
            'items.*.quantity' => 'integer|min:1'
        ]);

        $promoCode = PromoCode::create([
            'code' => strtoupper($validated['code']),
            'is_global' => $validated['is_global'],
            'is_active' => $validated['is_active'],
            'cash' => $validated['cash'],
            'expires_at' => $validated['expires_at']
        ]);

        if (!$validated['is_global'] && !empty($validated['target_user_ids'])) {
            $userData = [];
            foreach ($validated['target_user_ids'] as $userId) {
                $userData[$userId] = ['is_used' => false];
            }
            $promoCode->users()->attach($userData);
        }

        if (!empty($validated['items'])) {
            $itemData = [];
            foreach ($validated['items'] as $item) {
                $itemData[$item['id']] = ['quantity' => $item['quantity']];
            }
            $promoCode->items()->attach($itemData);
        }

        return redirect()->route('admin.promocodes.index')
            ->with('success', 'Code promotionnel créé avec succès');
    }

    public function show($id)
    {
        $promoCode = PromoCode::with(['items', 'users'])->findOrFail($id);

        if ($promoCode->is_global) {
            $usageStats = [
                'total_users' => User::count(),
                'used_count' => 0,
                'unused_count' => User::count(),
                'last_used' => null
            ];
        } else {
            $usageStats = [
                'total_users' => $promoCode->users()->count(),
                'used_count' => $promoCode->users()->wherePivot('is_used', true)->count(),
                'unused_count' => $promoCode->users()->wherePivot('is_used', false)->count(),
                'last_used' => $promoCode->users()->wherePivot('is_used', true)->max('promo_code_users.used_at')
            ];
        }

        return Inertia::render('Admin/PromoCodes/Show', [
            'promoCode' => $promoCode,
            'usageStats' => $usageStats
        ]);
    }

    public function edit($id)
    {
        $promoCode = PromoCode::with(['items', 'users'])->findOrFail($id);
        $users = User::select('id', 'username', 'email')->orderBy('username')->get();
        $items = Item::select('id', 'name', 'type', 'rarity')->orderBy('name')->get();

        return Inertia::render('Admin/PromoCodes/Edit', [
            'promoCode' => $promoCode,
            'users' => $users,
            'items' => $items
        ]);
    }

    public function update(Request $request, $id)
    {
        $promoCode = PromoCode::findOrFail($id);

        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:promo_codes,code,' . $promoCode->id,
            'is_global' => 'required|boolean',
            'is_active' => 'required|boolean',
            'cash' => 'required|integer|min:0',
            'expires_at' => 'nullable|date|after:now',
            'target_user_ids' => 'required_if:is_global,false|array',
            'target_user_ids.*' => 'exists:users,id',
            'items' => 'array',
            'items.*.id' => 'exists:items,id',
            'items.*.quantity' => 'integer|min:1'
        ]);

        $promoCode->update([
            'code' => strtoupper($validated['code']),
            'is_global' => $validated['is_global'],
            'is_active' => $validated['is_active'],
            'cash' => $validated['cash'],
            'expires_at' => $validated['expires_at']
        ]);

        $promoCode->items()->detach();
        if (!empty($validated['items'])) {
            $itemData = [];
            foreach ($validated['items'] as $item) {
                $itemData[$item['id']] = ['quantity' => $item['quantity']];
            }
            $promoCode->items()->attach($itemData);
        }

        if ($validated['is_global']) {
            $promoCode->users()->detach();
        } else {
            $existingUsers = $promoCode->users()->pluck('users.id')->toArray();
            $newUsers = $validated['target_user_ids'];

            $usersToRemove = array_diff($existingUsers, $newUsers);
            $usersToAdd = array_diff($newUsers, $existingUsers);

            if (!empty($usersToRemove)) {
                $promoCode->users()->detach($usersToRemove);
            }

            if (!empty($usersToAdd)) {
                $userData = [];
                foreach ($usersToAdd as $userId) {
                    $userData[$userId] = ['is_used' => false];
                }
                $promoCode->users()->attach($userData);
            }
        }

        return redirect()->route('admin.promocodes.index')
            ->with('success', 'Code promotionnel mis à jour avec succès');
    }

    public function destroy($id)
    {
        $promoCode = PromoCode::findOrFail($id);
        $promoCode->users()->detach();
        $promoCode->items()->detach();
        $promoCode->delete();

        return redirect()->route('admin.promocodes.index')
            ->with('success', 'Code promotionnel supprimé avec succès');
    }
}
