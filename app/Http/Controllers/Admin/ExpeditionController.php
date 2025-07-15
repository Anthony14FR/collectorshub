<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ExpeditionStoreRequest;
use App\Http\Requests\Admin\ExpeditionUpdateRequest;
use App\Models\Expedition;
use App\Services\AdminExpeditionService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ExpeditionController extends Controller
{
    protected AdminExpeditionService $adminExpeditionService;

    public function __construct(AdminExpeditionService $adminExpeditionService)
    {
        $this->adminExpeditionService = $adminExpeditionService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['rarity', 'is_active', 'search']);
        $expeditions = $this->adminExpeditionService->getExpeditionsWithPagination(30, $filters);
        $stats = $this->adminExpeditionService->getExpeditionStats();

        return Inertia::render('Admin/Expeditions/Index', [
            'expeditions' => $expeditions,
            'stats' => $stats,
            'filters' => $filters,
            'rarities' => $this->adminExpeditionService->getAvailableRarities()
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Expeditions/Create', [
            'rarities' => $this->adminExpeditionService->getAvailableRarities(),
            'availableTypes' => $this->adminExpeditionService->getAvailableTypes(),
            'items' => $this->adminExpeditionService->getAvailableItems(),
            'rewardTypes' => $this->adminExpeditionService->getRewardTypes(),
            'requirementTypes' => $this->adminExpeditionService->getRequirementTypes()
        ]);
    }

    public function store(ExpeditionStoreRequest $request)
    {
        try {
            $expedition = $this->adminExpeditionService->createExpedition($request->validated());

            return redirect()->route('admin.expeditions.index')
                ->with('success', 'Expédition créée avec succès');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de la création : ' . $e->getMessage());
        }
    }

    public function show(Expedition $expedition)
    {
        $expedition = $this->adminExpeditionService->getExpeditionWithDetails($expedition);

        return Inertia::render('Admin/Expeditions/Show', [
            'expedition' => $expedition,
            'items' => $this->adminExpeditionService->getAvailableItems()
        ]);
    }

    public function edit(Expedition $expedition)
    {
        $expedition = $this->adminExpeditionService->getExpeditionWithDetails($expedition);

        return Inertia::render('Admin/Expeditions/Edit', [
            'expedition' => $expedition,
            'rarities' => $this->adminExpeditionService->getAvailableRarities(),
            'availableTypes' => $this->adminExpeditionService->getAvailableTypes(),
            'items' => $this->adminExpeditionService->getAvailableItems(),
            'rewardTypes' => $this->adminExpeditionService->getRewardTypes(),
            'requirementTypes' => $this->adminExpeditionService->getRequirementTypes()
        ]);
    }

    public function update(ExpeditionUpdateRequest $request, Expedition $expedition)
    {
        try {
            $this->adminExpeditionService->updateExpedition($expedition, $request->validated());

            return redirect()->route('admin.expeditions.index')
                ->with('success', 'Expédition mise à jour avec succès');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de la mise à jour : ' . $e->getMessage());
        }
    }

    public function destroy(Expedition $expedition)
    {
        try {
            $deleted = $this->adminExpeditionService->deleteExpedition($expedition);

            if (!$deleted) {
                return redirect()->route('admin.expeditions.index')
                    ->with('error', 'Impossible de supprimer cette expédition car elle est utilisée par des joueurs');
            }

            return redirect()->route('admin.expeditions.index')
                ->with('success', 'Expédition supprimée avec succès');
        } catch (\Exception $e) {
            return redirect()->route('admin.expeditions.index')
                ->with('error', 'Erreur lors de la suppression : ' . $e->getMessage());
        }
    }

    public function toggle(Expedition $expedition)
    {
        try {
            $this->adminExpeditionService->toggleExpeditionStatus($expedition);

            $status = $expedition->is_active ? 'activée' : 'désactivée';
            return redirect()->route('admin.expeditions.index')
                ->with('success', "Expédition {$status} avec succès");
        } catch (\Exception $e) {
            return redirect()->route('admin.expeditions.index')
                ->with('error', 'Erreur lors du changement de statut : ' . $e->getMessage());
        }
    }

    public function duplicate(Expedition $expedition)
    {
        try {
            $newExpedition = $this->adminExpeditionService->duplicateExpedition($expedition);

            return redirect()->route('admin.expeditions.index')
                ->with('success', 'Expédition dupliquée avec succès');
        } catch (\Exception $e) {
            return redirect()->route('admin.expeditions.index')
                ->with('error', 'Erreur lors de la duplication : ' . $e->getMessage());
        }
    }
}
