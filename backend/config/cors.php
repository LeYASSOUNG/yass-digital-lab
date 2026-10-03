<?php

/**
 * ============================================================
 * CORS Configuration — Yass Digital Lab
 * ============================================================
 * Restreint l'accès API aux origines autorisées uniquement.
 * - En développement : localhost (Vite 5173)
 * - En production : domaine frontend Vercel / Netlify
 * - Utiliser FRONTEND_URL dans .env pour pointer vers le domaine de production.
 * ============================================================
 */

return [

    // Routes sur lesquelles les en-têtes CORS sont appliqués
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    // Méthodes HTTP autorisées
    'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],

    // Origines explicitement autorisées (via variable d'environnement)
    'allowed_origins' => array_values(array_filter(array_unique([
        rtrim(env('FRONTEND_URL', 'http://localhost:5173'), '/'),
        'http://localhost:5173',  // Vite (dev local)
        'http://127.0.0.1:5173',  // Vite (dev local loopback)
        'http://localhost:5174',  // Vite (fallback port)
        'http://localhost:3000',  // Vue CLI (dev local)
    ]))),

    // Patterns autorisés (déploiements de preview Vercel et Netlify)
    'allowed_origins_patterns' => [
        '#^https://[\w-]+\.vercel\.app$#',    // Toutes les previews Vercel
        '#^https://[\w-]+\.netlify\.app$#',   // Toutes les previews Netlify
    ],

    // En-têtes HTTP autorisés (minimum nécessaire pour Sanctum + JSON API)
    'allowed_headers' => [
        'Content-Type',
        'Authorization',
        'X-Requested-With',
        'Accept',
        'Origin',
        'X-CSRF-TOKEN',
    ],

    // En-têtes exposés au navigateur (pour les liens de téléchargement signés)
    'exposed_headers' => [
        'Content-Disposition',
        'X-Download-Token',
    ],

    // Durée de mise en cache des requêtes preflight (OPTIONS) en secondes (2 heures)
    // Réduit considérablement les requêtes OPTIONS inutiles en production
    'max_age' => 7200,

    // Indispensable pour que les cookies Sanctum fonctionnent en cross-origin
    'supports_credentials' => true,

];
