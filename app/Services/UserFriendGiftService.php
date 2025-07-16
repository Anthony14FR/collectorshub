<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserFriendGift;
use Illuminate\Support\Facades\DB;

class UserFriendGiftService
{
    public function sendGift(User $sender, User $receiver, int $amount = 100): bool
    {
        if ($sender->id === $receiver->id) {
            return false;
        }
        if (!$sender->friends()->where('users.id', $receiver->id)->exists()) {
            return false;
        }
        if ($this->hasSentToday($sender, $receiver)) {
            return false;
        }

        UserFriendGift::create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'amount' => $amount,
            'is_claimed' => false,
            'sent_at' => now(),
        ]);
        return true;
    }

    public function hasSentToday(User $sender, User $receiver): bool
    {
        $today = now()->startOfDay();
        return UserFriendGift::where('sender_id', $sender->id)
            ->where('receiver_id', $receiver->id)
            ->where('sent_at', '>=', $today)
            ->exists();
    }

    public function claimGift(User $receiver, UserFriendGift $gift): bool
    {
        if ($gift->receiver_id !== $receiver->id) {
            return false;
        }
        if ($gift->is_claimed) {
            return false;
        }

        DB::transaction(function () use ($receiver, $gift) {
            $receiver->cash += $gift->amount;
            $receiver->save();
            $gift->update([
                'is_claimed' => true,
                'claimed_at' => now(),
            ]);
        });
        return true;
    }

    public function sendToAllFriends(User $sender, int $amount = 100): int
    {
        $count = 0;
        foreach ($sender->friends as $friend) {
            if (!$this->hasSentToday($sender, $friend)) {
                $this->sendGift($sender, $friend, $amount);
                $count++;
            }
        }
        return $count;
    }

    public function getGiftsToClaim(User $user)
    {
        return $user->friendGiftsToClaim()->get();
    }

    public function hasUnclaimedGifts(User $user): bool
    {
        return $user->friendGiftsToClaim()->exists();
    }
}
