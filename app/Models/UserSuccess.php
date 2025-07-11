<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSuccess extends Model
{
    protected $fillable = [
        'user_id',
        'success_id',
        'unlocked_at',
        'is_claimed',
        'claimed_at'
    ];

    protected $casts = [
        'unlocked_at' => 'datetime',
        'claimed_at' => 'datetime',
        'is_claimed' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function success()
    {
        return $this->belongsTo(Success::class);
    }

    public function claim()
    {
        $this->update([
            'is_claimed' => true,
            'claimed_at' => now()
        ]);
    }

    public function scopeUnclaimed($query)
    {
        return $query->where('is_claimed', false);
    }

    public function scopeClaimed($query)
    {
        return $query->where('is_claimed', true);
    }
}
