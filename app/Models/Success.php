<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Success extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'title',
        'description',
        'image',
        'type',
        'requirements',
        'cash_reward',
        'xp_reward'
    ];

    protected $casts = [
        'requirements' => 'array',
        'cash_reward' => 'integer',
        'xp_reward' => 'integer'
    ];

    protected $appends = ['image_url'];

    public function getImageUrlAttribute(): string
    {
        return $this->image ? '/images/badges/' . $this->image : '/images/badges/default.png';
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_successes')
                    ->withTimestamps()
                    ->withPivot('unlocked_at', 'is_claimed', 'claimed_at');
    }
}
