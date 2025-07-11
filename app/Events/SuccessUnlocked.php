<?php

namespace App\Events;

use App\Models\User;
use App\Models\Success;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SuccessUnlocked implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $success;

    public function __construct(User $user, Success $success)
    {
        $this->user = $user;
        $this->success = $success;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('user.' . $this->user->id);
    }

    public function broadcastWith()
    {
        return [
            'success' => $this->success,
            'message' => 'Nouveau badge débloqué : ' . $this->success->title,
            'unclaimed_count' => $this->user->getUnclaimedCount()
        ];
    }
}
