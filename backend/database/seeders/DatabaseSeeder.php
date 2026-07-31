<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Service;
use App\Models\Post;
use App\Models\Order;
use App\Models\QuoteRequest;
use App\Models\Subscriber;
use App\Models\Coupon;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. COMPTES UTILISATEURS AVEC TOUS LES RÔLES
        User::firstOrCreate(
            ['email' => 'superadmin@yassdigital.lab'],
            [
                'name' => 'Yass Super Admin',
                'password' => bcrypt('password'),
                'role' => 'super_admin',
                'phone' => '+33 6 00 11 22 33',
                'company' => 'Yass Digital Lab HQ',
                'address' => 'Paris, France',
                'avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150'
            ]
        );

        User::firstOrCreate(
            ['email' => 'admin@yassdigital.lab'],
            [
                'name' => 'Yass Admin',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'phone' => '+33 6 11 22 33 44',
                'company' => 'Yass Digital Agency',
                'address' => 'Lyon, France',
                'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150'
            ]
        );

        User::firstOrCreate(
            ['email' => 'creator@yassdigital.lab'],
            [
                'name' => 'Alex Créateur',
                'password' => bcrypt('password'),
                'role' => 'creator',
                'phone' => '+33 6 22 33 44 55',
                'company' => 'Digital Product Lab',
                'address' => 'Bordeaux, France',
                'avatar' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=150'
            ]
        );

        User::firstOrCreate(
            ['email' => 'editor@yassdigital.lab'],
            [
                'name' => 'Camille Rédactrice',
                'password' => bcrypt('password'),
                'role' => 'editor',
                'phone' => '+33 6 33 44 55 66',
                'company' => 'Yass Blog Studio',
                'address' => 'Nantes, France',
                'avatar' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=150'
            ]
        );

        User::firstOrCreate(
            ['email' => 'support@yassdigital.lab'],
            [
                'name' => 'Marc Support',
                'password' => bcrypt('password'),
                'role' => 'support',
                'phone' => '+33 6 44 55 66 77',
                'company' => 'Customer Care Yass',
                'address' => 'Lille, France',
                'avatar' => 'https://images.unsplash.com/photo-1522075469751-3a6694fb2f61?w=150'
            ]
        );

        User::firstOrCreate(
            ['email' => 'client@yassdigital.lab'],
            [
                'name' => 'Jean Dupont (Client)',
                'password' => bcrypt('password'),
                'role' => 'client',
                'phone' => '+33 6 55 66 77 88',
                'company' => 'Dupont Tech Solution',
                'address' => 'Toulouse, France',
                'avatar' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=150'
            ]
        );

        // 2. CATÉGORIES
        $catIA = Category::firstOrCreate(['name' => 'Intelligence Artificielle'], ['slug' => 'intelligence-artificielle']);
        $catDev = Category::firstOrCreate(['name' => 'Développement Web'], ['slug' => 'developpement-web']);
        $catAuto = Category::firstOrCreate(['name' => 'Automatisation'], ['slug' => 'automatisation']);
        $catDesign = Category::firstOrCreate(['name' => 'Design & UX'], ['slug' => 'design-ux']);

        // 3. PRODUITS RÉELS EN CATALOGUE
        if (Product::count() === 0) {
            Product::create([
                'category_id' => $catIA->id,
                'title' => 'Mega Pack 500+ Prompts ChatGPT & Claude Pro',
                'slug' => 'mega-pack-500-prompts-chatgpt-claude-pro',
                'description' => 'Un pack complet de 500+ prompts optimisés pour la rédaction SEO, le code source, le marketing d\'acquisition et la productivité d\'entreprise.',
                'price' => 19.99,
                'type' => 'Pack',
            ]);

            Product::create([
                'category_id' => $catDev->id,
                'title' => 'Template SaaS Starter Vue 3 + Laravel 12 Glassmorphism',
                'slug' => 'template-saas-starter-vue-3-laravel-12',
                'description' => 'Starter kit prêt pour la production avec authentification Sanctum, gestion des rôles (RBAC), paiement Stripe et design ultra-soigné.',
                'price' => 49.99,
                'type' => 'Template',
            ]);

            Product::create([
                'category_id' => $catAuto->id,
                'title' => 'Kit d\'Automation N8N & Make pour Agences Tech',
                'slug' => 'kit-automation-n8n-make-agences',
                'description' => 'Ensemble de workflows automatisés prêt-à-importer pour synchroniser Stripe, vos réseaux sociaux, vos emails et Notion.',
                'price' => 39.99,
                'type' => 'Outil',
            ]);

            Product::create([
                'category_id' => $catIA->id,
                'title' => 'Guide Ultime : Développer son premier Agent IA avec Python',
                'slug' => 'guide-ultime-agent-ia-python',
                'description' => 'E-book pas-à-pas avec code fourni pour concevoir et déployer un agent IA autonome capable d\'analyser vos documents PDF et bases SQL.',
                'price' => 14.99,
                'type' => 'E-book',
            ]);

            Product::create([
                'category_id' => $catDesign->id,
                'title' => 'Dashboard Admin UI Kit Vue.js & Tailwind CSS',
                'slug' => 'dashboard-admin-ui-kit-vue-tailwind',
                'description' => 'Kit de composants d\'interface moderne comprenant graphiques analytiques, formulaires réactifs et thèmes clair/sombre.',
                'price' => 29.99,
                'type' => 'Template',
            ]);

            Product::create([
                'category_id' => $catIA->id,
                'title' => 'Pack Prompts Midjourney & DALL-E 3 pour Designers',
                'slug' => 'pack-prompts-midjourney-dalle3-designers',
                'description' => 'Plus de 200 prompts haute précision pour générer des maquettes UI, logos vectoriels et visuels photoréalistes.',
                'price' => 12.99,
                'type' => 'Pack',
            ]);
        }

        // 4. SERVICES DE PRESTATION RÉELS
        if (Service::count() === 0) {
            Service::create([
                'title' => 'Création de site web vitrine & e-commerce sur mesure',
                'slug' => 'creation-de-site-web-sur-mesure',
                'description' => 'Conception de sites vitrines et e-commerce modernes, haute performance, optimisés SEO avec intégration de paiement en ligne.',
                'starting_price' => 299.00,
            ]);

            Service::create([
                'title' => 'Développement d\'application SaaS & API Laravel / Vue.js',
                'slug' => 'developpement-application-saas-laravel-vue',
                'description' => 'Conception de plateformes web sur mesure avec architecture API RESTful, tableau de bord client, facturation automatisée et sécurité avancée.',
                'starting_price' => 899.00,
            ]);

            Service::create([
                'title' => 'Intégration d\'Agents IA & Automatisation de Workflows',
                'slug' => 'integration-agents-ia-automatisation',
                'description' => 'Développement et intégration d\'agents IA personnalisés (OpenAI/Claude) connectés à vos outils métiers pour automatiser vos tâches récurrentes.',
                'starting_price' => 499.00,
            ]);

            Service::create([
                'title' => 'Audit de Performance, SEO & Sécurité Web',
                'slug' => 'audit-performance-seo-securite-web',
                'description' => 'Analyse approfondie de votre code, temps de chargement (Core Web Vitals), failles de sécurité et recommandations concrètes d\'optimisation.',
                'starting_price' => 199.00,
            ]);
        }

        // 5. ARTICLES DE BLOG RÉELS
        if (Post::count() === 0) {
            Post::create([
                'title' => '10 Prompts IA indispensables pour booster votre productivité en 2026',
                'slug' => '10-prompts-ia-indispensables',
                'content' => "L'Intelligence Artificielle transforme notre manière de travailler au quotidien. Découvrez notre sélection des 10 meilleurs prompts à utiliser immédiatement sur ChatGPT et Claude pour automatiser vos tâches récursives et produire du contenu de haute qualité en un temps record.\n\n1. Résumer un long document en 5 points clés.\n2. Rédiger un email professionnel persuasif.\n3. Structurer le plan d'un article de blog SEO.\n4. Refactoriser un extrait de code TypeScript.\n\nEssayez-les dès aujourd'hui !",
                'is_published' => true,
            ]);

            Post::create([
                'title' => 'Pourquoi associer Vue 3 et Laravel 12 pour construire un SaaS moderne ?',
                'slug' => 'pourquoi-associer-vue-3-laravel-12-saas',
                'content' => "L'association de Vue 3 (Composition API) et de Laravel 12 représente aujourd'hui le combo idéal pour développer des applications web rapides, évolutives et sécurisées.\n\nDans cet article, nous analysons pourquoi cette stack découplée offre une expérience développeur inégalée et des performances de rendu optimales pour l'utilisateur final.",
                'is_published' => true,
            ]);

            Post::create([
                'title' => 'Comment automatiser la création de contenu avec ChatGPT et N8N',
                'slug' => 'automatiser-creation-contenu-chatgpt-n8n',
                'content' => "Automatiser ses workflows éditoriaux permet d'économiser jusqu'à 15 heures par semaine. Découvrez comment connecter l'API OpenAI à N8N pour générer, relire et planifier automatiquement vos publications sur vos réseaux et votre blog.",
                'is_published' => true,
            ]);
        }

        // 6. DEMANDES DE DEVIS RÉELLES
        if (QuoteRequest::count() === 0) {
            QuoteRequest::create([
                'name' => 'Sophie Martin',
                'email' => 'sophie.martin@martin-digital.fr',
                'service_title' => 'Création de site web vitrine & e-commerce sur mesure',
                'details' => 'Bonjour, nous souhaitons refondre notre boutique en ligne avec Vue 3 et Laravel 12, avec paiement Stripe et espace client.',
                'status' => 'pending'
            ]);

            QuoteRequest::create([
                'name' => 'Marc Durand',
                'email' => 'm.durand@techconsulting.com',
                'service_title' => 'Intégration d\'Agents IA & Automatisation de Workflows',
                'details' => 'Nous cherchons un expert pour développer un agent IA sur-mesure connecté à notre base documentaire Notion et notre CRM.',
                'status' => 'contacted'
            ]);

            QuoteRequest::create([
                'name' => 'Alexandre Leroy',
                'email' => 'a.leroy@studiocraft.io',
                'service_title' => 'Développement d\'application SaaS & API Laravel / Vue.js',
                'details' => 'Besoin d\'un accompagnement fullstack pour lancer notre MVP SaaS B2B au cours du mois prochain.',
                'status' => 'completed'
            ]);
        }

        // 7. COMMANDES & ACHATS RÉELS
        if (Order::count() === 0) {
            $order1 = Order::create([
                'email' => 'client@yassdigital.lab',
                'total_amount' => 19.99,
                'status' => 'paid'
            ]);
            $order1->items()->create([
                'product_title' => 'Mega Pack 500+ Prompts ChatGPT & Claude Pro',
                'price' => 19.99,
                'quantity' => 1
            ]);

            $order2 = Order::create([
                'email' => 'client@yassdigital.lab',
                'total_amount' => 49.99,
                'status' => 'paid'
            ]);
            $order2->items()->create([
                'product_title' => 'Template SaaS Starter Vue 3 + Laravel 12 Glassmorphism',
                'price' => 49.99,
                'quantity' => 1
            ]);

            $order3 = Order::create([
                'email' => 'sophie.martin@martin-digital.fr',
                'total_amount' => 39.99,
                'status' => 'paid'
            ]);
            $order3->items()->create([
                'product_title' => 'Kit d\'Automation N8N & Make pour Agences Tech',
                'price' => 39.99,
                'quantity' => 1
            ]);
        }

        // 8. ABONNÉS NEWSLETTER & COUPONS RÉELS
        if (Subscriber::count() === 0) {
            Subscriber::create(['email' => 'jean.dupont@gmail.com', 'is_active' => true]);
            Subscriber::create(['email' => 'sophie.martin@martin-digital.fr', 'is_active' => true]);
            Subscriber::create(['email' => 'contact@techconsulting.com', 'is_active' => true]);
        }

        if (Coupon::count() === 0) {
            Coupon::create(['code' => 'YASS20', 'discount_amount' => 5.00, 'discount_percentage' => 20]);
            Coupon::create(['code' => 'PROMO10', 'discount_amount' => 10.00, 'discount_percentage' => 10]);
        }

        // 9. AVIS & COMMENTAIRES CLIENTS RÉELS
        if (\App\Models\Review::count() === 0) {
            $p1 = Product::first();
            if ($p1) {
                \App\Models\Review::create([
                    'product_id' => $p1->id,
                    'name' => 'Alexandre M.',
                    'rating' => 5,
                    'comment' => 'Pack de prompts exceptionnel ! Gain de temps colossal pour l\'ensemble de notre équipe marketing.'
                ]);
                \App\Models\Review::create([
                    'product_id' => $p1->id,
                    'name' => 'Sarah K.',
                    'rating' => 5,
                    'comment' => 'Excellente qualité de rédaction, prompts très précis et prêts à l\'emploi.'
                ]);
            }
            $p2 = Product::skip(1)->first();
            if ($p2) {
                \App\Models\Review::create([
                    'product_id' => $p2->id,
                    'name' => 'Thomas B.',
                    'rating' => 5,
                    'comment' => 'Template SaaS ultra complet, code propre avec Vue 3 et Laravel 12. Je recommande à 100%.'
                ]);
            }
        }
    }
}
