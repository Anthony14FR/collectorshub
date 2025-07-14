<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserExpedition extends Model
{
    protected $fillable = [
        'user_id',
        'expedition_id',
        'date',
        'status',
        'started_at',
        'ends_at',
        'claimed_at'
    ];

    protected $casts = [
        'date' => 'date',
        'started_at' => 'datetime',
        'ends_at' => 'datetime',
        'claimed_at' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function expedition(): BelongsTo
    {
        return $this->belongsTo(Expedition::class);
    }

    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }

    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function canBeClaimed(): bool
    {
        return $this->status === 'in_progress' && $this->ends_at <= now();
    }
}
