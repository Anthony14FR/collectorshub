<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserFriend;
use Illuminate\Support\Facades\DB;

class FriendService
{
    protected $successService;

    public function __construct(SuccessService $successService)
    {
        $this->successService = $successService;
    }

    public function sendFriendRequest(User $user, User $target): bool
    {
        if ($user->id === $target->id) {
            return false;
        }
        if ($this->areFriends($user, $target)) {
            return false;
        }
        if ($this->hasPendingRequest($user, $target)) {
            return false;
        }
        if ($user->friends()->count() >= 30) {
            return false;
        }
        if ($target->friends()->count() >= 30) {
            return false;
        }

        UserFriend::create([
            'user_id' => $user->id,
            'friend_id' => $target->id,
            'status' => 'pending',
        ]);
        return true;
    }

    public function acceptFriendRequest(User $user, User $requester): bool
    {
        $request = UserFriend::where('user_id', $requester->id)
            ->where('friend_id', $user->id)
            ->where('status', 'pending')
            ->first();
        if (!$request) {
            return false;
        }
        if ($user->friends()->count() >= 30) {
            return false;
        }
        if ($requester->friends()->count() >= 30) {
            return false;
        }

        DB::transaction(function () use ($request, $user, $requester) {
            $request->update(['status' => 'accepted']);
            UserFriend::updateOrCreate([
                'user_id' => $user->id,
                'friend_id' => $requester->id,
            ], [
                'status' => 'accepted',
            ]);
        });

        $this->successService->checkAndUnlockSuccesses($user);
        $this->successService->checkAndUnlockSuccesses($requester);

        return true;
    }

    public function removeFriend(User $user, User $target): bool
    {
        UserFriend::where(function ($q) use ($user, $target) {
            $q->where('user_id', $user->id)->where('friend_id', $target->id);
        })->orWhere(function ($q) use ($user, $target) {
            $q->where('user_id', $target->id)->where('friend_id', $user->id);
        })->delete();
        return true;
    }

    public function areFriends(User $user, User $target): bool
    {
        return UserFriend::where('user_id', $user->id)
            ->where('friend_id', $target->id)
            ->where('status', 'accepted')
            ->exists();
    }

    public function hasPendingRequest(User $user, User $target): bool
    {
        return UserFriend::where('user_id', $user->id)
            ->where('friend_id', $target->id)
            ->where('status', 'pending')
            ->exists();
    }

    public function getFriendSuggestions(User $user, int $limit = 5)
    {
        $friendIds = $user->friends()->pluck('users.id')->toArray();
        $friendIds[] = $user->id;

        $pendingRequestIds = \App\Models\UserFriend::where(function ($q) use ($user) {
            $q->where('user_id', $user->id)->orWhere('friend_id', $user->id);
        })->where('status', 'pending')->get()->flatMap(function ($request) use ($user) {
            return [$request->user_id, $request->friend_id];
        })->unique()->toArray();

        $excludeIds = array_unique(array_merge($friendIds, $pendingRequestIds));

        return User::whereNotIn('id', $excludeIds)
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }

    public function searchUser(string $query, ?int $excludeUserId = null)
    {
        $searchQuery = User::where('username', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%");

        if ($excludeUserId) {
            $searchQuery->where('id', '!=', $excludeUserId);
        }

        return $searchQuery->get();
    }

    public function getFormattedFriends($user, UserFriendGiftService $userFriendGiftService)
    {
        return $user->friends()->get()->map(function ($friend) use ($user, $userFriendGiftService) {
            $lastSentGift = $userFriendGiftService->getLastSentGift($user, $friend);

            $nextGiftAvailableAt = null;
            $isOnCooldown = false;

            if ($lastSentGift) {
                $availableAt = $lastSentGift->sent_at->addHours(24);
                if (now()->lessThan($availableAt)) {
                    $isOnCooldown = true;
                    $nextGiftAvailableAt = $availableAt->toIso8601String();
                }
            }

            $gift = $friend->userFriendGiftsSent()
                ->where('receiver_id', $user->id)
                ->where('is_claimed', false)
                ->first();
            $hasGiftToClaim = $gift !== null;
            $giftId = $gift ? $gift->id : null;
            return [
                'id' => $friend->id,
                'username' => $friend->username,
                'level' => $friend->level,
                'avatar' => $friend->avatar ?: "/images/trainer/" . (($friend->id % 10) + 1) . ".png",
                'isOnCooldown' => $isOnCooldown,
                'nextGiftAvailableAt' => $nextGiftAvailableAt,
                'hasGiftToClaim' => $hasGiftToClaim,
                'giftId' => $giftId,
            ];
        });
    }

    public function getFormattedFriendRequests($user)
    {
        return $user->friendRequests()->with('user')->get()->map(function ($req) {
            return [
                'id' => $req->id,
                'user' => [
                    'id' => $req->user->id,
                    'username' => $req->user->username,
                    'email' => $req->user->email,
                    'level' => $req->user->level,
                    'avatar' => $req->user->avatar ?: "/images/trainer/" . (($req->user->id % 10) + 1) . ".png",
                ],
            ];
        });
    }

    public function getFormattedFriendSuggestions($user)
    {
        return $this->getFriendSuggestions($user)->map(function ($suggestion) {
            return [
                'id' => $suggestion->id,
                'username' => $suggestion->username,
                'level' => $suggestion->level,
                'avatar' => $suggestion->avatar ?: "/images/trainer/" . (($suggestion->id % 10) + 1) . ".png",
            ];
        });
    }
}
