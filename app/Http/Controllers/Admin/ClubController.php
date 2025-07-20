<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ClubController extends Controller
{
    public function index()
    {
        $clubs = Club::with(['leader:id,username,avatar', 'members'])
            ->withCount('members')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return Inertia::render('Admin/Clubs/Index', [
            'clubs' => $clubs
        ]);
    }

    public function show(Club $club)
    {
        $club->load([
            'leader:id,username,avatar,level',
            'members:id,username,avatar,level',
        ]);

        return Inertia::render('Admin/Clubs/Show', [
            'club' => $club
        ]);
    }

    public function destroy(Club $club, Request $request)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        try {
            DB::transaction(function () use ($club, $validated) {
                Notification::createAnnouncement(
                    $club->leader_id,
                    'Club supprimé par l\'administration',
                    "Votre club \"{$club->name}\" a été supprimé par l'administration. Raison : {$validated['reason']}"
                );

                $club->members()->detach();
                $club->requests()->delete();
                $club->delete();
            });

            return redirect()->route('admin.clubs.index')
                ->with('success', 'Club supprimé avec succès');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de la suppression : ' . $e->getMessage()]);
        }
    }
}
