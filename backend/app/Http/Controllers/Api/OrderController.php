<?php

/**
 * ============================================================
 * OrderController — Yass Digital Lab
 * ============================================================
 * Contrôleur de gestion des commandes clients.
 * Gère la création (pending), la consultation, le polling par session
 * et le téléchargement de factures PDF sécurisées par URL signées (anti-IDOR).
 * ============================================================
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderController extends Controller
{
    /**
     * Liste des commandes avec recherche, filtrage et pagination.
     */
    public function index(Request $request)
    {
        $query = Order::with('items');

        if ($request->filled('q')) {
            $q = strtolower($request->input('q'));
            $query->where(function ($sub) use ($q) {
                $sub->where('id', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $query->latest();

        if ($request->boolean('all')) {
            return response()->json($query->get());
        }

        $perPage = (int) $request->input('per_page', 10);
        return response()->json($query->paginate($perPage));
    }

    /**
     * Affiche une commande spécifique avec ses articles.
     */
    public function show(Request $request, string $id)
    {
        $order = Order::with('items')->findOrFail($id);
        $user = $request->user();

        if ($user && !$user->isAdmin() && strtolower($user->email) !== strtolower($order->email)) {
            return response()->json(['message' => 'Accès interdit à cette commande.'], 403);
        }

        return response()->json($order);
    }

    /**
     * Recherche une commande par son ID de session Stripe (utilisé pour le polling post-paiement).
     */
    public function showBySession(string $sessionId)
    {
        $order = Order::with('items')->where('stripe_session_id', $sessionId)->firstOrFail();
        
        // Synchronisation manuelle si la commande est en attente (utile en local sans Webhooks)
        if ($order->status === 'pending') {
            if (str_starts_with($sessionId, 'cs_test_mock_')) {
                $order->update(['status' => 'paid']);
                if ($order->quote_id) {
                    \App\Models\QuoteRequest::where('id', $order->quote_id)->update(['status' => 'completed']);
                }
            } else {
                $stripeSecret = env('STRIPE_SECRET');
                if ($stripeSecret && !str_contains($stripeSecret, 'fake')) {
                    try {
                        \Stripe\Stripe::setApiKey($stripeSecret);
                        $session = \Stripe\Checkout\Session::retrieve($sessionId);
                        if ($session->payment_status === 'paid') {
                            $order->update(['status' => 'paid']);
                            if ($order->quote_id) {
                                \App\Models\QuoteRequest::where('id', $order->quote_id)->update(['status' => 'completed']);
                            }
                        }
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Erreur vérification manuelle Stripe: ' . $e->getMessage());
                    }
                }
            }
        }

        return response()->json($order);
    }

    /**
     * Télécharge la facture PDF d'une commande.
     * Protection anti-IDOR : Nécessite une signature d'URL valide (URL::temporarySignedRoute)
     * OU un compte utilisateur propriétaire de la commande OU un administrateur.
     */
    public function downloadInvoice(Request $request, string $id)
    {
        $order = Order::with('items')->findOrFail($id);
        $user = $request->user();

        $hasValidSignature = $request->hasValidSignature();
        $isOwner = $user && strtolower($user->email) === strtolower($order->email);
        $isAdmin = $user && $user->isAdmin();

        if (!$hasValidSignature && !$isOwner && !$isAdmin) {
            return response()->json([
                'message' => 'Accès refusé : signature d\'URL invalide ou privilèges insuffisants.'
            ], 403);
        }

        $pdf = Pdf::loadView('invoice', compact('order'));
        return $pdf->download('facture-' . str_pad($order->id, 6, '0', STR_PAD_LEFT) . '.pdf');
    }

    /**
     * Téléchargement de facture dédié aux administrateurs (protégé par middleware de rôle).
     */
    public function downloadInvoiceAdmin(string $id)
    {
        $order = Order::with('items')->findOrFail($id);
        $pdf = Pdf::loadView('invoice', compact('order'));
        return $pdf->download('facture-admin-' . str_pad($order->id, 6, '0', STR_PAD_LEFT) . '.pdf');
    }
}
