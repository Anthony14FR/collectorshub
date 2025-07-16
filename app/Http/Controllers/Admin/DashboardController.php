<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        if (!is_admin()) {
            abort(403, 'Accès non autorisé');
        }

        $stats = [
            'totalUsers' => User::count(),
            'activeUsers' => User::where('status', 'active')->count(),
            'premiumUsers' => User::where('role', 'premium')->count(),
            'onlineUsers' => User::where('last_login', '>=', now()->subMinutes(15))->count(),
            'suspendedUsers' => User::where('status', 'suspended')->count(),
            'totalListings' => 234,
            'totalTransactions' => 1567,
            'reports' => 3,
            'totalPokemon' => 15483,
            'shinyPokemon' => 892,
            'legendaryPokemon' => 145,
            'errors' => 2,
            'warnings' => 8,
            'connections' => 2341,
        ];

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats
        ]);
    }

    public function clearCache(Request $request)
    {
        if (!is_admin()) {
            return response()->json([
                'success' => false,
                'message' => 'Accès non autorisé'
            ], 403);
        }

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
