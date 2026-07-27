<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Service;
use App\Models\Post;
use App\Models\Order;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Compte Super Administrateur
        User::firstOrCreate(
            ['email' => 'superadmin@yassdigital.lab'],
            [
                'name' => 'Yass Super Admin',
                'password' => bcrypt('password'),
                'role' => 'super_admin',
            ]
        );

        // 2. Compte Administrateur Standard
        User::firstOrCreate(
            ['email' => 'admin@yassdigital.lab'],
            [
                'name' => 'Yass Admin',
                'password' => bcrypt('password'),
                'role' => 'admin',
            ]
        );

        // 3. Compte Client Test
        $client = User::firstOrCreate(
            ['email' => 'client@yassdigital.lab'],
            [
                'name' => 'Jean Client',
                'password' => bcrypt('password'),
                'role' => 'client',
            ]
        );

        $catIA = Category::firstOrCreate(['name' => 'Intelligence Artificielle'], ['slug' => 'intelligence-artificielle']);
        $catDev = Category::firstOrCreate(['name' => 'Développement Web'], ['slug' => 'developpement-web']);

        if (Product::count() === 0) {
            Product::create([
                'category_id' => $catIA->id,
                'title' => 'Mega Pack 500+ Prompts ChatGPT & Claude',
                'slug' => 'mega-pack-500-prompts-chatgpt-claude',
                'description' => 'Un pack complet de prompts optimisés pour la productivité, le marketing, la rédaction et le développement.',
                'price' => 19.99,
                'type' => 'Pack',
            ]);

            Product::create([
                'category_id' => $catDev->id,
                'title' => 'Template SaaS Starter Vue 3 + Laravel 12',
                'slug' => 'template-saas-starter-vue-3-laravel-12',
                'description' => 'Un starter kit prêt à l\'emploi avec authentification, paiement Stripe et design Glassmorphism.',
                'price' => 49.99,
                'type' => 'Template',
            ]);
        }

        if (Service::count() === 0) {
            Service::create([
                'title' => 'Création de site web sur mesure',
                'slug' => 'creation-de-site-web-sur-mesure',
                'description' => 'Conception de sites vitrines et e-commerce modernes, rapides et optimisés SEO.',
                'starting_price' => 299.00,
            ]);

            Service::create([
                'title' => 'Développement & Automatisation IA',
                'slug' => 'developpement-automatisation-ia',
                'description' => 'Intégration d\'agents IA et automatisation de vos workflows d\'entreprise.',
                'starting_price' => 499.00,
            ]);
        }

        if (Post::count() === 0) {
            Post::create([
                'title' => '10 Prompts IA indispensables pour booster votre productivité',
                'slug' => '10-prompts-ia-indispensables',
                'content' => "L'Intelligence Artificielle transforme notre manière de travailler au quotidien. Découvrez notre sélection des 10 meilleurs prompts à utiliser immédiatement sur ChatGPT et Claude pour automatiser vos tâches récursives et produire du contenu de haute qualité en un temps record.\n\n1. Résumer un long document en 5 points clés.\n2. Rédiger un email professionnel persuasif.\n3. Structurer le plan d'un article de blog SEO.\n\nEssayez-les dès aujourd'hui !",
                'is_published' => true,
            ]);
        }

        if (Order::count() === 0) {
            $order1 = Order::create([
                'email' => 'client@yassdigital.lab',
                'total_amount' => 19.99,
                'status' => 'paid'
            ]);
            $order1->items()->create([
                'product_title' => 'Mega Pack 500+ Prompts ChatGPT & Claude',
                'price' => 19.99,
                'quantity' => 1
            ]);

            $order2 = Order::create([
                'email' => 'client@yassdigital.lab',
                'total_amount' => 49.99,
                'status' => 'paid'
            ]);
            $order2->items()->create([
                'product_title' => 'Template SaaS Starter Vue 3 + Laravel 12',
                'price' => 49.99,
                'quantity' => 1
            ]);
        }
    }
}
