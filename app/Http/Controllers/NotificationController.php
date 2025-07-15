<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class NotificationController extends Controller
{
    /**
     * Afficher la page des notifications de l'utilisateur
     */
    public function index()
    {
        $user = Auth::user();

        $announcements = $user->notifications()->announcements()->get();
        $marketplaceHistory = $user->notifications()->marketplaceHistory()->get();
        $unreadCount = $user->notifications()->unread()->count();

        return Inertia::render('Notifications/Index', [
            'announcements' => $announcements,
            'marketplace_history' => $marketplaceHistory,
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Marquer une notification comme lue
     */
    public function markAsRead(Notification $notification): JsonResponse
    {
        // Vérifier que la notification appartient à l'utilisateur connecté
        if ($notification->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Marquer toutes les notifications comme lues
     */
    public function markAllAsRead(): JsonResponse
    {
        Auth::user()->notifications()->unread()->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Supprimer une notification
     */
    public function destroy(Notification $notification): JsonResponse
    {
        if ($notification->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $notification->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Obtenir le nombre de notifications non lues
     */
    public function getUnreadCount(): JsonResponse
    {
        $count = Auth::user()->notifications()->unread()->count();

        return response()->json(['count' => $count]);
    }
}
