<?php

namespace App\Http\Controllers;

use App\Models\Success;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;

class SuccessController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        
        $successes = Success::with(['users' => function ($query) use ($user) {
            $query->where('user_id', $user->id);
        }])->get();
        
        $progress = $user->getSuccessProgress();
        
        return Inertia::render('Successes', [
            'successes' => $successes,
            'progress' => $progress
        ]);
    }

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $successes = Success::with(['users' => function ($query) use ($user) {
            $query->where('user_id', $user->id);
        }])->get();
        
        $progress = $user->getSuccessProgress();
        
        return response()->json([
            'successes' => $successes,
            'progress' => $progress
        ]);
    }

    public function userSuccesses(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $successes = $user->successes()
                            ->orderBy('user_successes.unlocked_at', 'desc')
                            ->get();
        
        return response()->json($successes);
    }

    public function unclaimed(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $unclaimed = $user->unclaimedSuccesses()
                          ->orderBy('unlocked_at', 'desc')
                          ->get();
        
        return response()->json([
            'unclaimed_successes' => $unclaimed,
            'count' => $unclaimed->count()
        ]);
    }

    public function claim(Request $request, $successId): JsonResponse
    {
        $user = $request->user();
        
        $success = $user->claimSuccess($successId);
        
        if ($success) {
            return response()->json([
                'message' => 'Badge réclamé avec succès!',
                'unclaimed_count' => $user->getUnclaimedCount()
            ]);
        }
        
        return response()->json([
            'message' => 'Badge non trouvé ou déjà réclamé'
        ], 404);
    }

    public function claimAll(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $claimedCount = $user->claimAllSuccesses();
        
        return response()->json([
            'message' => "Vous avez réclamé {$claimedCount} badge(s)!",
            'claimed_count' => $claimedCount,
            'unclaimed_count' => $user->getUnclaimedCount()
        ]);
    }

    public function notifications(Request $request): JsonResponse
    {
        $user = $request->user();
        
        return response()->json([
            'has_unclaimed' => $user->getUnclaimedCount() > 0,
            'unclaimed_count' => $user->getUnclaimedCount()
        ]);
    }
}
