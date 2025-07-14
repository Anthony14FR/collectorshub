<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
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
}
