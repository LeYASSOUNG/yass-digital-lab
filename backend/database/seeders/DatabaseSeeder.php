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
use App\Models\Review;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /** Point d'entrée principal : orchestre tous les seeders partiels. */
    public function run(): void
    {
        $this->seedUsers();
        $categories = $this->seedCategories();
        $this->seedProducts($categories);
        $this->seedServices();
        $this->seedPosts();
        $this->seedQuoteRequests();
        $this->seedOrders();
        $this->seedSubscribersAndCoupons();
        $this->seedReviews();
    }

    /** 1. Comptes utilisateurs avec tous les rôles. */
    private function seedUsers(): void
    {
        foreach ($this->getUserDefinitions() as $data) {
            $password = $data['email'] === env('SUPER_ADMIN_EMAIL', 'superadmin@yassdigital.lab')
                ? bcrypt(env('SUPER_ADMIN_PASSWORD', 'password'))
                : bcrypt('password');
            User::firstOrCreate(
                ['email' => $data['email']],
                array_merge($data, ['password' => $password])
            );
        }
    }

    /** Définitions des utilisateurs pour seedUsers. */
    private function getUserDefinitions(): array
    {
        return [
            [
                'email'   => env('SUPER_ADMIN_EMAIL', 'superadmin@yassdigital.lab'),
                'name'    => 'Diarrassouba Yassoungo Youssouf',
                'role'    => 'super_admin',
                'phone'   => '+33 6 00 11 22 33',
                'company' => 'Yass Digital Lab HQ',
                'address' => 'Paris, France',
                'avatar'  => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150',
            ],
            [
                'email'   => 'admin@yassdigital.lab',
                'name'    => 'Yass Admin',
                'role'    => 'admin',
                'phone'   => '+33 6 11 22 33 44',
                'company' => 'Yass Digital Agency',
                'address' => 'Lyon, France',
                'avatar'  => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150',
            ],
            [
                'email'   => 'creator@yassdigital.lab',
                'name'    => 'Alex Créateur',
                'role'    => 'creator',
                'phone'   => '+33 6 22 33 44 55',
                'company' => 'Digital Product Lab',
                'address' => 'Bordeaux, France',
                'avatar'  => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=150',
            ],
            [
                'email'   => 'editor@yassdigital.lab',
                'name'    => 'Camille Rédactrice',
                'role'    => 'editor',
                'phone'   => '+33 6 33 44 55 66',
                'company' => 'Yass Blog Studio',
                'address' => 'Nantes, France',
                'avatar'  => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=150',
            ],
            [
                'email'   => 'support@yassdigital.lab',
                'name'    => 'Marc Support',
                'role'    => 'support',
                'phone'   => '+33 6 44 55 66 77',
                'company' => 'Customer Care Yass',
                'address' => 'Lille, France',
                'avatar'  => 'https://images.unsplash.com/photo-1522075469751-3a6694fb2f61?w=150',
            ],
            [
                'email'   => 'client@yassdigital.lab',
                'name'    => 'Jean Dupont (Client)',
                'role'    => 'client',
                'phone'   => '+33 6 55 66 77 88',
                'company' => 'Dupont Tech Solution',
                'address' => 'Toulouse, France',
                'avatar'  => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=150',
            ],
        ];
    }

    /**
     * 2. Catégories de produits.
     *
     * @return array<string, Category>
     */
    private function seedCategories(): array
    {
        return [
            'ia'     => Category::firstOrCreate(
                ['name' => 'Intelligence Artificielle'],
                ['slug' => 'intelligence-artificielle']
            ),
            'dev'    => Category::firstOrCreate(
                ['name' => 'Développement Web'],
                ['slug' => 'developpement-web']
            ),
            'auto'   => Category::firstOrCreate(
                ['name' => 'Automatisation'],
                ['slug' => 'automatisation']
            ),
            'design' => Category::firstOrCreate(
                ['name' => 'Design & UX'],
                ['slug' => 'design-ux']
            ),
        ];
    }

    /** 3. Produits numériques en catalogue. */
    private function seedProducts(array $categories): void
    {
        if (Product::count() !== 0) {
            return;
        }

        foreach ($this->getProductDefinitions($categories) as $data) {
            Product::create($data);
        }
    }

    /** Définitions des produits pour seedProducts. */
    private function getProductDefinitions(array $categories): array
    {
        return [
            [
                'category_id' => $categories['ia']->id,
                'title'       => 'Mega Pack 500+ Prompts ChatGPT & Claude Pro',
                'slug'        => 'mega-pack-500-prompts-chatgpt-claude-pro',
                'description' => 'Un pack complet de 500+ prompts optimisés pour la rédaction SEO, '
                    . 'le code source, le marketing d\'acquisition et la productivité.',
                'price' => 19.99,
                'type'  => 'Pack',
            ],
            [
                'category_id' => $categories['dev']->id,
                'title'       => 'Template SaaS Starter Vue 3 + Laravel 12 Glassmorphism',
                'slug'        => 'template-saas-starter-vue-3-laravel-12',
                'description' => 'Starter kit prêt pour la production avec authentification Sanctum, '
                    . 'gestion des rôles (RBAC), paiement Stripe et design soigné.',
                'price' => 49.99,
                'type'  => 'Template',
            ],
            [
                'category_id' => $categories['auto']->id,
                'title'       => 'Kit d\'Automation N8N & Make pour Agences Tech',
                'slug'        => 'kit-automation-n8n-make-agences',
                'description' => 'Ensemble de workflows automatisés prêt-à-importer pour synchroniser '
                    . 'Stripe, vos réseaux sociaux, vos emails et Notion.',
                'price' => 39.99,
                'type'  => 'Outil',
            ],
            [
                'category_id' => $categories['ia']->id,
                'title'       => 'Guide Ultime : Développer son premier Agent IA avec Python',
                'slug'        => 'guide-ultime-agent-ia-python',
                'description' => 'E-book pas-à-pas avec code fourni pour concevoir et déployer un agent IA '
                    . 'autonome capable d\'analyser vos documents PDF.',
                'price' => 14.99,
                'type'  => 'E-book',
            ],
            [
                'category_id' => $categories['design']->id,
                'title'       => 'Dashboard Admin UI Kit Vue.js & Tailwind CSS',
                'slug'        => 'dashboard-admin-ui-kit-vue-tailwind',
                'description' => 'Kit de composants d\'interface moderne comprenant graphiques analytiques, '
                    . 'formulaires réactifs et thèmes clair/sombre.',
                'price' => 29.99,
                'type'  => 'Template',
            ],
            [
                'category_id' => $categories['ia']->id,
                'title'       => 'Pack Prompts Midjourney & DALL-E 3 pour Designers',
                'slug'        => 'pack-prompts-midjourney-dalle3-designers',
                'description' => 'Plus de 200 prompts haute précision pour générer des maquettes UI, '
                    . 'logos vectoriels et visuels photoréalistes.',
                'price' => 12.99,
                'type'  => 'Pack',
            ],
        ];
    }

    /** 4. Services de prestation. */
    private function seedServices(): void
    {
        if (Service::count() !== 0) {
            return;
        }

        $services = [
            [
                'title'          => 'Création de site web vitrine & e-commerce sur mesure',
                'slug'           => 'creation-de-site-web-sur-mesure',
                'description'    => 'Conception de sites vitrines et e-commerce modernes, haute performance, '
                    . 'optimisés SEO avec intégration de paiement en ligne.',
                'starting_price' => 299.00,
            ],
            [
                'title'          => 'Développement d\'application SaaS & API Laravel / Vue.js',
                'slug'           => 'developpement-application-saas-laravel-vue',
                'description'    => 'Conception de plateformes web sur mesure avec architecture API RESTful, '
                    . 'tableau de bord client, facturation automatisée.',
                'starting_price' => 899.00,
            ],
            [
                'title'          => 'Intégration d\'Agents IA & Automatisation de Workflows',
                'slug'           => 'integration-agents-ia-automatisation',
                'description'    => 'Développement et intégration d\'agents IA personnalisés (OpenAI/Claude) '
                    . 'connectés à vos outils métiers pour automatiser.',
                'starting_price' => 499.00,
            ],
            [
                'title'          => 'Audit de Performance, SEO & Sécurité Web',
                'slug'           => 'audit-performance-seo-securite-web',
                'description'    => 'Analyse approfondie de votre code, temps de chargement (Core Web Vitals), '
                    . 'failles de sécurité et recommandations.',
                'starting_price' => 199.00,
            ],
        ];

        foreach ($services as $data) {
            Service::create($data);
        }
    }

    /** 5. Articles de blog. */
    private function seedPosts(): void
    {
        if (Post::count() !== 0) {
            return;
        }

        $posts = [
            [
                'title'        => '10 Prompts IA indispensables pour booster votre productivité en 2026',
                'slug'         => '10-prompts-ia-indispensables',
                'content'      => "L'Intelligence Artificielle transforme notre manière de travailler au quotidien. "
                    . "Découvrez notre sélection des 10 meilleurs prompts à utiliser sur ChatGPT "
                    . "et Claude pour automatiser vos tâches récursives et produire du contenu.\n\n"
                    . "1. Résumer un long document en 5 points clés.\n"
                    . "2. Rédiger un email professionnel persuasif.\n"
                    . "3. Structurer le plan d'un article de blog SEO.\n"
                    . "4. Refactoriser un extrait de code TypeScript.\n\n"
                    . "Essayez-les dès aujourd'hui !",
                'is_published' => true,
            ],
            [
                'title'        => 'Pourquoi associer Vue 3 et Laravel 12 pour construire un SaaS moderne ?',
                'slug'         => 'pourquoi-associer-vue-3-laravel-12-saas',
                'content'      => "L'association de Vue 3 (Composition API) et de Laravel 12 représente "
                    . "le combo idéal pour développer des applications web rapides, évolutives et sécurisées.\n\n"
                    . "Dans cet article, nous analysons pourquoi cette stack découplée offre une expérience "
                    . "développeur inégalée et des performances de rendu optimales.",
                'is_published' => true,
            ],
            [
                'title'        => 'Comment automatiser la création de contenu avec ChatGPT et N8N',
                'slug'         => 'automatiser-creation-contenu-chatgpt-n8n',
                'content'      => "Automatiser ses workflows éditoriaux permet d'économiser du temps. "
                    . "Découvrez comment connecter l'API OpenAI à N8N pour générer, relire "
                    . "et planifier vos publications sur vos réseaux.",
                'is_published' => true,
            ],
        ];

        foreach ($posts as $data) {
            Post::create($data);
        }
    }

    /** 6. Demandes de devis. */
    private function seedQuoteRequests(): void
    {
        if (QuoteRequest::count() !== 0) {
            return;
        }

        $quotes = [
            [
                'name'          => 'Sophie Martin',
                'email'         => 'sophie.martin@martin-digital.fr',
                'service_title' => 'Création de site web vitrine & e-commerce sur mesure',
                'details'       => 'Bonjour, nous souhaitons refondre notre boutique en ligne '
                    . 'avec Vue 3 et Laravel 12, avec paiement Stripe et espace client.',
                'status'        => 'pending',
            ],
            [
                'name'          => 'Marc Durand',
                'email'         => 'm.durand@techconsulting.com',
                'service_title' => 'Intégration d\'Agents IA & Automatisation de Workflows',
                'details'       => 'Nous cherchons un expert pour développer un agent IA sur-mesure '
                    . 'connecté à notre base documentaire Notion et notre CRM.',
                'status'        => 'contacted',
            ],
            [
                'name'          => 'Alexandre Leroy',
                'email'         => 'a.leroy@studiocraft.io',
                'service_title' => 'Développement d\'application SaaS & API Laravel / Vue.js',
                'details'       => 'Besoin d\'un accompagnement fullstack pour lancer notre MVP SaaS B2B '
                    . 'au cours du mois prochain.',
                'status'        => 'completed',
            ],
        ];

        foreach ($quotes as $data) {
            QuoteRequest::create($data);
        }
    }

    /** 7. Commandes et achats. */
    private function seedOrders(): void
    {
        if (Order::count() !== 0) {
            return;
        }

        $orders = [
            [
                'order' => ['email' => 'client@yassdigital.lab', 'total_amount' => 19.99, 'status' => 'paid'],
                'item'  => [
                    'product_title' => 'Mega Pack 500+ Prompts ChatGPT & Claude Pro',
                    'price'         => 19.99,
                    'quantity'      => 1,
                ],
            ],
            [
                'order' => ['email' => 'client@yassdigital.lab', 'total_amount' => 49.99, 'status' => 'paid'],
                'item'  => [
                    'product_title' => 'Template SaaS Starter Vue 3 + Laravel 12 Glassmorphism',
                    'price'         => 49.99,
                    'quantity'      => 1,
                ],
            ],
            [
                'order' => ['email' => 'sophie.martin@martin-digital.fr', 'total_amount' => 39.99, 'status' => 'paid'],
                'item'  => [
                    'product_title' => 'Kit d\'Automation N8N & Make pour Agences Tech',
                    'price'         => 39.99,
                    'quantity'      => 1,
                ],
            ],
        ];

        foreach ($orders as $entry) {
            $order = Order::create($entry['order']);
            $order->items()->create($entry['item']);
        }
    }

    /** 8. Abonnés newsletter et coupons de réduction. */
    private function seedSubscribersAndCoupons(): void
    {
        if (Subscriber::count() === 0) {
            $emails = [
                'jean.dupont@gmail.com',
                'sophie.martin@martin-digital.fr',
                'contact@techconsulting.com',
            ];
            foreach ($emails as $email) {
                Subscriber::create(['email' => $email, 'is_active' => true]);
            }
        }

        if (Coupon::count() === 0) {
            Coupon::create(['code' => 'YASS20',  'discount_amount' => 5.00,  'discount_percentage' => 20]);
            Coupon::create(['code' => 'PROMO10', 'discount_amount' => 10.00, 'discount_percentage' => 10]);
        }
    }

    /** 9. Avis clients sur les produits. */
    private function seedReviews(): void
    {
        if (Review::count() !== 0) {
            return;
        }

        $p1 = Product::first();
        if ($p1) {
            Review::create([
                'product_id' => $p1->id,
                'name'       => 'Alexandre M.',
                'rating'     => 5,
                'comment'    => 'Pack de prompts exceptionnel ! Gain de temps colossal '
                    . 'pour l\'ensemble de notre équipe marketing.',
            ]);
            Review::create([
                'product_id' => $p1->id,
                'name'       => 'Sarah K.',
                'rating'     => 5,
                'comment'    => 'Excellente qualité de rédaction, prompts très précis et prêts à l\'emploi.',
            ]);
        }

        $p2 = Product::skip(1)->first();
        if ($p2) {
            Review::create([
                'product_id' => $p2->id,
                'name'       => 'Thomas B.',
                'rating'     => 5,
                'comment'    => 'Template SaaS ultra complet, code propre avec Vue 3 et Laravel 12. '
                    . 'Je recommande à 100%.',
            ]);
        }
    }
}
