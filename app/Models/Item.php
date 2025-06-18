<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'cost',
        'image_path',
        'type',
        'pokemon_per_ball'
    ];

    protected $casts = [
        'cost' => 'integer',
        'pokemon_per_ball' => 'integer'
    ];

    public function promoCodes(): BelongsToMany
    {
        return $this->belongsToMany(PromoCode::class, 'promo_code_items')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }
} 