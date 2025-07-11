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
}
