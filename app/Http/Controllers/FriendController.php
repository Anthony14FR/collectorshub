<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserFriend;
use App\Services\FriendService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class FriendController extends Controller
{
    protected FriendService $friendService;

    public function __construct(FriendService $friendService)
    {
        $this->friendService = $friendService;
    }

    public function index()
    {
        $user = Auth::user();
        $userFriendGiftService = app(\App\Services\UserFriendGiftService::class);
        $friends = $user->friends()->get()->map(function ($friend) use ($user, $userFriendGiftService) {
            $hasSentGiftToday = $userFriendGiftService->getSentGiftToday($user, $friend) !== null;
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
                'hasSentGiftToday' => $hasSentGiftToday,
                'hasGiftToClaim' => $hasGiftToClaim,
                'giftId' => $giftId,
            ];
        });
        $friendRequests = $user->friendRequests()->with('user')->get()->map(function ($req) {
            return [
                'id' => $req->id,
                'user' => [
                    'id' => $req->user->id,
                    'username' => $req->user->username,
                    'email' => $req->user->email,
                    'level' => $req->user->level,
                ],
            ];
        });
        $suggestions = $this->friendService->getFriendSuggestions($user);
        return Inertia::render('Friends/Modal', [
            'friends' => $friends,
            'friend_requests' => $friendRequests,
            'suggestions' => $suggestions,
        ]);
    }

    public function sendRequest(Request $request)
    {
        $user = Auth::user();
        $target = User::findOrFail($request->input('target_id'));
        $ok = $this->friendService->sendFriendRequest($user, $target);
        return redirect()->route('me')->with($ok ? 'success' : 'error', $ok ? 'Demande envoyée !' : 'Impossible d\'envoyer la demande.');
    }

    public function acceptRequest(Request $request)
    {
        $user = Auth::user();
        $requester = User::findOrFail($request->input('requester_id'));
        $ok = $this->friendService->acceptFriendRequest($user, $requester);
        return redirect()->route('me')->with($ok ? 'success' : 'error', $ok ? 'Ami ajouté !' : 'Impossible d\'accepter la demande.');
    }

    public function remove(Request $request)
    {
        $user = Auth::user();
        $target = User::findOrFail($request->input('target_id'));
        $ok = $this->friendService->removeFriend($user, $target);
        return redirect()->route('me')->with($ok ? 'success' : 'error', $ok ? 'Ami supprimé.' : 'Erreur lors de la suppression.');
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $user = Auth::user();

        $friendIds = $user->friends()->pluck('users.id')->toArray();
        $friendIds[] = $user->id;

        $pendingRequestIds = UserFriend::where(function ($q) use ($user) {
            $q->where('user_id', $user->id)->orWhere('friend_id', $user->id);
        })->where('status', 'pending')->get()->map(function ($request) use ($user) {
            return $request->user_id === $user->id ? $request->friend_id : $request->user_id;
        })->toArray();

        $excludeIds = array_merge($friendIds, $pendingRequestIds);

        $results = User::where(function ($q) use ($query) {
            $q->where('username', 'like', "%{$query}%")
              ->orWhere('email', 'like', "%{$query}%");
        })
        ->whereNotIn('id', $excludeIds)
        ->get();

        return response()->json(['results' => $results]);
    }

    public function suggestions()
    {
        $user = Auth::user();
        $suggestions = $this->friendService->getFriendSuggestions($user);
        return response()->json(['suggestions' => $suggestions]);
    }

    public function getPendingRequests()
    {
        try {
            $user = Auth::user();

            $pendingRequests = UserFriend::where('user_id', $user->id)
                ->where('status', 'pending')
                ->with('friend')
                ->get()
                ->map(function ($request) {
                    return [
                        'id' => $request->id,
                        'user' => [
                            'id' => $request->friend->id,
                            'username' => $request->friend->username,
                            'level' => $request->friend->level,
                            'avatar' => $request->friend->avatar,
                        ],
                    ];
                });

            return response()->json(['requests' => $pendingRequests]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
    }

    public function refresh(Request $request)
    {
        $user = Auth::user();

        $cacheKey = "friend_refresh_{$user->id}";
        $lastRefresh = Cache::get($cacheKey);

        if ($lastRefresh) {
            $secondsElapsed = now()->diffInSeconds($lastRefresh);
            if ($secondsElapsed < 15) {
                $remainingTime = 15 - $secondsElapsed;
                return response()->json([
                    'error' => 'Throttle actif',
                    'remaining_time' => $remainingTime,
                    'can_refresh' => false
                ], 429);
            }
        }

        Cache::put($cacheKey, now(), 15);

        try {
            $userFriendGiftService = app(\App\Services\UserFriendGiftService::class);

            $friends = $user->friends()->get()->map(function ($friend) use ($user, $userFriendGiftService) {
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

            $friendRequests = $user->friendRequests()->with('user')->get()->map(function ($req) {
                return [
                    'id' => $req->id,
                    'user' => [
                        'id' => $req->user->id,
                        'username' => $req->user->username,
                        'level' => $req->user->level,
                        'avatar' => $req->user->avatar ?: "/images/trainer/" . (($req->user->id % 10) + 1) . ".png",
                    ],
                ];
            });

            $suggestions = $this->friendService->getFriendSuggestions($user);

            $pendingRequests = UserFriend::where('user_id', $user->id)
                ->where('status', 'pending')
                ->with('friend')
                ->get()
                ->map(function ($request) {
                    return [
                        'id' => $request->id,
                        'user' => [
                            'id' => $request->friend->id,
                            'username' => $request->friend->username,
                            'level' => $request->friend->level,
                            'avatar' => $request->friend->avatar ?: "/images/trainer/" . (($request->friend->id % 10) + 1) . ".png",
                        ],
                    ];
                });

            return response()->json([
                'success' => true,
                'friends' => $friends,
                'friend_requests' => $friendRequests,
                'suggestions' => $suggestions,
                'pending_requests' => $pendingRequests
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors du rafraîchissement'
            ], 500);
        }
    }
}
