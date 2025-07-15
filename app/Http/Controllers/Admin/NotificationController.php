<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NotificationController extends Controller
{
    /**
     * Afficher la page de création d'annonces
     */
    public function create()
    {
        $users = User::select(['id', 'name', 'email'])->orderBy('name')->get();

        return Inertia::render('Admin/Notifications/Create', [
            'users' => $users
        ]);
    }

    /**
     * Envoyer une annonce
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
            'target_type' => 'required|in:all,specific',
            'user_ids' => 'array|nullable',
            'user_ids.*' => 'exists:users,id',
        ]);

        $title = $validated['title'];
        $message = $validated['message'];
        $targetType = $validated['target_type'];
        $userIds = $validated['user_ids'] ?? [];

        try {
            if ($targetType === 'all') {
                $users = User::all();
                foreach ($users as $user) {
                    Notification::createAnnouncement($user->id, $title, $message);
                }
            } else {
                foreach ($userIds as $userId) {
                    Notification::createAnnouncement($userId, $title, $message);
                }
            }

            $count = $targetType === 'all' ? User::count() : count($userIds);

            return redirect()->route('admin.notifications.create')
                ->with('success', "Annonce envoyée à {$count} utilisateur(s)");

        } catch (\Exception $e) {
            return back()->withErrors([
                'message' => 'Erreur lors de l\'envoi de l\'annonce : ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Afficher l'historique des annonces
     */
    public function index()
    {
        $announcements = Notification::announcements()
            ->with('user:id,name')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return Inertia::render('Admin/Notifications/Index', [
            'announcements' => $announcements
        ]);
    }
}
