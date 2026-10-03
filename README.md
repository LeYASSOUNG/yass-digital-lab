# Yass Digital Lab

[![Vue.js](https://img.shields.io/badge/Vue.js-3.5-4FC08D?style=flat-square&logo=vuedotjs&logoColor=white)](https://vuejs.org/)
[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=flat-square&logo=laravel&logoColor=white)](https://laravel.com/)
[![PHP](https://img.shields.io/badge/PHP-^8.2-777BB4?style=flat-square&logo=php&logoColor=white)](https://www.php.net/)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-Neon-4169E1?style=flat-square&logo=postgresql&logoColor=white)](https://neon.tech/)
[![Stripe](https://img.shields.io/badge/Stripe-Payment-635BFF?style=flat-square&logo=stripe&logoColor=white)](https://stripe.com/)
[![PWA](https://img.shields.io/badge/PWA-Ready-10B981?style=flat-square)](https://web.dev/progressive-web-apps/)
[![License](https://img.shields.io/badge/License-MIT-D4AF37?style=flat-square)](https://opensource.org/licenses/MIT)

**Yass Digital Lab** est une marketplace e-commerce spécialisée dans la vente de ressources numériques : templates web Vue 3 / Laravel, packs de prompts IA, scripts automatisés et prestations de développement sur mesure. La plateforme cible les professionnels, startups et créateurs en Côte d'Ivoire, en Afrique de l'Ouest et à l'international.

---

## Table des matières

- [Fonctionnalités](#-fonctionnalités)
- [Architecture](#-architecture)
- [Stack Technique](#-stack-technique)
- [Marketplace et Catalogue](#-marketplace-et-catalogue)
- [Paiements](#-paiements)
- [Multi-devises EUR / FCFA](#-multi-devises-eur--fcfa)
- [Licences et Téléchargements](#-licences-et-téléchargements)
- [Facturation PDF](#-facturation-pdf)
- [Authentification et RBAC](#-authentification-et-rbac)
- [Sécurité](#-sécurité)
- [Dashboard Admin](#-dashboard-admin)
- [Progressive Web App (PWA)](#-progressive-web-app-pwa)
- [SEO et OpenGraph](#-seo-et-opengraph)
- [Design System](#-design-system)
- [Tests et Audit](#-tests-et-audit)
- [Installation et Démarrage Local](#-installation-et-démarrage-local)
- [Variables d'Environnement](#-variables-denvironnement)
- [Déploiement](#-déploiement)
- [Comptes de Démonstration](#-comptes-de-démonstration)
- [Structure du Projet](#-structure-du-projet)
- [Roadmap](#-roadmap)
- [Auteur](#-auteur)

---

## ✨ Fonctionnalités

- Catalogue de produits numériques avec recherche, filtres et pagination
- Catalogue de services / prestations sur devis avec formulaire de contact
- Panier d'achat persistant avec gestion des quantités et codes promo
- Système de paiement Stripe (Checkout) avec Webhook de confirmation
- Paiement Mobile Money intégré : Wave CI, Orange Money, MTN MoMo
- Convertisseur de devises réactif EUR ↔ FCFA (XOF)
- Espace client : historique des achats, clés de licence, téléchargement des factures
- Facturation PDF automatique sécurisée par URLs temporaires signées
- Système d'authentification complet : inscription, connexion, vérification d'email, reset de mot de passe
- RBAC avec 6 rôles : `client`, `creator`, `editor`, `support`, `admin`, `super_admin`
- Dashboard Admin avec gestion des commandes, produits, utilisateurs, coupons, devis et blog
- Graphiques analytiques de revenus avec filtres temporels (`SalesChart.vue`)
- Blog intégré avec articles et gestion éditoriale par l'admin
- Newsletter avec formulaire d'inscription et gestion des abonnés
- Système de notifications in-app et widget de chat client
- Multilingue français / anglais (`i18n.js`) sans rechargement de page
- Progressive Web App installable (manifest + Service Worker)
- Balises SEO et OpenGraph pour partage sur WhatsApp, LinkedIn, Twitter

---

## 🏠 Architecture

```text
Utilisateur (navigateur)
       │
       ▼
  Vue 3 SPA + Vite
  (Pinia, Vue Router 4, Lucide Icons, CSS Tokens)
       │
       │  HTTPS / REST API (Axios)
       ▼
  Laravel 12 API (PHP ^8.2)
       │
       ├── Sanctum (Auth par token)
       ├── RBAC (rôle en colonne User)
       ├── Rate Limiting (throttle:6,1)
       ├── Products / Services / Categories
       ├── Orders / OrderItems
       ├── Payments (Stripe + Mobile Money)
       ├── Licences (génération par commande)
       ├── Invoices PDF (DomPDF + URL signée)
       ├── Coupons
       ├── Posts (Blog)
       ├── QuoteRequests (Devis)
       ├── Newsletter (Subscribers)
       └── Emails (OrderConfirmation Mailable)
       │
       ▼
  PostgreSQL (Neon en production)

Services externes
  ├── Stripe (Checkout Sessions + Webhook)
  ├── Wave CI / Orange Money / MTN MoMo (Mobile Money)
  └── Neon Tech (PostgreSQL hébergé)
```

---

## 🛠 Stack Technique

### Frontend

| Technologie | Version | Rôle |
| --- | --- | --- |
| Vue.js | 3.5.x | Framework réactif (Composition API `<script setup>`) |
| Vite | 8.x | Bundler et serveur de développement |
| Vue Router | 5.x | Routage SPA côté client |
| Pinia | 4.x | Gestion d'état global |
| Axios | 1.x | Client HTTP vers l'API Laravel |
| Lucide Vue Next | 1.x | Icônes SVG vectorielles |
| CSS Tokens | — | Design system Vanilla CSS (aucun Tailwind utilisé) |
| i18n.js | — | Internationalisation FR / EN (custom, sans bibliothèque tierce) |

> ⚠️ **Note :** Ce projet utilise du **CSS Vanilla personnalisé** avec des tokens CSS (`--color-primary`, `--space-*`, etc.). **Tailwind CSS n'est pas installé** dans ce projet.

### Backend

| Technologie | Version | Rôle |
| --- | --- | --- |
| Laravel | 12.x | Framework API REST |
| PHP | ^8.2 | Langage serveur |
| Laravel Sanctum | 4.x | Authentification par token API |
| Stripe PHP SDK | 21.x | Paiement par carte bancaire |
| barryvdh/laravel-dompdf | 3.x | Génération de factures PDF |
| spatie/laravel-activitylog | 4.x | Journal d'activité |
| spatie/laravel-permission | 6.x | Gestion des permissions (installé, permissions gérées via colonne `role`) |
| PHPUnit | 11.x | Framework de tests |

### Base de données

- **PostgreSQL** (via [Neon](https://neon.tech/) en production)
- **16 migrations** couvrant : users, categories, products, services, reviews, coupons, personal_access_tokens, orders, order_items, posts, subscribers, quote_requests et les colonnes additionnelles `role` et champs de profil

---

## 🛒 Marketplace et Catalogue

Le catalogue distingue deux types de ressources :

- **Produits numériques** : téléchargements immédiats après paiement (templates, packs de prompts, scripts). Gérés via `ProductController` avec filtrage par catégorie, recherche textuelle et tri.
- **Services / Prestations** : développement sur mesure, consulting. Accessibles via `ServiceController`, avec formulaire de demande de devis (`QuoteRequestController`).

**Catégories** (initialisées par le seeder) :

- Intelligence Artificielle
- Développement Web
- Templates & Design
- Automatisation & Scripts

---

## 💳 Paiements

### Stripe (Paiement par carte)

Le flux de paiement Stripe est géré par `PaymentController` :

1. `POST /api/create-checkout-session` — crée la commande en statut `pending` et ouvre une session Stripe Checkout.
2. L'utilisateur est redirigé vers Stripe, puis vers `/order-confirmation?session_id=...` après le paiement.
3. `POST /api/stripe/webhook` — endpoint Webhook Stripe (validé par vérification cryptographique de la signature `Stripe-Signature`). À la réception de l'événement `checkout.session.completed`, la commande passe en statut `paid` et l'email de confirmation est envoyé.

> **Note :** En l'absence de clé Stripe configurée (`STRIPE_SECRET` vide ou fictive), le contrôleur bascule automatiquement sur un mode simulation (`mock session`) pour faciliter les tests locaux.

### Mobile Money (Wave CI, Orange Money, MTN MoMo)

- `POST /api/mobile-money/checkout` — valide la commande directement en statut `paid`, génère les articles et envoie l'email de confirmation.
- Implémenté côté backend Laravel. Aucun SDK tiers de passerelle Mobile Money n'est intégré dans cette version : la validation est effectuée par le contrôleur après soumission du formulaire frontend.

> **Important :** La confirmation Mobile Money dans cette version ne repose pas sur un webhook ni une confirmation temps réel de la passerelle. Ce point est à renforcer avant le lancement commercial.

---

## 🪙 Multi-devises EUR / FCFA

Un store Pinia (`stores/currency.js`) gère la conversion de devises :

- Taux fixe officiel UEMOA / BCEAO : **1 EUR = 655,957 FCFA**
- Bascule réactive dans le header de l'application
- Persistance de la préférence dans `localStorage`
- Deux modes d'affichage : prix unique dans la devise sélectionnée, ou double affichage `29.00 € (19 011 FCFA)`

---

## 🔑 Licences et Téléchargements

- Lors d'une commande Mobile Money payée, une clé de licence est générée au format `YASS-[PROVIDER]-[8 CHARS]`.
- Dans l'espace client (`ClientDashboard.vue`), les achats sont récupérés via `GET /api/user/purchases` et affichés avec leur clé de licence respective.
- La clé générée via l'API `UserController@purchases` est déterministe : `YASS-LIC-[8 premiers chars MD5(order_id.item_id)]`.

---

## 📄 Facturation PDF

Les factures PDF sont générées par `DomPDF` (`barryvdh/laravel-dompdf`) :

- **Protection anti-IDOR** : le téléchargement de facture client (`GET /api/orders/{id}/invoice`) est protégé par trois niveaux cumulatifs :
  1. URL temporaire signée via `URL::temporarySignedRoute(...)` (valable 30 jours, envoyée par email)
  2. Vérification de la propriété de la commande (email de l'utilisateur connecté)
  3. Accès admin
- **Facture admin** : `GET /api/admin/orders/{id}/invoice` — accessible uniquement aux administrateurs.

---

## 🔒 Authentification et RBAC

### Authentification

- **Laravel Sanctum** : authentification par token API (Bearer Token).
- **Email obligatoire** : le modèle `User` implémente `MustVerifyEmail`. L'inscription envoie un email de vérification.
- **Rate Limiting** : les endpoints d'authentification (`/register`, `/login`, `/forgot-password`, `/reset-password`) sont protégés par `throttle:6,1`.

### Rôles (RBAC)

La gestion des rôles est implémentée via une colonne `role` sur le modèle `User` et des méthodes helpers :

| Rôle | Permissions |
| --- | --- |
| `client` | Accès espace client : achats, factures, profil |
| `creator` | Accès admin + création de produits |
| `editor` | Accès admin + gestion du blog |
| `support` | Accès admin + gestion des devis et clients |
| `admin` | Toutes les permissions sauf gestion des rôles |
| `super_admin` | Toutes les permissions y compris modification des rôles et suppression de comptes |

---

## 🛡 Sécurité

| Mécanisme | Statut | Détail |
| --- | --- | --- |
| Authentification Sanctum | ✅ Implémenté | Token Bearer, invalidation au logout |
| Email vérifié obligatoire | ✅ Implémenté | `MustVerifyEmail` sur `User` |
| RBAC par rôle | ✅ Implémenté | 6 rôles, méthodes `isAdmin()`, `isSuperAdmin()`, etc. |
| Rate Limiting | ✅ Implémenté | `throttle:6,1` sur les endpoints d'auth |
| Anti-IDOR Factures | ✅ Implémenté | URLs temporaires signées `URL::temporarySignedRoute()` |
| Webhook Stripe signé | ✅ Implémenté | Vérification `Stripe-Signature` cryptographique |
| CORS | ⚠️ À configurer | Configurer `config/cors.php` avec le domaine du frontend en production |
| HTTPS | ⚠️ Requis | Nécessaire pour le Service Worker PWA et la sécurité générale |

---

## 📊 Dashboard Admin

Le tableau de bord administrateur (`AdminDashboard.vue`) comprend :

- Vue d'ensemble : commandes, chiffre d'affaires, utilisateurs, produits
- Gestion des commandes avec recherche, filtrage par statut et pagination
- Gestion des produits (CRUD complet)
- Gestion des utilisateurs et modification des rôles (Super Admin)
- Gestion des coupons de réduction
- Gestion des demandes de devis
- Gestion du blog (articles / posts)
- Gestion des abonnés newsletter
- Graphiques analytiques de revenus avec filtre de période (`SalesChart.vue`, données simulées / API selon configuration)
- Téléchargement des factures PDF

---

## 📱 Progressive Web App (PWA)

| Élément | Fichier | Statut |
| --- | --- | --- |
| Web App Manifest | `frontend/public/manifest.json` | ✅ Présent |
| Service Worker | `frontend/public/sw.js` | ✅ Présent |
| Stratégie de cache | Cache-First sur ressources statiques | ✅ Configuré |
| Icône | SVG 512×512 | ✅ Configuré |
| Affichage standalone | `"display": "standalone"` | ✅ Configuré |
| Enregistrement SW | Automatique si HTTPS | ✅ Dans `index.html` |

> Le Service Worker met en cache : `/`, `/index.html`, `/manifest.json`, `/favicon.svg`. Il ne fonctionne que sur HTTPS en production.

---

## 🌐 SEO et OpenGraph

Configuré dans `frontend/index.html` :

- `<title>` et `<meta name="description">` descriptifs
- Balises Open Graph complètes (type, url, title, description, image, site_name)
- Twitter Card `summary_large_image`
- `<link rel="apple-touch-icon">` pour iOS
- `<link rel="manifest">` pour la PWA

---

## 🎨 Design System

Le design system est documenté dans :

- **[design-system/MASTER.md](./design-system/MASTER.md)** — Source de vérité principale

Palette de couleurs :

| Rôle | Valeur | Variable CSS |
| --- | --- | --- |
| Primary (Indigo) | `#6366F1` | `--color-primary` |
| Secondary (Violet) | `#8B5CF6` | `--color-secondary` |
| Accent (Émeraude) | `#10B981` | `--color-accent` |
| Brand Gold | `#D4AF37` | `--color-gold` |
| Background Dark | `#0B0F19` | `--color-bg-dark` |

Typographie : `Poppins` (titres) + `Plus Jakarta Sans` / `Inter` (corps de texte) importés depuis Google Fonts.

---

## 🧪 Tests et Audit

### Résultats vérifiés

| Vérification | Résultat |
| --- | --- |
| Build Vite production | ✅ SUCCESS — 1835 modules, 0 erreur, 0 avertissement, 852 ms |
| Routes Laravel (`php artisan route:list`) | ✅ 52 routes API enregistrées |
| Modèle RBAC | ✅ 6 rôles vérifiés dans `User.php` |
| Comptes de démonstration | ✅ Présents dans `DatabaseSeeder.php` |
| Webhook Stripe | ✅ Signature cryptographique vérifiée |
| Anti-IDOR Factures | ✅ `URL::temporarySignedRoute()` implémenté |
| PWA Manifest + SW | ✅ Fichiers présents et configurés |
| SEO / OpenGraph | ✅ Balises complètes dans `index.html` |
| Convertisseur EUR / FCFA | ✅ Store Pinia `currency.js` fonctionnel |

### Tests PHPUnit

PHPUnit 11.x est installé (`phpunit/phpunit ^11.5.50`). Les tests applicatifs restent à écrire (couverture de tests unitaires et fonctionnels actuellement nulle).

---

## 💻 Installation et Démarrage Local

### Prérequis

- PHP 8.2+
- Composer
- Node.js 20+
- npm 10+
- PostgreSQL (ou SQLite pour les tests rapides en local)

### 1. Cloner le dépôt

```bash
git clone https://github.com/LeYASSOUNG/yass-digital-lab.git
cd yass-digital-lab
```

### 2. Backend (Laravel 12 API)

```bash
cd backend
composer install
```

**Linux / macOS :**

```bash
cp .env.example .env
```

**Windows PowerShell :**

```powershell
Copy-Item .env.example .env
```

```bash
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Le serveur API s'exécute sur `http://127.0.0.1:8000`.

### 3. Frontend (Vue 3 + Vite)

```bash
cd ../frontend
npm install
npm run dev
```

L'application web s'exécute sur `http://localhost:5173`.

---

## ⚙ Variables d'Environnement

Toutes les valeurs ci-dessous sont à renseigner dans `backend/.env`. Ne jamais committer de secrets.

```env
# Application
APP_NAME="Yass Digital Lab API"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://votre-backend.onrender.com
APP_KEY=                          # Généré par php artisan key:generate

# Frontend
FRONTEND_URL=https://votre-frontend.vercel.app

# Base de données PostgreSQL (Neon)
DB_CONNECTION=pgsql
DB_HOST=ep-xxx.neon.tech
DB_PORT=5432
DB_DATABASE=neondb
DB_USERNAME=
DB_PASSWORD=
DB_SSLMODE=require

# Session & Cache
SESSION_DRIVER=database
SESSION_LIFETIME=120
QUEUE_CONNECTION=database
CACHE_STORE=database

# Stripe
STRIPE_KEY=
STRIPE_SECRET=
STRIPE_WEBHOOK_SECRET=            # Secret du Webhook Stripe (Dashboard Stripe)

# Comptes démo (optionnel, overrides les valeurs par défaut du seeder)
SUPER_ADMIN_EMAIL=superadmin@yassdigital.lab
SUPER_ADMIN_PASSWORD=
```

---

## 🚀 Déploiement

### Architecture cible

| Composant | Plateforme | Statut |
| --- | --- | --- |
| Frontend SPA | Vercel (ou Netlify — `netlify.toml` présent) | Cible de déploiement |
| Backend API | Render | Cible de déploiement |
| Base de données | Neon (PostgreSQL) | Configuré dans `.env.example` |

> Les URLs de production (`https://yassdigitallab.com`, `https://yass-digital-backend.onrender.com`) sont les cibles prévues mais ne sont pas encore opérationnelles en production effective. Elles sont à configurer dans les variables d'environnement des plateformes respectives.

Un fichier `netlify.toml` est présent à la racine pour un déploiement alternatif du frontend via Netlify (répertoire `frontend/`, publication `dist/`, redirection SPA vers `index.html`).

---

## 🔒 Checklist Production

- [ ] `APP_DEBUG=false`
- [ ] `APP_ENV=production`
- [ ] HTTPS activé (obligatoire pour le Service Worker PWA)
- [ ] CORS configuré (`config/cors.php`) avec le domaine frontend
- [ ] `APP_KEY` générée et secrète
- [ ] Clés Stripe production configurées
- [ ] `STRIPE_WEBHOOK_SECRET` configuré (depuis le Dashboard Stripe)
- [ ] Credentials Mobile Money production configurés (Wave, Orange Money, MTN)
- [ ] Comptes de démonstration supprimés ou protégés
- [ ] PostgreSQL Neon production configuré
- [ ] Backups PostgreSQL configurés
- [ ] `FRONTEND_URL` et `APP_URL` vérifiés dans `.env`
- [ ] Rate Limiting vérifié
- [ ] Logs configurés (`LOG_CHANNEL`, `LOG_LEVEL`)

---

## 🔑 Comptes de Démonstration

> ⚠️ Ces identifiants sont réservés au développement et à la démonstration. Ne jamais les utiliser en production.

| Rôle | Email | Mot de passe |
| --- | --- | --- |
| **Super Admin** | `superadmin@yassdigital.lab` | `password` |
| **Admin** | `admin@yassdigital.lab` | `password` |
| **Creator** | `creator@yassdigital.lab` | `password` |
| **Editor** | `editor@yassdigital.lab` | `password` |
| **Support** | `support@yassdigital.lab` | `password` |
| **Client** | `client@yassdigital.lab` | `password` |

---

## 🗂 Structure du Projet

```text
yass-digital-lab/
├── backend/                        # API Laravel 12
│   ├── app/
│   │   ├── Http/Controllers/Api/   # 11 contrôleurs API
│   │   ├── Models/                 # 11 modèles Eloquent
│   │   ├── Mail/                   # Mailable OrderConfirmation
│   │   └── Notifications/          # Notifications Sanctum
│   ├── database/
│   │   ├── migrations/             # 16 migrations
│   │   └── seeders/                # DatabaseSeeder.php (données de démo)
│   ├── routes/
│   │   └── api.php                 # 52 routes API
│   └── .env.example
├── frontend/                       # SPA Vue 3 + Vite
│   ├── public/
│   │   ├── manifest.json           # PWA Manifest
│   │   └── sw.js                   # Service Worker
│   ├── src/
│   │   ├── components/             # 6 composants (Logo, SalesChart, ChatWidget…)
│   │   ├── stores/                 # 6 stores Pinia (auth, cart, currency…)
│   │   ├── views/                  # 17 vues (Home, Products, Admin…)
│   │   ├── router/index.js         # Vue Router 4 avec guards de navigation
│   │   ├── i18n.js                 # Traductions FR / EN
│   │   ├── api.js                  # Instance Axios configurée
│   │   └── style.css               # Design System CSS Tokens
│   └── index.html                  # Point d'entrée HTML + SEO
├── design-system/
│   └── MASTER.md                   # Design System — Source de vérité
├── netlify.toml                    # Configuration déploiement Netlify (alternatif)
└── README.md
```

---

## 🗺 Roadmap

- [x] Tests unitaires et fonctionnels PHPUnit (Suite de tests fonctionnels automatisés avec SQLite en mémoire)
- [x] Confirmation temps réel Mobile Money via webhooks des passerelles (Wave, Orange, MTN)
- [x] Gestion des téléchargements de fichiers numériques côté serveur (Stockage S3 / Cloudflare R2 & URLs temporaires signées)
- [x] Tableau de bord analytics connecté à des données réelles (REST API & agrégations SQL)
- [x] Gestion avancée des avis clients avec modération (Badges Acheteur Vérifié & Réponses Admin)
- [x] Programme d'affiliation et système de parrainage (Lien unique, 15% commission, Retraits Mobile Money & PayPal)
- [x] Application mobile PWA Améliorée (Manifest, Service Worker v2 avec navigation hors-ligne & Prompt d'installation native)

---

## 👤 Auteur

### Diarrassouba Yassoungo Youssouf

Développeur Full-Stack — Fondateur de Yass Digital Lab

🌐 Portfolio : [portfolio-tau-inky-96i2vyeddb.vercel.app](https://portfolio-tau-inky-96i2vyeddb.vercel.app/)

🐙 GitHub : [github.com/LeYASSOUNG](https://github.com/LeYASSOUNG)

---

> Le projet a passé avec succès les principales vérifications techniques (build production, routes API, RBAC, sécurité, PWA, SEO). Les dernières étapes avant le lancement public concernent principalement la configuration des services de production, des secrets, des paiements Mobile Money en temps réel, du HTTPS, du CORS et de l'infrastructure.

---

Yass Digital Lab — Outils Numériques Intelligents
