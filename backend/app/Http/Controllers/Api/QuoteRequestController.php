<?php

/**
 * ============================================================
 * QuoteRequestController — Yass Digital Lab
 * ============================================================
 * Contrôleur de gestion des demandes de devis clients.
 * Permet aux visiteurs de soumettre une demande de devis,
 * aux Admins de consulter, mettre à jour le statut et supprimer.
 *
 * Routes associées (api.php) :
 *   POST   /api/quote-requests               → store()          [Public]
 *   GET    /api/quote-requests               → index()          [Admin]
 *   PUT    /api/quote-requests/{id}/status   → updateStatus()   [Admin]
 *   DELETE /api/quote-requests/{id}          → destroy()        [Admin]
 *
 * Cycle de vie d'une demande de devis :
 *   pending (en attente) → contacted (contacté) → completed (terminé)
 * ============================================================
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\QuoteRequest;
use Illuminate\Http\Request;

class QuoteRequestController extends Controller
{
    /**
     * Liste toutes les demandes de devis, triées par date décroissante (Admin).
     *
     * Les demandes les plus récentes apparaissent en premier
     * pour faciliter le suivi par l'équipe commerciale.
     *
     * @return \Illuminate\Http\JsonResponse  Tableau de toutes les demandes de devis
     */
    public function index()
    {
        // Tri décroissant pour voir les nouvelles demandes en premier
        return response()->json(QuoteRequest::orderBy('created_at', 'desc')->get());
    }

    /**
     * Enregistre une nouvelle demande de devis (Public).
     *
     * Accessible depuis le formulaire de devis de la page Services.
     * Le statut initial est automatiquement 'pending'.
     *
     * @param  Request $request  POST : name, email, service_title, details?
     * @return \Illuminate\Http\JsonResponse  201 avec la demande créée
     */
    public function store(Request $request)
    {
        // Validation des champs du formulaire de devis
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|max:255',
            'service_title' => 'required|string|max:255', // Service demandé (ex: "Création de site web")
            'details'       => 'nullable|string',          // Description libre du besoin du client
        ]);

        // Création de la demande (statut 'pending' défini par défaut dans le modèle ou migration)
        $quote = QuoteRequest::create($validated);

        return response()->json([
            'message' => 'Demande de devis transmise avec succès',
            'data'    => $quote
        ], 201);
    }

    /**
     * Met à jour le statut d'une demande de devis (Admin uniquement).
     *
     * Permet de faire progresser la demande dans le pipeline commercial :
     *   pending → contacted → completed
     *
     * @param  Request $request  PUT : status (pending | contacted | completed)
     * @param  mixed   $id       Identifiant de la demande de devis
     * @return \Illuminate\Http\JsonResponse  Confirmation + demande mise à jour
     */
    public function updateStatus(Request $request, $id)
    {
        $quote = QuoteRequest::findOrFail($id);

        // Validation stricte du statut via la liste blanche (in:)
        $request->validate([
            'status' => 'required|string|in:pending,contacted,completed'
        ]);

        // Mise à jour du seul champ 'status'
        $quote->update(['status' => $request->status]);

        return response()->json(['message' => 'Statut mis à jour', 'data' => $quote]);
    }

    /**
     * Supprime une demande de devis (Admin uniquement).
     *
     * @param  mixed $id  Identifiant de la demande à supprimer
     * @return \Illuminate\Http\JsonResponse  Message de confirmation | 404 si introuvable
     */
    public function destroy($id)
    {
        $quote = QuoteRequest::findOrFail($id);
        $quote->delete();

        return response()->json(['message' => 'Demande de devis supprimée']);
    }
}
