<?php

/**
 * ============================================================
 * NotificationController — Yass Digital Lab
 * ============================================================
 * Gère les notifications en base de données pour l'utilisateur connecté.
 * ============================================================
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Retourne la liste des notifications de l'utilisateur.
     */
    public function index(Request $request)
    {
        $notifications = $request->user()->notifications;

        // Formater les notifications pour le frontend
        $formatted = $notifications->map(function ($notif) {
            return [
                'id' => $notif->id,
                'type' => $notif->data['type'] ?? 'system',
                'title' => $notif->data['title'] ?? 'Notification',
                'desc' => $notif->data['desc'] ?? '',
                'time' => $notif->created_at->diffForHumans(),
                'read' => !is_null($notif->read_at),
                'link' => $notif->data['link'] ?? null,
            ];
        });

        return response()->json($formatted);
    }

    /**
     * Marquer une notification comme lue.
     */
    public function markAsRead(Request $request, $id)
    {
        $notification = $request->user()->notifications()->find($id);

        if ($notification) {
            $notification->markAsRead();
            return response()->json(['message' => 'Notification marquée comme lue.']);
        }

        return response()->json(['message' => 'Notification introuvable.'], 404);
    }

    /**
     * Marquer toutes les notifications comme lues.
     */
    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();
        return response()->json(['message' => 'Toutes les notifications ont été marquées comme lues.']);
    }

    /**
     * Supprimer toutes les notifications.
     */
    public function clearAll(Request $request)
    {
        $request->user()->notifications()->delete();
        return response()->json(['message' => 'Toutes les notifications ont été supprimées.']);
    }
}
