<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserDailyQuestProgress extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'daily_quest_id',
        'date',
        'current_count',
        'is_completed',
        'is_claimed',
        'completed_at',
        'claimed_at'
    ];

    protected $casts = [
        'date' => 'date',
        'is_completed' => 'boolean',
        'is_claimed' => 'boolean',
        'completed_at' => 'datetime',
        'claimed_at' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function dailyQuest(): BelongsTo
    {
        return $this->belongsTo(DailyQuest::class);
    }

    public function scopeToday($query)
    {
        return $query->where('date', today());
    }

    public function canBeClaimed(): bool
    {
        return $this->is_completed && !$this->is_claimed;
    }
}
