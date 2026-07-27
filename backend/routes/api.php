<?php

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

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Routes publiques
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::post('/products/{id}/reviews', [ProductController::class, 'addReview']);
Route::get('/services', [ServiceController::class, 'index']);
Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/{slug}', [PostController::class, 'show']);
Route::get('/orders/{id}/invoice', [OrderController::class, 'downloadInvoice']);
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe']);
Route::post('/coupons/validate', [CouponController::class, 'validateCoupon']);

// Routes protégées
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Admin CRUD Routes
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{id}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);

    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::get('/orders/{id}/invoice', [OrderController::class, 'downloadInvoice']);
    
    Route::post('/coupons', [CouponController::class, 'store']);
    Route::get('/coupons', [CouponController::class, 'index']);
    
    Route::post('/posts', [PostController::class, 'store']);
    Route::get('/newsletter', [NewsletterController::class, 'index']);

    // Super Admin Routes
    Route::get('/users', [UserController::class, 'index']);
    Route::put('/users/{id}/role', [UserController::class, 'updateRole']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
});

// Payment Route
Route::post('/create-checkout-session', [PaymentController::class, 'createCheckoutSession']);
Route::post('/orders', [OrderController::class, 'store']); // Peut être public pour la création après paiement
