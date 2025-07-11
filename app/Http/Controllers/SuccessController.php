<?php

namespace App\Http\Controllers;

use App\Models\Success;
use App\Models\UserSuccess;
use App\Services\SuccessService;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class SuccessController extends Controller
{
    private SuccessService $successService;

    public function __construct(SuccessService $successService)
    {
        $this->successService = $successService;
    }

    public function index()
    {
        $user = Auth::user();

        $unclaimedSuccesses = $user->unclaimedSuccesses;
        $claimedSuccesses = $user->claimedSuccesses;
        $otherSuccesses = Success::whereNotIn('id', $user->userSuccesses()->pluck('success_id'))->get();
        $progress = $this->successService->getSuccessProgress($user);

        return Inertia::render('Success/Index', [
            'successes' => $otherSuccesses,
            'progress' => $progress,
            'unclaimed_successes' => $unclaimedSuccesses,
            'claimed_successes' => $claimedSuccesses
        ]);
    }

    public function claim($successId)
    {
        $user = Auth::user();

        $userSuccess = UserSuccess::where('user_id', $user->id)
            ->where('success_id', $successId)
            ->first();

        if (!$userSuccess) {
            return redirect()->back()->with('error', 'Badge inconnu');
        }

        if ($userSuccess->is_claimed) {
            return redirect()->back()->with('error', 'Ce badge a déjà été réclamé');
        }

        try {
            $this->successService->claimSuccess($user, $successId);
            return redirect()->back()->with('success', 'Badge récupéré avec succès!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Impossible de réclamer ce badge');
        }
    }

    public function claimAll()
    {
        $user = Auth::user();

        try {
            $count = $this->successService->claimAllSuccesses($user);
            return redirect()->back()->with('success', "Vous avez réclamé {$count} badge(s) avec succès!");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Impossible de réclamer tous les badges');
        }
    }
}
