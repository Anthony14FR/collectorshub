<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Expedition extends Model
{
    protected $fillable = [
        'name',
        'description',
        'rarity',
        'duration_minutes',
        'rewards',
        'is_active'
    ];

    protected $casts = [
        'rewards' => 'array',
        'is_active' => 'boolean'
    ];

    public function requirements(): HasMany
    {
        return $this->hasMany(ExpeditionRequirement::class);
    }

    public function expeditionPokemons(): HasMany
    {
        return $this->hasMany(ExpeditionPokemon::class);
    }

    public function userExpeditions(): HasMany
    {
        return $this->hasMany(UserExpedition::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_expeditions')
            ->withPivot('date', 'status', 'started_at', 'ends_at', 'claimed_at')
            ->withTimestamps();
    }

    public function getTotalPokemonsRequired(): int
    {
        return $this->requirements->sum('quantity');
    }

    public function canUserParticipate(User $user, $selectedPokemons): bool
    {
        foreach ($this->requirements as $requirement) {
            if (!$this->checkRequirement($requirement, $selectedPokemons)) {
                return false;
            }
        }
        return true;
    }

    private function checkRequirement(ExpeditionRequirement $requirement, $selectedPokemons): bool
    {
        $count = 0;

        foreach ($selectedPokemons as $pokedexEntry) {
            $pokemon = $pokedexEntry->pokemon;

            switch ($requirement->type) {
                case 'rarity':
                    if ($pokemon->rarity === $requirement->value) {
                        $count++;
                    }
                    break;
                case 'type':
                    $types = is_array($pokemon->types) ? $pokemon->types : json_decode($pokemon->types, true);
                    foreach ($types as $type) {
                        $typeName = is_array($type) ? $type['name'] : $type;
                        if (strtolower($typeName) === strtolower($requirement->value)) {
                            $count++;
                            break;
                        }
                    }
                    break;
            }
        }

        return $count >= $requirement->quantity;
    }
}
