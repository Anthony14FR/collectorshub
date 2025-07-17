<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DailyQuest extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'name',
        'description',
        'target_count',
        'rewards',
        'sort_order',
        'is_active'
    ];

    protected $casts = [
        'rewards' => 'array',
        'is_active' => 'boolean'
    ];

    public function userProgress(): HasMany
    {
        return $this->hasMany(UserDailyQuestProgress::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
