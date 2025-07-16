<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserFriend;
use Illuminate\Support\Facades\DB;

class FriendService
{
    /**
     * Envoie une demande d'ami
     */
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

    /**
     * Accepte une demande d'ami
     */
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
            // On crée la relation réciproque
            UserFriend::updateOrCreate([
                'user_id' => $user->id,
                'friend_id' => $requester->id,
            ], [
                'status' => 'accepted',
            ]);
        });
        return true;
    }

    /**
     * Refuse ou supprime une demande d'ami
     */
    public function removeFriend(User $user, User $target): bool
    {
        UserFriend::where(function ($q) use ($user, $target) {
            $q->where('user_id', $user->id)->where('friend_id', $target->id);
        })->orWhere(function ($q) use ($user, $target) {
            $q->where('user_id', $target->id)->where('friend_id', $user->id);
        })->delete();
        return true;
    }

    /**
     * Vérifie si deux utilisateurs sont amis
     */
    public function areFriends(User $user, User $target): bool
    {
        return UserFriend::where('user_id', $user->id)
            ->where('friend_id', $target->id)
            ->where('status', 'accepted')
            ->exists();
    }

    /**
     * Vérifie s'il y a une demande en attente
     */
    public function hasPendingRequest(User $user, User $target): bool
    {
        return UserFriend::where('user_id', $user->id)
            ->where('friend_id', $target->id)
            ->where('status', 'pending')
            ->exists();
    }

    /**
     * Suggestions d'amis (5 utilisateurs aléatoires non amis)
     */
    public function getFriendSuggestions(User $user, int $limit = 5)
    {
        $friendIds = $user->friends()->pluck('users.id')->toArray();
        $friendIds[] = $user->id;
        return User::whereNotIn('id', $friendIds)
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }

    /**
     * Recherche d'utilisateur par username ou email
     */
    public function searchUser(string $query, ?int $excludeUserId = null)
    {
        $searchQuery = User::where('username', 'like', "%$query%")
            ->orWhere('email', 'like', "%$query%");

        if ($excludeUserId) {
            $searchQuery->where('id', '!=', $excludeUserId);
        }

        return $searchQuery->get();
    }
}
