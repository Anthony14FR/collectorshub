<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\Expedition;
use App\Models\Item;
use App\Models\Pokemon;
use App\Models\PromoCode;
use App\Models\Success;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_pokemons' => Pokemon::count(),
            'total_expeditions' => Expedition::count(),
            'totalPromoCodes' => PromoCode::count(),
            'totalSuccess' => Success::count(),
            'shop_items' => Item::count(),
            'clubsCount' => Club::count(),
        ];

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats
        ]);
    }

    public function clearCache(Request $request)
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');

            return response()->json([
                'success' => true,
                'message' => 'Cache vidé avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du vidage du cache'
            ], 500);
        }
    }
}
