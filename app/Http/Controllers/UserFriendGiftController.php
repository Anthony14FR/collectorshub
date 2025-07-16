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

        return redirect()->route('me')->with(
            $ok ? 'success' : 'error',
            $ok ? '🎁 Cadeau envoyé avec succès !' : 'Impossible d\'envoyer le cadeau.'
        );
    }

    public function sendToAll()
    {
        $user = Auth::user();
        $count = $this->giftService->sendToAllFriends($user);

        return redirect()->route('me')->with(
            'success',
            "🎉 $count cadeaux envoyés à vos amis !"
        );
    }

    public function claim(Request $request)
    {
        $user = Auth::user();
        $gift = UserFriendGift::findOrFail($request->input('gift_id'));

        if ($gift->receiver_id !== $user->id) {
            return redirect()->route('me')->with('error', 'Ce cadeau ne vous appartient pas.');
        }

        if ($gift->is_claimed) {
            return redirect()->route('me')->with('error', 'Ce cadeau a déjà été réclamé.');
        }

        $amount = $gift->amount;
        $ok = $this->giftService->claimGift($user, $gift);

        if ($ok) {
            return redirect()->route('me')->with('success', "🎁 Cadeau récupéré ! Vous avez reçu {$amount} Cash !");
        } else {
            return redirect()->route('me')->with('error', 'Impossible de récupérer ce cadeau.');
        }
    }

    public function toClaim()
    {
        $user = Auth::user();
        $gifts = $this->giftService->getGiftsToClaim($user);

        return response()->json([
            'gifts' => $gifts->map(function ($gift) {
                return [
                    'id' => $gift->id,
                    'amount' => $gift->amount,
                    'sender' => [
                        'id' => $gift->sender->id,
                        'username' => $gift->sender->username,
                        'avatar' => $gift->sender->avatar,
                        'level' => $gift->sender->level,
                    ],
                    'sent_at' => $gift->sent_at->format('Y-m-d H:i:s'),
                ];
            })
        ]);
    }

    public function getStats()
    {
        $user = Auth::user();

        $stats = [
            'gifts_to_claim' => $user->friendGiftsToClaim()->count(),
            'gifts_sent_today' => $user->userFriendGiftsSent()
                ->where('sent_at', '>=', now()->startOfDay())
                ->count(),
            'total_gifts_sent' => $user->userFriendGiftsSent()->count(),
            'total_gifts_received' => $user->userFriendGiftsReceived()->count(),
            'total_cash_received' => $user->userFriendGiftsReceived()
                ->where('is_claimed', true)
                ->sum('amount'),
        ];

        return response()->json(['stats' => $stats]);
    }

    public function claimAll()
    {
        $user = Auth::user();

        try {
            $gifts = $user->friendGiftsToClaim()->get();
            $totalAmount = 0;
            $count = 0;

            foreach ($gifts as $gift) {
                if ($this->giftService->claimGift($user, $gift)) {
                    $totalAmount += $gift->amount;
                    $count++;
                }
            }

            if ($count > 0) {
                return redirect()->route('me')->with('success', "🎁 $count cadeaux récupérés ! Vous avez reçu $totalAmount Cash au total !");
            } else {
                return redirect()->route('me')->with('error', 'Aucun cadeau à récupérer.');
            }
        } catch (\Exception $e) {
            return redirect()->route('me')->with('error', 'Erreur lors de la récupération des cadeaux.');
        }
    }
}
