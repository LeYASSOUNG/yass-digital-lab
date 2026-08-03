# 🚀 Yass Digital Lab — Outils Numériques Intelligents

[![Vue.js](https://img.shields.io/badge/Vue.js-3.x-4FC08D?style=flat-square&logo=vuedotjs)](https://vuejs.org/)
[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=flat-square&logo=laravel)](https://laravel.com/)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-16.x-4169E1?style=flat-square&logo=postgresql)](https://www.postgresql.org/)
[![Herald Score](https://img.shields.io/badge/Herald_Quality-Grade_A_(91%2F100)-10B981?style=flat-square)](https://github.com/LeYASSOUNG/yass-digital-lab)

**Yass Digital Lab** est une plateforme e-commerce & SaaS d'élite permettant la vente de ressources numériques, templates web d'applications, e-books et prestations sur mesure.

---

## ✨ Fonctionnalités Principales

- **🎨 Design Ultra-Moderne & Glassmorphism** : Thème clair / sombre, animations fluides et logo officiel YDL.
- **🔐 Système Multi-Rôles (RBAC)** : Gestion fine des accès (`super_admin`, `admin`, `creator`, `editor`, `support`, `client`).
- **🛍️ Catalogue Produits Avancé** : Recherche instantanée autocomplétée, filtres par tranche de prix, démos en direct et tri réactif.
- **🛒 Panier & Codes Promo** : Réduction réactive (ex: `-20% avec YASS20`) et checkout sécurisé.
- **📄 Facturation Corporate PDF** : Génération automatique de factures PDF officielles DomPDF.
- **🔑 Clés de Licences & Fichiers** : Espace client dédié avec clés de licence uniques et suivi des téléchargements.
- **🌐 Multilingue Réactif (FR / EN)** : Traduction instantanée sans rechargement de page (`i18n.js`).
- **📊 Tableau de Bord Admin** : Graphiques analytiques de ventes, gestion des utilisateurs, export CSV et rapport financier PDF.
- **🔔 Notifications In-App & Support Chat** : Centre de notifications en direct et widget de chat.

---

## 🛠️ Stack Technique

- **Frontend** : Vue 3 (`<script setup>`), Pinia, Vue Router 4, Vanilla CSS Design System.
- **Backend API** : Laravel 12 API, Sanctum Authentication, DomPDF (`barryvdh/laravel-dompdf`).
- **Base de Données** : PostgreSQL (Neon) en production / SQLite en local.

---

## 🌐 Hébergement en Production

| Service | Plateforme | URL |
| --- | --- | --- |
| Frontend | Vercel | Votre URL Vercel |
| Backend API | Render | <https://yass-digital-backend.onrender.com> |
| Base de Données | Neon (PostgreSQL) | Neon Dashboard |

---

## 💻 Installation & Démarrage Local

### 1. Cloner le Projet

```bash
git clone https://github.com/LeYASSOUNG/yass-digital-lab.git
cd yass-digital-lab
```

### 2. Backend (Laravel API)

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Le serveur API tourne sur `http://localhost:8000`.

### 3. Frontend (Vue 3 + Vite)

```bash
cd frontend
npm install
npm run dev
```

L'application web tourne sur `http://localhost:5174`.

---

## 🔑 Comptes de Démonstration

| Rôle | Email | Mot de passe |
| --- | --- | --- |
| **Super Admin** | `superadmin@yassdigital.lab` | `password` |
| **Admin** | `admin@yassdigital.lab` | `password` |
| **Client** | `client@yassdigital.lab` | `password` |

---

Créé avec ❤️ par **Diarrassouba Yassoungo Youssouf** — *Yass Digital Lab*

🌐 **Portfolio** : [portfolio-tau-inky-96i2vyeddb.vercel.app](https://portfolio-tau-inky-96i2vyeddb.vercel.app/)
