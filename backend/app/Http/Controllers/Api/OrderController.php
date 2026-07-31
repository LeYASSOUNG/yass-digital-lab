<?php

/**
 * ============================================================
 * OrderController — Yass Digital Lab
 * ============================================================
 * Contrôleur de gestion des commandes clients.
 * Gère la création, la consultation et la génération de factures PDF.
 *
 * Routes associées (api.php) :
 *   GET  /api/orders            → index()           [Admin]
 *   GET  /api/orders/{id}       → show($id)         [Admin]
 *   POST /api/orders            → store()           [Public — après paiement Stripe]
 *   GET  /api/orders/{id}/invoice → downloadInvoice($id) [Public + Admin]
 *
 * Flux de création de commande :
 *   1. Paiement Stripe réussi → frontend appelle POST /api/orders
 *   2. store() valide les données, crée la commande et ses articles
 *   3. Un email de confirmation est envoyé automatiquement au client
 * ============================================================
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Mail\OrderConfirmation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderController extends Controller
{
    /**
     * Liste toutes les commandes avec leurs articles (réservé Admin).
     *
     * @return \Illuminate\Http\JsonResponse  Tableau de toutes les commandes avec items
     */
    public function index()
    {
        // Charge les commandes avec leur relation 'items' (eager loading)
        return response()->json(Order::with('items')->get());
    }

    /**
     * Affiche une commande spécifique avec ses articles.
     *
     * @param  int $id  Identifiant de la commande
     * @return \Illuminate\Http\JsonResponse  Commande + items | 404 si introuvable
     */
    public function show($id)
    {
        // findOrFail() retourne automatiquement un 404 si la commande n'existe pas
        return response()->json(Order::with('items')->findOrFail($id));
    }

    /**
     * Enregistre une nouvelle commande après un paiement Stripe réussi.
     *
     * Calcule le montant total, crée la commande principale, puis crée
     * chaque article de commande individuellement (relation hasMany).
     * Envoie ensuite un email de confirmation au client de façon asynchrone.
     *
     * @param  Request $request  POST : {email, items[], stripe_session_id?}
     * @return \Illuminate\Http\JsonResponse  201 avec la commande créée et ses articles
     */
    public function store(Request $request)
    {
        // Validation des données POST reçues depuis le frontend
        $validated = $request->validate([
            'email'              => 'required|email',
            'items'              => 'required|array',
            'items.*.title'      => 'required|string',
            'items.*.price'      => 'required|numeric',
            'items.*.quantity'   => 'required|integer|min:1',
            'stripe_session_id'  => 'nullable|string' // ID de session Stripe pour traçabilité
        ]);

        // Calcul du montant total (somme de prix × quantité pour chaque article)
        $totalAmount = collect($validated['items'])->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });

        // Création de la commande principale en base de données
        $order = Order::create([
            'email'        => $validated['email'],
            'total_amount' => $totalAmount,
            // Statut : payé (paiement déjà validé par Stripe)
            'status'            => 'paid',
            // Identifiant de session Stripe pour traçabilité sur le dashboard Stripe
            'stripe_session_id' => $validated['stripe_session_id'] ?? null,
        ]);

        // Création de chaque article lié à la commande (relation OrderItem)
        foreach ($validated['items'] as $item) {
            $order->items()->create([
                'product_title' => $item['title'],
                'price'         => $item['price'],
                'quantity'      => $item['quantity']
            ]);
        }

        // -------------------------------------------------------
        // Envoi de l'email de confirmation de commande au client
        // L'envoi est encapsulé dans un try/catch pour ne pas
        // bloquer la création de commande si l'email échoue
        // (ex: SMTP mal configuré en développement)
        // -------------------------------------------------------
        try {
            Mail::to($order->email)->send(new OrderConfirmation($order->load('items')));
        } catch (\Exception $e) {
            // Log l'erreur d'envoi d'email sans interrompre la réponse
            \Log::error('Email de confirmation non envoyé : ' . $e->getMessage());
        }

        // Retourne la commande créée avec ses articles (relation chargée)
        return response()->json($order->load('items'), 201);
    }

    /**
     * Génère et télécharge la facture PDF d'une commande.
     *
     * Utilise le package barryvdh/laravel-dompdf pour générer
     * le PDF depuis le template Blade : resources/views/invoice.blade.php
     * Le nom de fichier est formaté : facture-000042.pdf
     *
     * @param  int $id  Identifiant de la commande
     * @return \Symfony\Component\HttpFoundation\Response  Fichier PDF en téléchargement
     */
    public function downloadInvoice($id)
    {
        // Chargement de la commande avec ses articles pour la facture
        $order = Order::with('items')->findOrFail($id);

        // Génération du PDF depuis le template Blade 'invoice'
        $pdf = Pdf::loadView('invoice', compact('order'));

        // Téléchargement avec nom de fichier formaté sur 6 chiffres (ex: facture-000042.pdf)
        return $pdf->download('facture-' . str_pad($order->id, 6, '0', STR_PAD_LEFT) . '.pdf');
    }
}
