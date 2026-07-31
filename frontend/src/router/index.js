/**
 * ============================================================
 * Vue Router — Configuration des Routes (index.js)
 * Yass Digital Lab — Frontend Vue 3
 * ============================================================
 * Définit toutes les routes de l'application SPA (Single Page Application).
 * Utilise l'historique HTML5 (createWebHistory) pour des URLs propres
 * sans le caractère '#' (pas de hash-based routing).
 *
 * Pages disponibles :
 *   /              → Accueil
 *   /products      → Catalogue de produits numériques
 *   /products/:id  → Détail d'un produit avec avis et téléchargement
 *   /services      → Services proposés + formulaire de devis
 *   /about         → Page À Propos
 *   /checkout      → Panier et paiement Stripe
 *   /login         → Connexion / Inscription
 *   /admin         → Dashboard Administrateur (CRUD complet)
 *   /dashboard     → Espace Client (profil, commandes, téléchargements)
 *   /blog          → Articles de blog
 *   /contact       → Formulaire de contact
 *   /notifications → Centre de notifications
 *   /*             → Page 404 (route catchall)
 *
 * Note sécurité : Les gardes de navigation (meta.requiresAuth) peuvent
 * être ajoutées pour protéger /admin et /dashboard. (À implémenter)
 * ============================================================
 */

import { createRouter, createWebHistory } from 'vue-router'

// Import de toutes les vues (lazy loading recommandé en production)
import Home            from '../views/Home.vue'
import Products        from '../views/Products.vue'
import ProductDetail   from '../views/ProductDetail.vue'
import Services        from '../views/Services.vue'
import About           from '../views/About.vue'
import Checkout        from '../views/Checkout.vue'
import Login           from '../views/Login.vue'
import AdminDashboard  from '../views/AdminDashboard.vue'
import ClientDashboard from '../views/ClientDashboard.vue'
import Blog            from '../views/Blog.vue'
import Contact         from '../views/Contact.vue'
import Notifications   from '../views/Notifications.vue'
import NotFound        from '../views/NotFound.vue'

/**
 * Définition des routes de l'application.
 * Chaque route associe un chemin URL à un composant Vue.
 */
const routes = [
  // --- Pages publiques ---
  { path: '/',             name: 'Home',            component: Home },            // Page d'accueil
  { path: '/products',     name: 'Products',         component: Products },        // Catalogue produits
  { path: '/products/:id', name: 'ProductDetail',    component: ProductDetail },   // Détail produit (param dynamique)
  { path: '/services',     name: 'Services',         component: Services },        // Services + devis
  { path: '/about',        name: 'About',            component: About },           // À Propos
  { path: '/checkout',     name: 'Checkout',         component: Checkout },        // Panier + paiement
  { path: '/login',        name: 'Login',            component: Login },           // Connexion / Inscription
  { path: '/blog',         name: 'Blog',             component: Blog },            // Blog
  { path: '/contact',      name: 'Contact',          component: Contact },         // Contact

  // --- Pages protégées (authentification recommandée) ---
  { path: '/admin',        name: 'AdminDashboard',   component: AdminDashboard },  // Dashboard Admin (CRUD)
  { path: '/dashboard',    name: 'ClientDashboard',  component: ClientDashboard }, // Espace Client
  { path: '/notifications',name: 'Notifications',    component: Notifications },   // Centre de notifications

  // --- Route 404 : capture tous les chemins non reconnus ---
  // '/:pathMatch(.*)*' est la syntaxe Vue Router 4 pour le catchall
  { path: '/:pathMatch(.*)*', name: 'NotFound', component: NotFound }
]

/**
 * Création de l'instance du routeur Vue.
 *
 * - createWebHistory() → URLs propres (ex: /products au lieu de /#/products)
 * - scrollBehavior()   → Retour en haut de page à chaque navigation
 */
const router = createRouter({
  history: createWebHistory(),  // Historique HTML5 (nécessite une config serveur en production)
  routes,

  // Comportement de défilement : remonte automatiquement en haut à chaque changement de route
  scrollBehavior() {
    return { top: 0 }
  }
})

export default router
