<?php

/**
 * ============================================================
 * PaymentController — Yass Digital Lab
 * ============================================================
 * Contrôleur de paiement via l'API Stripe Checkout.
 * Crée une session de paiement sécurisée et redirige le client
 * vers la page de paiement Stripe hébergée.
 *
 * Route associée (api.php) :
 *   POST /api/create-checkout-session  → createCheckoutSession()
 *
 * Flux de paiement complet :
 *   1. Frontend envoie les articles du panier → createCheckoutSession()
 *   2. Stripe génère une URL de paiement sécurisée
 *   3. L'utilisateur est redirigé vers la page Stripe
 *   4. Après paiement → redirection vers /dashboard?success=true
 *   5. Frontend appelle POST /api/orders pour enregistrer la commande
 * ============================================================
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class PaymentController extends Controller
{
    /**
     * Crée une session de paiement Stripe Checkout.
     *
     * Reçoit les articles du panier, construit les line_items Stripe
     * (produit + prix en centimes + quantité), puis crée une session
     * de paiement et retourne l'URL de redirection.
     *
     * @param  Request $request  POST : items[] avec {title, price, quantity}
     * @return \Illuminate\Http\JsonResponse  {id: session_id, url: checkout_url} | 500 en cas d'erreur Stripe
     */
    public function createCheckoutSession(Request $request)
    {
        // Validation des articles du panier
        $request->validate([
            'items'              => 'required|array',
            'items.*.title'      => 'required|string',
            'items.*.price'      => 'required|numeric',
            'items.*.quantity'   => 'required|integer|min:1',
        ]);

        // Initialisation de la clé secrète Stripe depuis le fichier .env
        // ⚠️ Remplacez la valeur STRIPE_SECRET dans .env par votre vraie clé Stripe
        Stripe::setApiKey(env('STRIPE_SECRET', 'sk_test_fake_key_123456789'));

        // Construction du tableau de line_items pour Stripe
        $lineItems = [];

        foreach ($request->items as $item) {
            $lineItems[] = [
                'price_data' => [
                    'currency'     => 'eur',                         // Devise : Euro
                    'product_data' => [
                        'name' => $item['title'],                    // Nom du produit affiché sur Stripe
                    ],
                    'unit_amount'  => intval($item['price'] * 100),  // Stripe exige le montant en centimes (ex: 29.99€ → 2999)
                ],
                'quantity' => $item['quantity'],
            ];
        }

        try {
            // Création de la session Stripe Checkout
            $session = Session::create([
                'payment_method_types' => ['card'],      // Méthode de paiement acceptée : carte bancaire
                'line_items'           => $lineItems,    // Articles du panier
                'mode'                 => 'payment',     // Mode unique (pas abonnement)

                // URL de redirection après paiement réussi
                'success_url' => env('FRONTEND_URL', 'http://localhost:5173') . '/dashboard?success=true',

                // URL de redirection si le client annule le paiement
                'cancel_url'  => env('FRONTEND_URL', 'http://localhost:5173') . '/checkout?canceled=true',
            ]);

            // Retourne l'identifiant de session et l'URL de paiement Stripe
            return response()->json(['id' => $session->id, 'url' => $session->url]);

        } catch (\Exception $e) {
            // En cas d'erreur Stripe (clé invalide, réseau, etc.), retourne une erreur 500
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
