<?php
namespace App\Http\Controllers;

use App\Models\Success;
use App\Services\SuccessService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class SuccessController extends Controller
{
    private SuccessService $successService;

    public function __construct(SuccessService $successService)
    {
        $this->successService = $successService;
    }

    public function show(Request $request)
    {
        $user = Auth::user();
        
        $userSuccesses = $user->successes;
        $allSuccesses = Success::all();
        $progress = $this->successService->getSuccessProgress($user);
        
        return Inertia::render('Successes', [
            'successes' => $allSuccesses,
            'user_successes' => $userSuccesses,
            'progress' => $progress
        ]);
    }

    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        $userSuccesses = $user->successes;
        $allSuccesses = Success::all();
        $progress = $this->successService->getSuccessProgress($user);
        
        return response()->json([
            'successes' => $allSuccesses,
            'user_successes' => $userSuccesses,
            'progress' => $progress
        ]);
    }

    public function userSuccesses(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        $successes = $user->successes()
                          ->orderBy('user_successes.unlocked_at', 'desc')
                          ->get();
        
        return response()->json($successes);
    }

    public function unclaimed(Request $request): JsonResponse
    {
        $user = Auth::user();
        
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
        $user = Auth::user();
        
        $success = $this->successService->claimSuccess($user, $successId);
        
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
        $user = Auth::user();
        
        $claimedCount = $this->successService->claimAllSuccesses($user);
        
        return response()->json([
            'message' => "Vous avez réclamé {$claimedCount} badge(s)!",
            'claimed_count' => $claimedCount,
            'unclaimed_count' => $user->getUnclaimedCount()
        ]);
    }

    public function notifications(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        return response()->json([
            'has_unclaimed' => $user->getUnclaimedCount() > 0,
            'unclaimed_count' => $user->getUnclaimedCount()
        ]);
    }
}