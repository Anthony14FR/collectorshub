<?php

namespace App\Http\Controllers;

use App\Models\Pokedex;
use App\Services\PokemonUpgradeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PokemonUpgradeController extends Controller
{
    protected PokemonUpgradeService $upgradeService;

    public function __construct(PokemonUpgradeService $upgradeService)
    {
        $this->upgradeService = $upgradeService;
    }

    public function index()
    {
        $user = Auth::user();
        $userPokemons = $user->pokedex()->with('pokemon')->get();
        
        return Inertia::render('PokemonUpgrade/Index', [
            'userPokemons' => $userPokemons
        ]);
    }

    public function getUpgradeRequirements($pokedexId)
    {
        $pokedexEntry = Pokedex::with('pokemon')->findOrFail($pokedexId);
        
        if ($pokedexEntry->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $requirements = $this->upgradeService->getUpgradeRequirements($pokedexEntry);
        $availablePokemons = $this->upgradeService->getAvailablePokemons(Auth::user(), $requirements, $pokedexEntry->id, $pokedexEntry->pokemon->is_shiny);

        return response()->json([
            'requirements' => $requirements,
            'availablePokemons' => $availablePokemons,
            'canUpgrade' => $this->upgradeService->canUpgrade($pokedexEntry, Auth::user())
        ]);
    }

    public function getAvailablePokemonsForSlot(Request $request, $pokedexId)
    {
        $request->validate([
            'requirement_type' => 'required|in:main,secondary',
            'already_selected' => 'array',
            'already_selected.*' => 'integer'
        ]);

        $pokedexEntry = Pokedex::with('pokemon')->findOrFail($pokedexId);
        
        if ($pokedexEntry->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $requirements = $this->upgradeService->getUpgradeRequirements($pokedexEntry);
        $requirementType = $request->input('requirement_type');
        $alreadySelected = $request->input('already_selected', []);
        
        $requirement = $requirementType === 'main' 
            ? $requirements['main_requirement']
            : $requirements['secondary_requirement'];

        if (!$requirement) {
            return response()->json(['available' => []]);
        }

        $available = $this->upgradeService->getAvailablePokemonsForSlot(
            Auth::user(),
            $requirement,
            $pokedexEntry->id,
            $pokedexEntry->pokemon->is_shiny,
            $alreadySelected
        );

        return response()->json(['available' => $available]);
    }

    public function getUpgradablePokemons()
    {
        $user = Auth::user();
        $userPokemons = $user->pokedex()->with('pokemon')->where('star', '<', 6)->get();
        
        $upgradableIds = [];
        $errors = [];
        
        foreach ($userPokemons as $pokemon) {
            try {
                if ($pokemon->star >= 6) {
                    $errors[$pokemon->id] = "Déjà au niveau maximum";
                    continue;
                }
                
                $requirements = $this->upgradeService->getUpgradeRequirements($pokemon);
                
                $canUpgrade = true;
                $availableCounts = [];
                
                foreach ($requirements as $key => $requirement) {
                    $query = $user->pokedex()
                        ->where('id', '!=', $pokemon->id)
                        ->where('star', $requirement['star'])
                        ->whereHas('pokemon', function($q) use ($pokemon) {
                            $q->where('is_shiny', $pokemon->pokemon->is_shiny);
                        });
                    
                    if ($requirement['pokemon_id']) {
                        $query->where('pokemon_id', $requirement['pokemon_id']);
                    }
                    
                    $count = $query->count();
                    $availableCounts[$key] = $count;
                    
                    if ($count < $requirement['quantity']) {
                        $canUpgrade = false;
                        $errors[$pokemon->id] = "Pas assez de ressources pour {$requirement['description']} (disponible: $count/{$requirement['quantity']})";
                        break;
                    }
                }
                
                if ($canUpgrade) {
                    $upgradableIds[] = $pokemon->id;
                }
            } catch (\Exception $e) {
                $errors[$pokemon->id] = $e->getMessage();
            }
        }
        
        return response()->json([
            'upgradableIds' => $upgradableIds,
            'errors' => $errors,
            'debug' => true
        ]);
    }

    public function upgrade(Request $request)
    {
        $request->validate([
            'pokedex_id' => 'required|integer|exists:pokedex,id',
            'materials' => 'required|array'
        ]);

        $pokedexEntry = Pokedex::with('pokemon')->findOrFail($request->pokedex_id);
        
        if ($pokedexEntry->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            DB::beginTransaction();
            
            $result = $this->upgradeService->upgradePokemon($pokedexEntry, $request->materials, Auth::user());
            $upgradedPokemon = $result->load('pokemon');
            
            DB::commit();
            
            return back()->with('success', 'Pokémon amélioré avec succès !');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}