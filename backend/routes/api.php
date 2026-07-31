<?php

/**
 * ============================================================
 * Routes API — Yass Digital Lab
 * ============================================================
 * Définition de toutes les routes de l'API REST Laravel.
 * Séparées en trois blocs :
 *
 *   1. Routes d'authentification (publiques)
 *   2. Routes publiques (accessibles sans token)
 *   3. Routes protégées (nécessitent un token Sanctum valide)
 *   4. Routes de paiement (partiellement publiques)
 *
 * Toutes les routes retournent du JSON (application/json).
 * Les erreurs sont au format : {"message": "..."}
 *
 * Base URL : http://localhost:8000/api/
 * ============================================================
 */

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\CouponController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\NewsletterController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\QuoteRequestController;
use App\Http\Controllers\Api\ReviewController;

// ============================================================
// BLOC 1 : Authentification (Public — aucun token requis)
// ============================================================

// Inscription d'un nouveau compte client
Route::post('/login', [AuthController::class, 'login']);

// Connexion et récupération du token Bearer
Route::post('/register', [AuthController::class, 'register']);


// ============================================================
// BLOC 2 : Routes publiques (sans authentification)
// ============================================================

// --- Produits ---
Route::get('/products', [ProductController::class, 'index']);         // Liste tous les produits
Route::get('/products/{id}', [ProductController::class, 'show']);     // Détail d'un produit
Route::post('/products/{id}/reviews', [ProductController::class, 'addReview']); // Ajouter un avis

// --- Services ---
Route::get('/services', [ServiceController::class, 'index']);          // Liste des services proposés

// --- Blog ---
Route::get('/posts', [PostController::class, 'index']);                // Articles publiés
Route::get('/posts/{slug}', [PostController::class, 'show']);          // Article par son slug SEO

// --- Commandes (facture publique pour téléchargement après paiement) ---
Route::get('/orders/{id}/invoice', [OrderController::class, 'downloadInvoice']); // Téléchargement PDF

// --- Newsletter ---
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe']); // Inscription newsletter

// --- Coupons (validation publique au checkout) ---
Route::post('/coupons/validate', [CouponController::class, 'validateCoupon']);    // Valider un code promo

// --- Demandes de devis (formulaire public) ---
Route::post('/quote-requests', [QuoteRequestController::class, 'store']);         // Soumettre une demande


// ============================================================
// BLOC 3 : Routes protégées (token Sanctum obligatoire)
// ============================================================

Route::middleware('auth:sanctum')->group(function () {

    // --- Profil utilisateur connecté ---
    Route::get('/user', function (Request $request) {
        // Retourne les données de l'utilisateur authentifié
        return $request->user();
    });
    Route::put('/user/profile', [UserController::class, 'updateProfile']); // Modifier son profil
    Route::post('/logout', [AuthController::class, 'logout']);              // Déconnexion (révoque le token)

    // -------------------------------------------------------
    // CRUD Admin — Produits
    // -------------------------------------------------------
    Route::post('/products', [ProductController::class, 'store']);       // Créer un produit
    Route::put('/products/{id}', [ProductController::class, 'update']); // Modifier un produit
    Route::delete('/products/{id}', [ProductController::class, 'destroy']); // Supprimer un produit

    // -------------------------------------------------------
    // Commandes (lecture Admin)
    // -------------------------------------------------------
    Route::get('/orders', [OrderController::class, 'index']);            // Liste toutes les commandes
    Route::get('/orders/{id}', [OrderController::class, 'show']);        // Détail d'une commande
    Route::get('/orders/{id}/invoice', [OrderController::class, 'downloadInvoice']); // Facture PDF (Admin)

    // -------------------------------------------------------
    // CRUD Admin — Coupons de réduction
    // -------------------------------------------------------
    Route::post('/coupons', [CouponController::class, 'store']);         // Créer un coupon
    Route::get('/coupons', [CouponController::class, 'index']);          // Liste tous les coupons
    Route::put('/coupons/{id}', [CouponController::class, 'update']);   // Modifier un coupon
    Route::delete('/coupons/{id}', [CouponController::class, 'destroy']); // Supprimer un coupon

    // -------------------------------------------------------
    // CRUD Admin — Articles de blog
    // -------------------------------------------------------
    Route::post('/posts', [PostController::class, 'store']);             // Créer un article
    Route::put('/posts/{id}', [PostController::class, 'update']);       // Modifier un article
    Route::delete('/posts/{id}', [PostController::class, 'destroy']);   // Supprimer un article

    // --- Newsletter (lecture Admin) ---
    Route::get('/newsletter', [NewsletterController::class, 'index']);   // Liste des abonnés

    // -------------------------------------------------------
    // Demandes de devis (gestion Admin)
    // -------------------------------------------------------
    Route::get('/quote-requests', [QuoteRequestController::class, 'index']);              // Liste des demandes
    Route::put('/quote-requests/{id}/status', [QuoteRequestController::class, 'updateStatus']); // Changer le statut
    Route::delete('/quote-requests/{id}', [QuoteRequestController::class, 'destroy']);   // Supprimer une demande

    // -------------------------------------------------------
    // Avis clients (gestion Admin)
    // -------------------------------------------------------
    Route::get('/reviews', [ReviewController::class, 'index']);          // Liste tous les avis
    Route::delete('/reviews/{id}', [ReviewController::class, 'destroy']); // Supprimer un avis

    // -------------------------------------------------------
    // Super Admin — Gestion des utilisateurs
    // -------------------------------------------------------
    Route::get('/users', [UserController::class, 'index']);              // Liste tous les utilisateurs
    Route::put('/users/{id}/role', [UserController::class, 'updateRole']); // Changer le rôle d'un user
    Route::delete('/users/{id}', [UserController::class, 'destroy']);   // Supprimer un utilisateur
});


// ============================================================
// BLOC 4 : Routes de paiement Stripe (partiellement publiques)
// ============================================================

// Création d'une session de paiement Stripe (appelé depuis le frontend au checkout)
Route::post('/create-checkout-session', [PaymentController::class, 'createCheckoutSession']);

// Création de commande après paiement réussi (public car déclenché par le frontend post-Stripe)
// ⚠️ La sécurité est assurée par la vérification de stripe_session_id côté Stripe
Route::post('/orders', [OrderController::class, 'store']);
