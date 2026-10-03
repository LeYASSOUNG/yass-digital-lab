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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Mail\QuoteAccepted;
use App\Mail\QuoteReceived;

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
            'phone'         => 'nullable|string|max:100',
            'company'       => 'nullable|string|max:255',
            'service_title' => 'required|string|max:255',
            'budget'        => 'nullable|string|max:100',
            'amount'        => 'nullable|string|max:100',
            'deadline'      => 'nullable|string|max:100',
            'details'       => 'nullable|string',
        ]);

        // Création de la demande (statut 'pending' défini par défaut dans le modèle ou migration)
        $quote = QuoteRequest::create($validated);

        // Email de confirmation immédiat au client — confirmation de réception et délai de traitement
        try {
            Mail::to($quote->email)->send(new QuoteReceived($quote));
        } catch (\Exception $e) {
            // Erreur SMTP non bloquante — ne pas interrompre la réponse
            Log::warning('QuoteReceived mail failed: ' . $e->getMessage());
        }

        return response()->json([
            'message' => 'Demande de devis transmise avec succès',
            'data'    => $quote
        ], 201);
    }

    /**
     * Met à jour le statut et/ou le montant réel d'une demande de devis (Admin uniquement).
     * Envoie un email automatique au client si le statut est 'accepted'.
     */
    public function updateStatus(Request $request, string $id)
    {
        $quote = QuoteRequest::findOrFail($id);
        $previousStatus = $quote->status;

        $validated = $request->validate([
            'status' => 'sometimes|string|in:pending,contacted,accepted,in_progress,completed,rejected',
            'amount' => 'nullable|string|max:100',
        ]);

        $quote->update($validated);

        // Envoi automatique de l'email au client lorsque le devis est accepté
        if (isset($validated['status']) && $validated['status'] === 'accepted' && $previousStatus !== 'accepted') {
            try {
                Mail::to($quote->email)->send(new QuoteAccepted($quote));
            } catch (\Exception $e) {
                // Erreur SMTP non bloquante — loggée sans interrompre la réponse
                Log::warning('QuoteAccepted mail failed: ' . $e->getMessage());
            }
        }

        return response()->json(['message' => 'Devis mis à jour avec succès', 'data' => $quote]);
    }

    /**
     * Permet au client de suivre le statut de son devis via son email ou son ID (Public).
     * Retourne uniquement les champs nécessaires pour le suivi côté client.
     */
    public function trackStatus(Request $request)
    {
        $request->validate([
            'ref'   => 'nullable|integer',
            'email' => 'nullable|email',
        ]);

        if (!$request->ref && !$request->email) {
            return response()->json(['message' => 'Fournissez un numéro de référence ou un email.'], 422);
        }

        $query = QuoteRequest::query();
        if ($request->ref) {
            $query->where('id', (int) $request->ref);
        }
        if ($request->email) {
            $query->where('email', $request->email);
        }

        $quotes = $query->orderBy('created_at', 'desc')->get();

        if ($quotes->isEmpty()) {
            return response()->json(['message' => 'Aucune demande trouvée.'], 404);
        }

        // Retour des champs publics uniquement (pas de données sensibles)
        $public = $quotes->map(function ($q) {
            return [
                'id'            => $q->id,
                'ref'           => 'DEV-' . str_pad($q->id, 6, '0', STR_PAD_LEFT),
                'service_title' => $q->service_title,
                'status'        => $q->status ?: 'pending',
                'amount'        => $q->amount ?: $q->budget,
                'deadline'      => $q->deadline,
                'email'         => $q->email,
                'created_at'    => $q->created_at,
            ];
        });

        return response()->json(['data' => $public]);
    }

    /**
     * Supprime une demande de devis (Admin uniquement).
     */
    public function destroy(string $id)
    {
        $quote = QuoteRequest::findOrFail($id);
        $quote->delete();

        return response()->json(['message' => 'Demande de devis supprimée']);
    }

    /**
     * Génère une URL signée temporaire pour le téléchargement du devis PDF proforma.
     */
    public function getPdfLink(Request $request, string $id)
    {
        $quote = QuoteRequest::findOrFail($id);
        $expiresAt = now()->addDays(30);

        $signedUrl = URL::temporarySignedRoute(
            'quote-requests.pdf',
            $expiresAt,
            ['id' => $quote->id]
        );

        return response()->json([
            'pdf_url'    => $signedUrl,
            'expires_at' => $expiresAt->toIso8601String(),
            'quote_id'   => $quote->id,
        ]);
    }

    /**
     * Génère et télécharge le fichier PDF du Devis Proforma / Bon de Commande.
     */
    public function downloadPdf(Request $request, string $id)
    {
        $quote = QuoteRequest::findOrFail($id);
        $pdf = Pdf::loadView('quote_pdf', compact('quote'));
        $fileName = 'devis-proforma-DEV-' . str_pad($quote->id, 6, '0', STR_PAD_LEFT) . '.pdf';

        return $pdf->download($fileName);
    }
}
