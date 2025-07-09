<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inventory extends Model
{
    use HasFactory;

    protected $table = 'inventory';

    protected $fillable = [
        'user_id',
        'item_id',
        'quantity'
    ];

    protected $casts = [
        'quantity' => 'integer'
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($inventory) {
            if ($inventory->quantity < 0) {
                throw new \Exception('La quantité ne peut pas être négative');
            }
            if ($inventory->quantity > 999) {
                throw new \Exception('La quantité ne peut pas dépasser 999');
            }

            // Vérification de l'unicité user_id/item_id
            $exists = static::where('user_id', $inventory->user_id)
                ->where('item_id', $inventory->item_id)
                ->where('id', '!=', $inventory->id)
                ->exists();

            if ($exists) {
                throw new \Exception('Cet item est déjà dans l\'inventaire de l\'utilisateur');
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
