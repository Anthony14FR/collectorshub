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

        return Inertia::render('Me', [
            'pokedex' => $pokedex,
        ]);
    }
}
