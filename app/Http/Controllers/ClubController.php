<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\ClubRequest;
use App\Models\User;
use App\Services\ClubService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ClubController extends Controller
{
    public function __construct(
        private ClubService $clubService
    ) {
    }

    public function index(): Response
    {
        $clubs = Club::with(['leader', 'members'])
            ->orderBy('total_cp', 'desc')
            ->get();

        $user = Auth::user();
        $userClub = $user->club()->with(['leader', 'members'])->first();

        $userPendingRequests = ClubRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->pluck('club_id')
            ->toArray();

        return Inertia::render('Club/Index', [
            'clubs' => $clubs,
            'userClub' => $userClub,
            'userPendingRequests' => $userPendingRequests,
            'canCreateClub' => !$user->isInClub() && $user->cash >= 100000
        ]);
    }

    public function show(Club $club): Response
    {
        $club->load([
            'leader',
            'members.pokedex' => function ($query) {
                $query->where('is_in_team', true)->with('pokemon');
            },
            'pendingRequests.user'
        ]);

        $user = Auth::user();
        $isMember = $user->club()->where('club_id', $club->id)->exists();
        $isLeader = $club->leader_id === $user->id;
        $hasPendingRequest = $club->requests()->where('user_id', $user->id)->where('status', 'pending')->exists();

        $pendingRequests = $club->pendingRequests()->with('user')->get();

        return Inertia::render('Club/Show', [
            'club' => $club,
            'isMember' => $isMember,
            'isLeader' => $isLeader,
            'hasPendingRequest' => $hasPendingRequest,
            'canJoin' => !$user->isInClub() && !$hasPendingRequest && $club->canAcceptNewMember(),
            'pendingRequests' => $pendingRequests
        ]);
    }

    public function create(): Response|RedirectResponse
    {
        $user = Auth::user();
        if ($user->isInClub()) {
            return redirect()->route('club.index');
        }

        $typeImages = [];
        $typesPath = public_path('images/club-icons');

        if (is_dir($typesPath)) {
            $files = glob($typesPath . '/*.png');
            foreach ($files as $file) {
                $filename = basename($file, '.png');
                $typeImages[$filename] = '/images/club-icons/' . basename($file);
            }
        }

        return Inertia::render('Club/Create', [
            'typeImages' => $typeImages,
            'userCash' => $user->cash,
            'canCreateClub' => $user->cash >= 100000
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:clubs,name',
            'description' => 'required|string|max:500',
            'icon' => 'required|string|max:100'
        ]);

        try {
            $club = $this->clubService->createClub(
                Auth::user(),
                $validated['name'],
                $validated['description'],
                $validated['icon']
            );

            return redirect()->route('club.show', $club)->with('success', 'Club créé avec succès !');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function join(Club $club)
    {
        try {
            $this->clubService->sendJoinRequest(Auth::user(), $club);
            return back()->with('success', 'Demande d\'adhésion envoyée !');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function leave()
    {
        try {
            $this->clubService->leaveClub(Auth::user());
            return redirect()->route('club.index')->with('success', 'Vous avez quitté le club');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function acceptRequest(ClubRequest $request)
    {
        if ($request->club->leader_id !== Auth::user()->id) {
            return back()->withErrors(['error' => 'Accès refusé']);
        }

        try {
            $this->clubService->acceptRequest($request);
            return back()->with('success', 'Demande acceptée !');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function rejectRequest(ClubRequest $request)
    {
        if ($request->club->leader_id !== Auth::user()->id) {
            return back()->withErrors(['error' => 'Accès refusé']);
        }

        try {
            $this->clubService->rejectRequest($request);
            return back()->with('success', 'Demande refusée');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function removeMember(Club $club, Request $request)
    {
        if ($club->leader_id !== Auth::user()->id) {
            return back()->withErrors(['error' => 'Accès refusé']);
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        try {
            $member = User::findOrFail($validated['user_id']);
            $this->clubService->removeMember($club, $member);
            return back()->with('success', 'Membre expulsé');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function transferLeadership(Club $club, Request $request)
    {
        if ($club->leader_id !== Auth::user()->id) {
            return back()->withErrors(['error' => 'Accès refusé']);
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        try {
            $newLeader = User::findOrFail($validated['user_id']);
            $this->clubService->transferLeadership($club, $newLeader);
            return back()->with('success', 'Leadership transféré avec succès. Vous êtes maintenant membre du club.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function destroyClub(Club $club)
    {
        if ($club->leader_id !== Auth::user()->id) {
            return back()->withErrors(['error' => 'Accès refusé']);
        }

        try {
            $this->clubService->destroyClub($club);
            return redirect()->route('club.index')->with('success', 'Club détruit avec succès');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
