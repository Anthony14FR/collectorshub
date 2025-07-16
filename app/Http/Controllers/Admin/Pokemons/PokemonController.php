<?php

namespace App\Http\Controllers\Admin\Pokemons;

use App\Http\Controllers\Controller;
use App\Models\Pokemon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PokemonController extends Controller
{
    private function getValidationRules(bool $isUpdate = false): array
    {
        $rules = [
            'pokedex_id' => 'required|integer' . ($isUpdate ? '' : '|unique:pokemon,pokedex_id'),
            'name' => 'required|string|max:50',
            'types' => 'required|array',
            'types.*.name' => 'required|string',
            'types.*.image' => 'required|string',
            'resistances' => 'required|array',
            'resistances.*.name' => 'required|string',
            'resistances.*.damage_multiplier' => 'required|numeric',
            'resistances.*.damage_relation' => 'required|in:' . implode(',', Pokemon::DAMAGE_RELATIONS),
            'evolution_id' => 'nullable',
            'pre_evolution_id' => 'nullable',
            'description' => 'required|string|max:250',
            'height' => 'required|integer|min:1',
            'weight' => 'required|integer|min:1',
            'rarity' => 'required|in:' . implode(',', Pokemon::RARITIES),
            'is_shiny' => 'required|boolean',
            'hp' => 'required|integer|min:1|max:255',
            'attack' => 'required|integer|min:1|max:255',
            'defense' => 'required|integer|min:1|max:255',
            'speed' => 'required|integer|min:1|max:255',
            'special_attack' => 'required|integer|min:1|max:255',
            'special_defense' => 'required|integer|min:1|max:255',
            'generation' => 'nullable|integer|min:1|max:9',
        ];

        if (!$isUpdate) {
            $rules['image'] = 'required|image|mimes:gif|max:2048';
        } else {
            $rules['image'] = 'nullable|image|mimes:gif|max:2048';
        }

        return $rules;
    }

    private function processRequestData(array $data): array
    {
        if (is_string($data['types'])) {
            $data['types'] = json_decode($data['types'], true);
        }

        if (is_string($data['resistances'])) {
            $data['resistances'] = json_decode($data['resistances'], true);
        }

        if (isset($data['is_shiny']) && is_string($data['is_shiny'])) {
            $data['is_shiny'] = $data['is_shiny'] === 'true';
        }

        $nullableFields = ['evolution_id', 'pre_evolution_id', 'generation'];
        foreach ($nullableFields as $field) {
            if (isset($data[$field]) && $data[$field] === '') {
                $data[$field] = null;
            }
        }

        return $data;
    }

    private function handleImageUpload(Request $request, int $pokedexId): void
    {
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = $pokedexId . '.gif';
            $path = public_path('images/pokemon-gifs/' . $filename);

            if (file_exists($path)) {
                unlink($path);
            }

            $file->move(public_path('images/pokemon-gifs'), $filename);
        }
    }

    public function index(Request $request)
    {
        $filters = $request->only(['rarity', 'search', 'type', 'shiny']);
        $sort = $request->only(['sort_by', 'sort_direction']);

        $query = Pokemon::query();

        if (!empty($filters['rarity'])) {
            $query->where('rarity', $filters['rarity']);
        }

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('pokedex_id', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (!empty($filters['type'])) {
            $query->whereRaw("types LIKE ?", ['%"name":"' . $filters['type'] . '"%']);
        }

        if (isset($filters['shiny']) && $filters['shiny'] !== '') {
            $query->where('is_shiny', $filters['shiny'] === 'true');
        }

        $sortBy = $sort['sort_by'] ?? 'pokedex_id';
        $sortDirection = $sort['sort_direction'] ?? 'asc';

        $allowedSortFields = ['pokedex_id', 'name', 'rarity', 'generation', 'hp', 'attack', 'defense', 'speed', 'special_attack', 'special_defense'];

        if (in_array($sortBy, $allowedSortFields)) {
            if ($sortBy === 'total_stats') {
                $query->orderByRaw('(hp + attack + defense + speed + special_attack + special_defense) ' . $sortDirection);
            } else {
                $query->orderBy($sortBy, $sortDirection);
            }
        } else {
            $query->orderBy('pokedex_id', 'asc');
        }

        $pokemons = $query->paginate(30);

        $stats = [
            'total' => Pokemon::count(),
            'filtered' => $query->count(),
            'by_rarity' => Pokemon::selectRaw('rarity, count(*) as count')->groupBy('rarity')->pluck('count', 'rarity'),
            'by_generation' => Pokemon::selectRaw('generation, count(*) as count')->groupBy('generation')->pluck('count', 'generation'),
            'shiny_count' => Pokemon::where('is_shiny', true)->count(),
            'avg_stats' => Pokemon::selectRaw('AVG(hp + attack + defense + speed + special_attack + special_defense) as avg_total')->first()->avg_total ?? 0
        ];

        return Inertia::render('Admin/Pokemons/Index', [
            'pokemons' => $pokemons,
            'stats' => $stats,
            'filters' => $filters,
            'sort' => $sort,
            'rarities' => Pokemon::RARITIES,
            'types' => Pokemon::TYPES
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Pokemons/Create', [
            'rarities' => Pokemon::RARITIES,
            'generations' => Pokemon::GENERATIONS,
            'types' => Pokemon::TYPES,
            'damageRelations' => Pokemon::DAMAGE_RELATIONS
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->processRequestData($request->all());
        $validated = validator($data, $this->getValidationRules())->validate();

        $this->handleImageUpload($request, $validated['pokedex_id']);

        try {
            $validated['id'] = $validated['pokedex_id'];
            Pokemon::create($validated);

            return redirect()->route('admin.pokemons.index')
                ->with('success', 'Pokémon créé avec succès');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de la création : ' . $e->getMessage()]);
        }
    }

    public function show(Pokemon $pokemon)
    {
        $pokemon->load(['evolution', 'preEvolution']);

        $evolutions = collect();
        $preEvolutions = collect();

        $currentPokemon = $pokemon;
        while ($currentPokemon->evolution) {
            $evolutions->push($currentPokemon->evolution);
            $currentPokemon = $currentPokemon->evolution;
        }

        $currentPokemon = $pokemon;
        while ($currentPokemon->preEvolution) {
            $preEvolutions->prepend($currentPokemon->preEvolution);
            $currentPokemon = $currentPokemon->preEvolution;
        }

        return Inertia::render('Admin/Pokemons/Show', [
            'pokemon' => $pokemon,
            'evolutions' => $evolutions,
            'preEvolutions' => $preEvolutions
        ]);
    }

    public function edit(Pokemon $pokemon)
    {
        $pokemon->load(['evolution', 'preEvolution']);

        return Inertia::render('Admin/Pokemons/Edit', [
            'pokemon' => $pokemon,
            'rarities' => Pokemon::RARITIES,
            'generations' => Pokemon::GENERATIONS,
            'types' => Pokemon::TYPES,
            'damageRelations' => Pokemon::DAMAGE_RELATIONS,
            'availablePokemons' => Pokemon::select('id', 'name', 'pokedex_id')->orderBy('pokedex_id')->get()
        ]);
    }

    public function update(Request $request, Pokemon $pokemon)
    {
        $data = $this->processRequestData($request->all());
        $validated = validator($data, $this->getValidationRules(true))->validate();

        $this->handleImageUpload($request, $validated['pokedex_id']);

        try {
            $pokemon->update($validated);

            return redirect()->route('admin.pokemons.index')
                ->with('success', 'Pokémon mis à jour avec succès');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de la mise à jour : ' . $e->getMessage()]);
        }
    }

    public function destroy(Pokemon $pokemon)
    {
        try {
            $pokemon->delete();

            return redirect()->route('admin.pokemons.index')
                ->with('success', 'Pokémon supprimé avec succès');
        } catch (\Exception $e) {
            return redirect()->route('admin.pokemons.index')
                ->with('error', 'Erreur lors de la suppression : ' . $e->getMessage());
        }
    }
}
