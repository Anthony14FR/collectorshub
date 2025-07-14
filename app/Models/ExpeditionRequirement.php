<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExpeditionRequirement extends Model
{
    protected $fillable = [
        'expedition_id',
        'type',
        'value',
        'quantity'
    ];

    public function expedition(): BelongsTo
    {
        return $this->belongsTo(Expedition::class);
    }
}
