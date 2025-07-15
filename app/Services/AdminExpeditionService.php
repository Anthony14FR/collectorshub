<?php

namespace App\Services;

use App\Models\Expedition;
use App\Models\ExpeditionRequirement;
use App\Models\Item;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class AdminExpeditionService
{
    public function getExpeditionsWithPagination(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Expedition::with(['requirements', 'userExpeditions']);

        if (!empty($filters['rarity'])) {
            $query->where('rarity', $filters['rarity']);
        }

        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $query->where('is_active', $filters['is_active'] === 'true');
        }

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $searchTerm = strtolower($filters['search']);
                $q->whereRaw('LOWER(name) LIKE ?', ['%' . $searchTerm . '%'])
                  ->orWhereRaw('LOWER(description) LIKE ?', ['%' . $searchTerm . '%']);
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function getExpeditionWithDetails(Expedition $expedition): Expedition
    {
        return $expedition->load(['requirements', 'userExpeditions.user']);
    }

    public function createExpedition(array $data): Expedition
    {
        return DB::transaction(function () use ($data) {
            $expedition = Expedition::create([
                'name' => $data['name'],
                'description' => $data['description'],
                'rarity' => $data['rarity'],
                'duration_minutes' => $data['duration_minutes'],
                'rewards' => $data['rewards'],
                'is_active' => $data['is_active'] ?? true
            ]);

            foreach ($data['requirements'] as $requirement) {
                ExpeditionRequirement::create([
                    'expedition_id' => $expedition->id,
                    'type' => $requirement['type'],
                    'value' => $requirement['value'],
                    'quantity' => $requirement['quantity']
                ]);
            }

            return $expedition->load('requirements');
        });
    }

    public function updateExpedition(Expedition $expedition, array $data): Expedition
    {
        return DB::transaction(function () use ($expedition, $data) {
            $expedition->update([
                'name' => $data['name'],
                'description' => $data['description'],
                'rarity' => $data['rarity'],
                'duration_minutes' => $data['duration_minutes'],
                'rewards' => $data['rewards'],
                'is_active' => $data['is_active'] ?? true
            ]);

            $expedition->requirements()->delete();

            foreach ($data['requirements'] as $requirement) {
                ExpeditionRequirement::create([
                    'expedition_id' => $expedition->id,
                    'type' => $requirement['type'],
                    'value' => $requirement['value'],
                    'quantity' => $requirement['quantity']
                ]);
            }

            return $expedition->load('requirements');
        });
    }

    public function deleteExpedition(Expedition $expedition): bool
    {
        return DB::transaction(function () use ($expedition) {
            if ($expedition->userExpeditions()->exists()) {
                return false;
            }

            $expedition->requirements()->delete();
            $expedition->delete();

            return true;
        });
    }

    public function toggleExpeditionStatus(Expedition $expedition): Expedition
    {
        $expedition->update(['is_active' => !$expedition->is_active]);
        return $expedition;
    }

    public function getExpeditionStats(): array
    {
        return [
            'total' => Expedition::count(),
            'active' => Expedition::where('is_active', true)->count(),
            'inactive' => Expedition::where('is_active', false)->count(),
            'by_rarity' => Expedition::select('rarity', DB::raw('count(*) as count'))
                ->groupBy('rarity')
                ->pluck('count', 'rarity')
                ->toArray()
        ];
    }

    public function getAvailableItems(): Collection
    {
        return Item::select('id', 'name', 'image')->get();
    }

    public function getAvailableTypes(): array
    {
        return DB::table('pokemon')
            ->select('types')
            ->distinct()
            ->get()
            ->flatMap(function ($pokemon) {
                $types = json_decode($pokemon->types, true);
                return collect($types)->map(function ($type) {
                    return is_array($type) ? $type['name'] : $type;
                });
            })
            ->unique()
            ->sort()
            ->values()
            ->toArray();
    }

    public function getAvailableRarities(): array
    {
        return ['normal', 'rare', 'epic', 'legendary'];
    }

    public function getRewardTypes(): array
    {
        return ['cash', 'xp', 'pokeball', 'masterball', 'item'];
    }

    public function getRequirementTypes(): array
    {
        return ['rarity', 'type'];
    }

    public function canDeleteExpedition(Expedition $expedition): bool
    {
        return !$expedition->userExpeditions()->exists();
    }

    public function duplicateExpedition(Expedition $expedition): Expedition
    {
        return DB::transaction(function () use ($expedition) {
            $newExpedition = Expedition::create([
                'name' => $expedition->name . ' (Copie)',
                'description' => $expedition->description,
                'rarity' => $expedition->rarity,
                'duration_minutes' => $expedition->duration_minutes,
                'rewards' => $expedition->rewards,
                'is_active' => false
            ]);

            foreach ($expedition->requirements as $requirement) {
                ExpeditionRequirement::create([
                    'expedition_id' => $newExpedition->id,
                    'type' => $requirement->type,
                    'value' => $requirement->value,
                    'quantity' => $requirement->quantity
                ]);
            }

            return $newExpedition->load('requirements');
        });
    }
}
