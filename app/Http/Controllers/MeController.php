<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class MeController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $pokedex = $user->pokedex()->with('pokemon')->get();
        $inventory = $user->inventory()->with('item')->get();

        return Inertia::render('Me', [
            'user' => $user,
            'pokedex' => $pokedex,
            'inventory' => $inventory,
        ]);
    }
}
