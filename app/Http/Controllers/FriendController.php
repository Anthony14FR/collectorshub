<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\FriendService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class FriendController extends Controller
{
    protected FriendService $friendService;

    public function __construct(FriendService $friendService)
    {
        $this->friendService = $friendService;
    }

    /**
     * Affiche la liste des amis, suggestions et demandes
     */
    public function index()
    {
        $user = Auth::user();
        $userFriendGiftService = app(\App\Services\UserFriendGiftService::class);
        $friends = $user->friends()->get()->map(function ($friend) use ($user, $userFriendGiftService) {
            $hasSentGiftToday = $userFriendGiftService->hasSentToday($user, $friend);
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

    /**
     * Envoie une demande d'ami
     */
    public function sendRequest(Request $request)
    {
        $user = Auth::user();
        $target = User::findOrFail($request->input('target_id'));
        $ok = $this->friendService->sendFriendRequest($user, $target);
        return redirect()->route('me')->with($ok ? 'success' : 'error', $ok ? 'Demande envoyée !' : 'Impossible d\'envoyer la demande.');
    }

    /**
     * Accepte une demande d'ami
     */
    public function acceptRequest(Request $request)
    {
        $user = Auth::user();
        $requester = User::findOrFail($request->input('requester_id'));
        $ok = $this->friendService->acceptFriendRequest($user, $requester);
        return redirect()->route('me')->with($ok ? 'success' : 'error', $ok ? 'Ami ajouté !' : 'Impossible d\'accepter la demande.');
    }

    /**
     * Supprime un ami ou refuse une demande
     */
    public function remove(Request $request)
    {
        $user = Auth::user();
        $target = User::findOrFail($request->input('target_id'));
        $ok = $this->friendService->removeFriend($user, $target);
        return redirect()->route('me')->with($ok ? 'success' : 'error', $ok ? 'Ami supprimé.' : 'Erreur lors de la suppression.');
    }

    /**
     * Recherche d'utilisateur (username ou email)
     */
    public function search(Request $request)
    {
        $query = $request->input('query');
        $results = $this->friendService->searchUser($query);
        return response()->json(['results' => $results]);
    }

    /**
     * Suggestions d'amis (API JSON)
     */
    public function suggestions()
    {
        $user = Auth::user();
        $suggestions = $this->friendService->getFriendSuggestions($user);
        return response()->json(['suggestions' => $suggestions]);
    }
}
