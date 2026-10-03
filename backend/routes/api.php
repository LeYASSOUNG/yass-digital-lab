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
use App\Http\Controllers\Api\SitemapController;
use App\Http\Controllers\Api\NewsletterController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\QuoteRequestController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\AnalyticsController;
use App\Http\Controllers\Api\AffiliateController;
use App\Http\Controllers\Api\MobileMoneyWebhookController;
use App\Http\Controllers\Api\AiAssistantController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\WishlistController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\CategoryController;

// ============================================================
// BLOC 1 : Authentification & Mot de passe (Rate Limited)
// ============================================================

Route::middleware('throttle:6,1')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);

    // Connexion via réseaux sociaux (OAuth)
    Route::get('/auth/{provider}/redirect', [\App\Http\Controllers\Api\SocialAuthController::class, 'redirect']);
    Route::get('/auth/{provider}/callback', [\App\Http\Controllers\Api\SocialAuthController::class, 'callback']);

    Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('/contact', [ContactController::class, 'send']);
});

// Vérification d'email via URL signée (nom de route : verification.verify)
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
    ->name('verification.verify');


// ============================================================
// BLOC 2 : Routes publiques (Consultation catalogue & Webhooks)
// ============================================================

// --- Diagnostic & Health Check ---
Route::get('/health', function () {
    $dbConnected = false;
    try {
        \Illuminate\Support\Facades\DB::connection()->getPdo();
        $dbConnected = true;
    } catch (\Exception $e) {}

    return response()->json([
        'status' => 'healthy',
        'app' => 'Yass Digital Lab API',
        'version' => '2.0.0',
        'php_version' => PHP_VERSION,
        'laravel_version' => app()->version(),
        'database' => $dbConnected ? 'connected' : 'disconnected',
        'timestamp' => now()->toIso8601String(),
    ]);
});

// --- Utilitaires & SEO ---
Route::get('/sitemap.xml', [SitemapController::class, 'generate']);

// --- Produits & Assistant IA ---
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::get('/products/{id}/cross-sell', [ProductController::class, 'getCrossSellingProducts']);
Route::post('/products/{id}/reviews', [ProductController::class, 'addReview'])->middleware('auth:sanctum');
Route::post('/ai/recommend', [AiAssistantController::class, 'recommend']);

// --- Services ---
Route::get('/services', [ServiceController::class, 'index']);

// --- E-learning (Formations) ---
Route::get('/courses', [CourseController::class, 'index']);
Route::get('/courses/{id}', [CourseController::class, 'show']);
Route::post('/courses/{id}/reviews', [CourseController::class, 'storeReview']);

// --- Blog ---
Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/{slug}', [PostController::class, 'show']);

// --- Newsletter ---
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe']);

// --- Coupons ---
Route::post('/coupons/validate', [CouponController::class, 'validateCoupon']);
Route::get('/coupons/active', [CouponController::class, 'publicIndex']);

// --- Demandes de devis & Devis Proforma PDF ---
Route::post('/quote-requests', [QuoteRequestController::class, 'store']);
Route::get('/quote-requests/{id}/pdf', [QuoteRequestController::class, 'downloadPdf'])
    ->name('quote-requests.pdf');
Route::get('/quote-requests/{id}/pdf-link', [QuoteRequestController::class, 'getPdfLink']);
// Suivi public du devis (sans authentification) — le client cherche par ref ou email
Route::get('/quote-requests/track', [QuoteRequestController::class, 'trackStatus']);

// --- Paiement Stripe Checkout & Webhook & Mobile Money ---
Route::post('/create-checkout-session', [PaymentController::class, 'createCheckoutSession']);
Route::post('/mobile-money/checkout', [PaymentController::class, 'handleMobileMoneyCheckout']);
Route::post('/stripe/webhook', [PaymentController::class, 'handleWebhook']);

// --- CinetPay (Agrégateur Mobile Money) ---
Route::post('/payment/cinetpay/initiate', [PaymentController::class, 'initiateCinetPay']);
Route::post('/payment/cinetpay/notify', [PaymentController::class, 'cinetpayNotify']);

// --- GeniusPay (Orchestrateur de paiement) ---
Route::post('/payment/geniuspay/initiate', [PaymentController::class, 'initiateGeniusPay']);
Route::post('/payment/geniuspay/notify', [PaymentController::class, 'geniuspayNotify']);
// --- Webhooks Passerelles Mobile Money (Wave, Orange Money, MTN MoMo, Moov Money) ---
Route::post('/webhooks/wave', [MobileMoneyWebhookController::class, 'handleWave']);
Route::post('/webhooks/orange-money', [MobileMoneyWebhookController::class, 'handleOrangeMoney']);
Route::post('/webhooks/mtn-momo', [MobileMoneyWebhookController::class, 'handleMtnMoMo']);
Route::post('/webhooks/moov-money', [MobileMoneyWebhookController::class, 'handleMoovMoney']);
Route::post('/webhooks/simulate-momo', [MobileMoneyWebhookController::class, 'simulateWebhook']);

// --- Suivi de commande post-paiement (polling) ---
Route::get('/orders/by-session/{sessionId}', [OrderController::class, 'showBySession']);

// --- Facture PDF Client & Téléchargement Sécurisé Produit (URLs temporaires signées) ---
Route::get('/orders/{id}/invoice', [OrderController::class, 'downloadInvoice'])
    ->name('orders.invoice');
Route::get('/products/{id}/download', [ProductController::class, 'downloadSecureFile'])
    ->name('products.download');


// ============================================================
// BLOC 3 : Routes protégées Utilisateurs (token Sanctum valide)
// ============================================================

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::put('/user/profile', [UserController::class, 'updateProfile']);
    Route::get('/user/purchases', [UserController::class, 'purchases']);
    Route::get('/user/affiliate', [AffiliateController::class, 'getUserAffiliate']);
    Route::post('/user/affiliate/payout', [AffiliateController::class, 'requestPayout']);
    Route::get('/products/{id}/download-link', [ProductController::class, 'getDownloadLink']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Progression E-learning
    Route::post('/chapters/{chapterId}/progress', [CourseController::class, 'toggleProgress']);

    // Wishlist (Favoris Produits)
    Route::get('/user/wishlist', [WishlistController::class, 'index']);
    Route::post('/user/wishlist/{productId}/toggle', [WishlistController::class, 'toggle']);
    Route::delete('/user/wishlist', [WishlistController::class, 'clear']);

    // Notifications
    Route::get('/user/notifications', [NotificationController::class, 'index']);
    Route::post('/user/notifications/mark-read', [NotificationController::class, 'markAllAsRead']);
    Route::post('/user/notifications/{id}/mark-read', [NotificationController::class, 'markAsRead']);
    Route::delete('/user/notifications', [NotificationController::class, 'clearAll']);

    // Tickets Support (Client)
    Route::get('/user/tickets', [TicketController::class, 'index']);
    Route::post('/user/tickets', [TicketController::class, 'store']);
    Route::get('/user/tickets/{id}', [TicketController::class, 'show']);
    Route::post('/user/tickets/{id}/reply', [TicketController::class, 'reply']);

    // Renvoi du lien de vérification d'email
    Route::post('/email/verification-notification', [AuthController::class, 'resendVerificationNotification'])
        ->middleware('throttle:6,1');

    // -------------------------------------------------------
    // Routes Admin / Créateur — Produits & IA
    // -------------------------------------------------------
    Route::middleware('role:admin,creator,super_admin')->group(function () {
        Route::post('/products', [ProductController::class, 'store']);
        Route::put('/products/{id}', [ProductController::class, 'update']);
        Route::delete('/products/{id}', [ProductController::class, 'destroy']);
        Route::get('/admin/products/export', [ProductController::class, 'exportProducts']);
        Route::post('/admin/products/import', [ProductController::class, 'importProducts']);
        Route::post('/admin/ai/generate-description', [AiAssistantController::class, 'generateProductDescription']);
    });

    // -------------------------------------------------------
    // Routes Admin / Support — Commandes, Factures, Affiliation & Tickets
    // -------------------------------------------------------
    Route::middleware('role:admin,creator,support,super_admin')->group(function () {
        Route::get('/orders', [OrderController::class, 'index']);
        Route::get('/orders/{id}', [OrderController::class, 'show']);
        Route::get('/admin/orders/{id}/invoice', [OrderController::class, 'downloadInvoiceAdmin']);
        Route::get('/admin/analytics', [AnalyticsController::class, 'index']);
        Route::get('/admin/affiliates', [AffiliateController::class, 'getAdminAffiliates']);
        Route::put('/admin/affiliates/payouts/{id}', [AffiliateController::class, 'updatePayoutStatus']);

        // Tickets Support (Admin)
        Route::get('/admin/tickets', [TicketController::class, 'adminIndex']);
        Route::get('/admin/tickets/{id}', [TicketController::class, 'adminShow']);
        Route::post('/admin/tickets/{id}/reply', [TicketController::class, 'adminReply']);
        Route::put('/admin/tickets/{id}/status', [TicketController::class, 'adminUpdateStatus']);
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
        Route::put('/reviews/{id}/status', [ReviewController::class, 'updateStatus']);
        Route::post('/reviews/{id}/reply', [ReviewController::class, 'reply']);
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
