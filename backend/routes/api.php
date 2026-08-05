<?php

/**
 * ============================================================
 * Routes API — Yass Digital Lab
 * ============================================================
 * Définition de toutes les routes de l'API REST Laravel.
 * Sécurisées avec RBAC, URLs signées anti-IDOR, Webhook Stripe,
 * Rate Limiting et réinitialisation de mot de passe.
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
// BLOC 1 : Authentification & Mot de passe (Rate Limited)
// ============================================================

Route::middleware('throttle:6,1')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
});

// Vérification d'email via URL signée (nom de route : verification.verify)
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
    ->name('verification.verify');


// ============================================================
// BLOC 2 : Routes publiques (Consultation catalogue & Webhooks)
// ============================================================

// --- Produits ---
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::post('/products/{id}/reviews', [ProductController::class, 'addReview']);

// --- Services ---
Route::get('/services', [ServiceController::class, 'index']);

// --- Blog ---
Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/{slug}', [PostController::class, 'show']);

// --- Newsletter ---
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe']);

// --- Coupons ---
Route::post('/coupons/validate', [CouponController::class, 'validateCoupon']);

// --- Demandes de devis ---
Route::post('/quote-requests', [QuoteRequestController::class, 'store']);

// --- Paiement Stripe Checkout & Webhook ---
Route::post('/create-checkout-session', [PaymentController::class, 'createCheckoutSession']);
Route::post('/stripe/webhook', [PaymentController::class, 'handleWebhook']);

// --- Suivi de commande post-paiement (polling) ---
Route::get('/orders/by-session/{sessionId}', [OrderController::class, 'showBySession']);

// --- Facture PDF Client (URL temporaire signée obligatoire ou propriétaire) ---
Route::get('/orders/{id}/invoice', [OrderController::class, 'downloadInvoice'])
    ->name('orders.invoice');


// ============================================================
// BLOC 3 : Routes protégées Utilisateurs (token Sanctum valide)
// ============================================================

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::put('/user/profile', [UserController::class, 'updateProfile']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Renvoi du lien de vérification d'email
    Route::post('/email/verification-notification', [AuthController::class, 'resendVerificationNotification'])
        ->middleware('throttle:6,1');

    // -------------------------------------------------------
    // Routes Admin / Créateur — Produits
    // -------------------------------------------------------
    Route::middleware('role:admin,creator,super_admin')->group(function () {
        Route::post('/products', [ProductController::class, 'store']);
        Route::put('/products/{id}', [ProductController::class, 'update']);
        Route::delete('/products/{id}', [ProductController::class, 'destroy']);
    });

    // -------------------------------------------------------
    // Routes Admin / Support — Commandes & Factures
    // -------------------------------------------------------
    Route::middleware('role:admin,creator,support,super_admin')->group(function () {
        Route::get('/orders', [OrderController::class, 'index']);
        Route::get('/orders/{id}', [OrderController::class, 'show']);
        Route::get('/admin/orders/{id}/invoice', [OrderController::class, 'downloadInvoiceAdmin']);
    });

    // -------------------------------------------------------
    // Routes Admin — Coupons de réduction
    // -------------------------------------------------------
    Route::middleware('role:admin,super_admin')->group(function () {
        Route::post('/coupons', [CouponController::class, 'store']);
        Route::get('/coupons', [CouponController::class, 'index']);
        Route::put('/coupons/{id}', [CouponController::class, 'update']);
        Route::delete('/coupons/{id}', [CouponController::class, 'destroy']);
    });

    // -------------------------------------------------------
    // Routes Admin / Rédacteur — Blog
    // -------------------------------------------------------
    Route::middleware('role:admin,editor,super_admin')->group(function () {
        Route::post('/posts', [PostController::class, 'store']);
        Route::put('/posts/{id}', [PostController::class, 'update']);
        Route::delete('/posts/{id}', [PostController::class, 'destroy']);
    });

    // -------------------------------------------------------
    // Routes Admin / Support — Devis, Avis & Newsletter
    // -------------------------------------------------------
    Route::middleware('role:admin,support,super_admin')->group(function () {
        Route::get('/newsletter', [NewsletterController::class, 'index']);
        Route::get('/quote-requests', [QuoteRequestController::class, 'index']);
        Route::put('/quote-requests/{id}/status', [QuoteRequestController::class, 'updateStatus']);
        Route::delete('/quote-requests/{id}', [QuoteRequestController::class, 'destroy']);
        Route::get('/reviews', [ReviewController::class, 'index']);
        Route::delete('/reviews/{id}', [ReviewController::class, 'destroy']);
    });

    // -------------------------------------------------------
    // Routes réservées au Super Admin (Gestion des utilisateurs)
    // -------------------------------------------------------
    Route::middleware('role:super_admin')->group(function () {
        Route::get('/users', [UserController::class, 'index']);
        Route::put('/users/{id}/role', [UserController::class, 'updateRole']);
        Route::delete('/users/{id}', [UserController::class, 'destroy']);
    });
});
