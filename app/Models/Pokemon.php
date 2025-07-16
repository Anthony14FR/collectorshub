<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pokemon extends Model
{
    public $incrementing = false;
    protected $keyType = 'integer';

    public const RARITIES = ['normal', 'rare', 'epic', 'legendary'];
    public const RARITY_LABELS = [
        'normal' => 'Normal',
        'rare' => 'Rare',
        'epic' => 'Épique',
        'legendary' => 'Légendaire'
    ];

    public const TYPES = [
        'Normal', 'Feu', 'Eau', 'Électrik', 'Plante', 'Glace', 'Combat', 'Poison',
        'Sol', 'Vol', 'Psy', 'Insecte', 'Roche', 'Spectre', 'Dragon', 'Ténèbres', 'Acier', 'Fée'
    ];

    public const DAMAGE_RELATIONS = ['neutral', 'resistant', 'vulnerable', 'twice_resistant'];
    public const DAMAGE_RELATION_LABELS = [
        'neutral' => 'Neutre',
        'resistant' => 'Résistant',
        'vulnerable' => 'Vulnérable',
        'twice_resistant' => 'Double Résistant'
    ];

    public const GENERATIONS = [1, 2, 3, 4, 5, 6, 7, 8, 9];

    protected $fillable = [
        'id',
        'pokedex_id',
        'name',
        'types',
        'resistances',
        'evolution_id',
        'pre_evolution_id',
        'description',
        'height',
        'weight',
        'rarity',
        'is_shiny',
        'hp',
        'attack',
        'defense',
        'speed',
        'special_attack',
        'special_defense',
        'generation'
    ];

    protected $casts = [
        'types' => 'array',
        'resistances' => 'array',
        'is_shiny' => 'boolean',
        'height' => 'integer',
        'weight' => 'integer',
        'hp' => 'integer',
        'attack' => 'integer',
        'defense' => 'integer',
        'speed' => 'integer',
        'special_attack' => 'integer',
        'special_defense' => 'integer',
        'generation' => 'integer'
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($pokemon) {
            // Validation des champs
            if (strlen($pokemon->name) > 50) {
                throw new \Exception('Le nom ne peut pas dépasser 50 caractères');
            }
            if (strlen($pokemon->description) > 250) {
                throw new \Exception('La description ne peut pas dépasser 250 caractères');
            }
            if (!in_array($pokemon->rarity, ['normal', 'rare', 'epic', 'legendary'])) {
                throw new \Exception('Rareté invalide');
            }

            // Validation des types
            if (!is_array($pokemon->types)) {
                throw new \Exception('Les types doivent être un tableau');
            }
            foreach ($pokemon->types as $type) {
                if (!isset($type['name']) || !isset($type['image'])) {
                    throw new \Exception('Format de type invalide');
                }
            }

            // Validation des résistances
            if (!is_array($pokemon->resistances)) {
                throw new \Exception('Les résistances doivent être un tableau');
            }
            foreach ($pokemon->resistances as $resistance) {
                if (!isset($resistance['name']) || !isset($resistance['damage_multiplier']) || !isset($resistance['damage_relation'])) {
                    throw new \Exception('Format de résistance invalide');
                }
                if (!in_array($resistance['damage_relation'], ['neutral', 'resistant', 'vulnerable', 'twice_resistant'])) {
                    throw new \Exception('Relation de dégâts invalide');
                }
            }

            // Validation des statistiques
            $stats = ['hp', 'attack', 'defense', 'speed', 'special_attack', 'special_defense'];
            foreach ($stats as $stat) {
                if ($pokemon->$stat < 1 || $pokemon->$stat > 255) {
                    throw new \Exception("La statistique $stat doit être entre 1 et 255");
                }
            }

            // Validation de la génération
            if ($pokemon->generation && ($pokemon->generation < 1 || $pokemon->generation > 9)) {
                throw new \Exception('La génération doit être entre 1 et 9');
            }
        });
    }

    public function pokedex(): HasMany
    {
        return $this->hasMany(Pokedex::class);
    }

    public function marketplace(): HasMany
    {
        return $this->hasMany(Marketplace::class);
    }

    public function evolution(): HasOne
    {
        return $this->hasOne(Pokemon::class, 'id', 'evolution_id');
    }

    public function preEvolution(): HasOne
    {
        return $this->hasOne(Pokemon::class, 'id', 'pre_evolution_id');
    }

    public function getRarityLabel(): string
    {
        return self::RARITY_LABELS[$this->rarity] ?? $this->rarity;
    }

    public function getTotalStats(): int
    {
        return $this->hp + $this->attack + $this->defense + $this->speed + $this->special_attack + $this->special_defense;
    }

    public static function getTypeImage(string $type): string
    {
        return "/images/types/{$type}.png";
    }
}
