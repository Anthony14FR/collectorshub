<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Success;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SuccessController extends Controller
{
    public function index(Request $request)
    {
        $query = Success::query();
        // Ajout de filtres si besoin (ex: type, catégorie)
        if ($request->has('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }
        $perPage = $request->get('per_page', 16);
        $perPage = min(max($perPage, 10), 100);
        $successes = $query->orderBy('created_at', 'desc')->paginate($perPage)->withQueryString();

        // Statistiques par type si besoin
        $stats = [
            'total' => Success::count(),
            // Ajoute ici d'autres stats par type/catégorie si tu veux
        ];

        return Inertia::render('Admin/Success/Index', [
            'successes' => $successes,
            'stats' => $stats,
            'filters' => $request->only(['type', 'per_page'])
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Success/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:100',
            'description' => 'nullable|string|max:250',
            'type' => 'required|string|max:50',
            'cash_reward' => 'nullable|integer',
            'xp_reward' => 'nullable|integer',
            'requirements' => 'nullable|json',
            'image' => 'nullable|file|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $validated['cash_reward'] = $request->input('cash_reward', 0);
        $validated['xp_reward'] = $request->input('xp_reward', 0);
        $validated['requirements'] = $request->input('requirements') ? json_decode($request->input('requirements'), true) : [];

        // Générer la clé unique à partir du titre
        $key = strtolower(preg_replace('/[^a-z0-9]+/i', '_', $validated['title']));
        // S'assurer que la clé est unique
        $baseKey = $key;
        $i = 2;
        while (\App\Models\Success::where('key', $key)->exists()) {
            $key = $baseKey . '_' . $i;
            $i++;
        }
        $validated['key'] = $key;

        $imagePath = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = strtolower(str_replace(' ', '_', $validated['title'])) . '.' . $image->getClientOriginalExtension();
            $path = public_path('images/badges/' . $filename);
            if (file_exists($path)) {
                unlink($path);
            }
            $image->move(public_path('images/badges'), $filename);
            $imagePath = $filename;
        }
        $validated['image'] = $imagePath;
        $success = \App\Models\Success::create($validated);
        // Mettre à jour la clé avec l'id du badge
        $success->key = (string)$success->id;
        $success->save();
        return redirect()->route('admin.success.index')->with('success', 'Succès créé avec succès');
    }

    public function show($id)
    {
        $success = Success::findOrFail($id);
        return Inertia::render('Admin/Success/Show', [
            'success' => $success
        ]);
    }

    public function edit($id)
    {
        $success = Success::findOrFail($id);
        return Inertia::render('Admin/Success/Edit', [
            'success' => $success
        ]);
    }

    public function update(Request $request, $id)
    {
        $success = Success::findOrFail($id);
        $validated = $request->validate([
            'title' => 'required|string|max:100',
            'description' => 'nullable|string|max:250',
            'type' => 'required|string|max:50',
            'cash_reward' => 'nullable|integer',
            'xp_reward' => 'nullable|integer',
            'requirements' => 'nullable|json',
            'image' => 'nullable|file|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);
        $validated['cash_reward'] = $request->input('cash_reward', 0);
        $validated['xp_reward'] = $request->input('xp_reward', 0);
        $validated['requirements'] = $request->input('requirements') ? json_decode($request->input('requirements'), true) : [];

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = strtolower(str_replace(' ', '_', $validated['title'])) . '.' . $image->getClientOriginalExtension();
            $path = public_path('images/badges/' . $filename);
            if (file_exists($path)) {
                unlink($path);
            }
            $image->move(public_path('images/badges'), $filename);
            $validated['image'] = $filename;
        }
        $success->update($validated);
        // Mettre à jour la clé avec l'id du badge (comme dans le create)
        $success->key = (string)$success->id;
        $success->save();
        return redirect()->route('admin.success.index')->with('success', 'Succès mis à jour avec succès');
    }

    public function destroy(Request $request, $id)
    {
        $success = Success::findOrFail($id);
        $success->delete();
        $params = [];
        if ($request->has('type') && $request->type !== 'all') {
            $params['type'] = $request->type;
        }
        if ($request->has('per_page') && $request->per_page != 16) {
            $params['per_page'] = $request->per_page;
        }
        return redirect()->route('admin.success.index', $params)->with('success', 'Succès supprimé avec succès');
    }
}
