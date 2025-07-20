<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $query = Item::query();

        if ($request->has('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        $perPage = $request->get('per_page', 16);
        $perPage = min(max($perPage, 10), 100);

        $items = $query->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        $stats = [
            'total' => Item::count(),
            'ball' => Item::where('type', 'ball')->count(),
            'avatar' => Item::where('type', 'avatar')->count(),
            'background' => Item::where('type', 'background')->count(),
        ];

        return Inertia::render('Admin/Items/Index', [
            'items' => $items,
            'stats' => $stats,
            'filters' => $request->only(['type', 'per_page'])
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Items/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'description' => 'nullable|string|max:250',
            'type' => 'required|in:ball,avatar,background',
            'price' => 'required|integer|min:0',
            'rarity' => 'required|in:normal,rare,epic,legendary',
            'image' => 'required|file|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = strtolower(str_replace(' ', '_', $validated['name'])) . '.' . $image->getClientOriginalExtension();
            $path = public_path('images/items/' . $filename);

            if (file_exists($path)) {
                unlink($path);
            }

            $image->move(public_path('images/items'), $filename);
            $imagePath = '/images/items/' . $filename;
        }

        $validated['image'] = $imagePath;
        Item::create($validated);

        return redirect()->route('admin.items.index')
            ->with('success', 'Item créé avec succès');
    }

    public function show($id)
    {
        $item = Item::findOrFail($id);

        return Inertia::render('Admin/Items/Show', [
            'item' => $item
        ]);
    }

    public function edit($id)
    {
        $item = Item::findOrFail($id);

        return Inertia::render('Admin/Items/Edit', [
            'item' => $item
        ]);
    }

    public function update(Request $request, $id)
    {
        $item = Item::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'description' => 'nullable|string|max:250',
            'type' => 'required|in:ball,avatar,background',
            'price' => 'required|integer|min:0',
            'rarity' => 'required|in:normal,rare,epic,legendary',
            'image' => 'nullable|file|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = strtolower(str_replace(' ', '_', $validated['name'])) . '.' . $image->getClientOriginalExtension();
            $path = public_path('images/items/' . $filename);

            if (file_exists($path)) {
                unlink($path);
            }

            $image->move(public_path('images/items'), $filename);
            $validated['image'] = '/images/items/' . $filename;
        }

        $item->update($validated);

        return redirect()->route('admin.items.index')
            ->with('success', 'Item mis à jour avec succès');
    }

    public function destroy(Request $request, $id)
    {
        $item = Item::findOrFail($id);
        $item->delete();

        $params = [];
        if ($request->has('type') && $request->type !== 'all') {
            $params['type'] = $request->type;
        }
        if ($request->has('per_page') && $request->per_page != 16) {
            $params['per_page'] = $request->per_page;
        }

        $stats = [
            'total' => Item::count(),
            'ball' => Item::where('type', 'ball')->count(),
            'avatar' => Item::where('type', 'avatar')->count(),
            'background' => Item::where('type', 'background')->count(),
        ];

        return redirect()->route('admin.items.index', $params)
            ->with('success', 'Item supprimé avec succès');
    }
}
