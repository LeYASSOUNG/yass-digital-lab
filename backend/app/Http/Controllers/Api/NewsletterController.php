<?php

/**
 * ============================================================
 * NewsletterController — Yass Digital Lab
 * ============================================================
 * Contrôleur de gestion des abonnements à la newsletter.
 * Gère l'inscription des visiteurs et la liste des abonnés pour l'Admin.
 *
 * Routes associées (api.php) :
 *   POST  /api/newsletter/subscribe   → subscribe()  [Public]
 *   GET   /api/newsletter             → index()      [Admin — protégé auth:sanctum]
 *
 * Note : L'email doit être unique dans la table 'subscribers'.
 *        Un message d'erreur personnalisé est retourné si déjà inscrit.
 * ============================================================
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subscriber;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    /**
     * Inscrit un email à la newsletter (Public).
     *
     * Valide l'unicité de l'email en base avant de créer l'abonnement.
     * Un message d'erreur clair est renvoyé si l'email est déjà inscrit.
     *
     * @param  Request $request  POST : email (adresse à inscrire)
     * @return \Illuminate\Http\JsonResponse  200 avec message de confirmation | 422 si déjà inscrit
     */
    public function subscribe(Request $request)
    {
        // Validation : email requis, format valide, unique dans la table subscribers
        $validated = $request->validate([
            'email' => 'required|email|unique:subscribers,email'
        ], [
            // Message d'erreur personnalisé en français pour la règle unique
            'email.unique' => 'Cet email est déjà inscrit à notre newsletter.'
        ]);

        // Création de l'abonnement en base de données
        Subscriber::create($validated);

        return response()->json(['message' => 'Merci pour votre inscription à notre newsletter !']);
    }

    /**
     * Liste tous les abonnés à la newsletter (Admin uniquement).
     *
     * Permet à l'équipe de consulter la liste complète des inscrits
     * pour des campagnes email ou des exports.
     *
     * @return \Illuminate\Http\JsonResponse  Tableau de tous les abonnés
     */
    public function index()
    {
        // Retourne tous les abonnés sans filtrage ni pagination (liste complète)
        return response()->json(Subscriber::all());
    }
}
