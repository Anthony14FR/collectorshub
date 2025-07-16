<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserFriendGift;
use App\Services\UserFriendGiftService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserFriendGiftController extends Controller
{
    protected UserFriendGiftService $giftService;

    public function __construct(UserFriendGiftService $giftService)
    {
        $this->giftService = $giftService;
    }

    public function send(Request $request)
    {
        $user = Auth::user();
        $receiver = User::findOrFail($request->input('receiver_id'));
        $ok = $this->giftService->sendGift($user, $receiver);
        return redirect()->back()->with($ok ? 'success' : 'error', $ok ? 'Cadeau envoyé !' : 'Impossible d\'envoyer le cadeau.');
    }

    public function sendToAll()
    {
        $user = Auth::user();
        $count = $this->giftService->sendToAllFriends($user);
        return redirect()->back()->with('success', "$count cadeaux envoyés à vos amis !");
    }

    public function claim(Request $request)
    {
        $user = Auth::user();
        $gift = UserFriendGift::findOrFail($request->input('gift_id'));
        $ok = $this->giftService->claimGift($user, $gift);
        return redirect()->back()->with($ok ? 'success' : 'error', $ok ? 'Cadeau récupéré !' : 'Impossible de récupérer ce cadeau.');
    }

    public function toClaim()
    {
        $user = Auth::user();
        $gifts = $this->giftService->getGiftsToClaim($user);
        return response()->json(['gifts' => $gifts]);
    }
}
