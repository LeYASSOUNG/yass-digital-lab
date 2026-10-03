<?php

/**
 * ============================================================
 * ReviewController — Yass Digital Lab
 * ============================================================
 * Contrôleur de gestion et modération avancée des avis clients.
 * Permet aux administrateurs de filtrer par statut, d'approuver/rejeter,
 * de rédiger une réponse officielle et de supprimer des avis.
 * ============================================================
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Liste des avis pour l'administration avec filtres par statut et recherche.
     */
    public function index(Request $request)
    {
        $query = Review::with('product');

        // Filtre par statut de modération
        if ($request->filled('status') && $request->input('status') !== 'all') {
            $query->where('status', $request->input('status'));
        }

        // Filtre par produit
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->input('product_id'));
        }

        // Recherche texte
        if ($request->filled('q')) {
            $q = strtolower($request->input('q'));
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('comment', 'like', "%{$q}%")
                    ->orWhereHas('product', function ($p) use ($q) {
                        $p->where('title', 'like', "%{$q}%");
                    });
            });
        }

        $query->orderBy('created_at', 'desc');

        if ($request->boolean('all')) {
            return response()->json($query->get());
        }

        $perPage = (int) $request->input('per_page', 15);
        return response()->json($query->paginate($perPage));
    }

    /**
     * Met à jour le statut de modération d'un avis ('approved', 'pending', 'rejected').
     */
    public function updateStatus(Request $request, int $id)
    {
        $request->validate([
            'status' => 'required|in:approved,pending,rejected'
        ]);

        $review = Review::findOrFail($id);
        $newStatus = $request->input('status');

        $review->update([
            'status'       => $newStatus,
            'is_published' => $newStatus === 'approved',
        ]);

        return response()->json([
            'message' => 'Statut de l\'avis mis à jour avec succès.',
            'review'  => $review->load('product')
        ]);
    }

    /**
     * Publie ou met à jour une réponse officielle administrateur sur un avis.
     */
    public function reply(Request $request, int $id)
    {
        $request->validate([
            'admin_reply' => 'required|string|max:1000'
        ]);

        $review = Review::findOrFail($id);
        $review->update([
            'admin_reply'    => $request->input('admin_reply'),
            'admin_reply_at' => now(),
        ]);

        return response()->json([
            'message' => 'Réponse officielle enregistrée avec succès.',
            'review'  => $review->load('product')
        ]);
    }

    /**
     * Supprime définitivement un avis client.
     */
    public function destroy(int $id)
    {
        $review = Review::findOrFail($id);
        $review->delete();

        return response()->json(['message' => 'Avis client supprimé avec succès.']);
    }
}
