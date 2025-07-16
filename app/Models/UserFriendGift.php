<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFriendGift extends Model
{
    use HasFactory;

    protected $table = 'user_friend_gifts';

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'amount',
        'is_claimed',
        'sent_at',
        'claimed_at',
    ];

    protected $casts = [
        'is_claimed' => 'boolean',
        'sent_at' => 'datetime',
        'claimed_at' => 'datetime',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}
