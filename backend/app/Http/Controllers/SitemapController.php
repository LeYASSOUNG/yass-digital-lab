<?php

/**
 * ============================================================
 * SitemapController — Yass Digital Lab
 * ============================================================
 * Génère un sitemap XML dynamique SEO-friendly conforme au
 * protocole sitemaps.org (Google, Bing, etc.).
 *
 * Inclut :
 *  - Pages statiques (accueil, produits, services, blog, à propos...)
 *  - Produits numériques (dynamiques)
 *  - Articles de blog (dynamiques)
 *  - Formations E-learning (dynamiques)
 *
 * Routes (web.php — sans authentification) :
 *   GET /sitemap.xml  → sitemap XML complet
 *   GET /robots.txt   → fichier robots avec lien sitemap
 * ============================================================
 */

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Post;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Génère et retourne le sitemap XML complet.
     */
    public function index(): Response
    {
        $frontendUrl = rtrim(env('FRONTEND_URL', 'http://localhost:5173'), '/');
        $now = now()->toAtomString();

        $urls = [];

        // -------------------------------------------------------
        // 1. Pages Statiques
        // -------------------------------------------------------
        $staticPages = [
            ['loc' => '/',         'priority' => '1.0', 'changefreq' => 'daily'],
            ['loc' => '/products', 'priority' => '0.9', 'changefreq' => 'daily'],
            ['loc' => '/services', 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['loc' => '/courses',  'priority' => '0.8', 'changefreq' => 'weekly'],
            ['loc' => '/blog',     'priority' => '0.7', 'changefreq' => 'daily'],
            ['loc' => '/about',    'priority' => '0.5', 'changefreq' => 'monthly'],
            ['loc' => '/contact',  'priority' => '0.5', 'changefreq' => 'monthly'],
            ['loc' => '/login',    'priority' => '0.3', 'changefreq' => 'yearly'],
            ['loc' => '/register', 'priority' => '0.3', 'changefreq' => 'yearly'],
        ];

        foreach ($staticPages as $page) {
            $urls[] = [
                'loc'        => $frontendUrl . $page['loc'],
                'lastmod'    => $now,
                'changefreq' => $page['changefreq'],
                'priority'   => $page['priority'],
            ];
        }

        // -------------------------------------------------------
        // 2. Pages Produits Numériques
        // -------------------------------------------------------
        $products = Product::select('id', 'slug', 'updated_at')->orderBy('updated_at', 'desc')->get();
        foreach ($products as $product) {
            $urls[] = [
                'loc'        => $frontendUrl . '/products/' . $product->id,
                'lastmod'    => $product->updated_at->toAtomString(),
                'changefreq' => 'weekly',
                'priority'   => '0.8',
            ];
        }

        // -------------------------------------------------------
        // 3. Articles de Blog
        // -------------------------------------------------------
        $posts = Post::select('id', 'slug', 'updated_at')
            ->where('is_published', true)
            ->orderBy('updated_at', 'desc')
            ->get();

        foreach ($posts as $post) {
            $urls[] = [
                'loc'        => $frontendUrl . '/blog/' . ($post->slug ?: $post->id),
                'lastmod'    => $post->updated_at->toAtomString(),
                'changefreq' => 'monthly',
                'priority'   => '0.6',
            ];
        }

        // -------------------------------------------------------
        // 4. Formations E-learning
        // -------------------------------------------------------
        $courses = DB::table('courses')->select('id', 'updated_at')->orderBy('updated_at', 'desc')->get();
        foreach ($courses as $course) {
            $urls[] = [
                'loc'        => $frontendUrl . '/courses/' . $course->id,
                'lastmod'    => now()->toAtomString(),
                'changefreq' => 'monthly',
                'priority'   => '0.7',
            ];
        }

        // -------------------------------------------------------
        // Construction du XML
        // -------------------------------------------------------
        $xml = $this->buildXml($urls);

        return response($xml, 200, [
            'Content-Type'  => 'application/xml; charset=UTF-8',
            'Cache-Control' => 'public, max-age=3600', // Cache 1 heure
        ]);
    }

    /**
     * Génère le fichier robots.txt avec référence au sitemap.
     */
    public function robots(): Response
    {
        $frontendUrl = rtrim(env('FRONTEND_URL', 'http://localhost:5173'), '/');
        $backendUrl  = rtrim(env('APP_URL', 'http://localhost:8000'), '/');

        $content = "User-agent: *\n"
            . "Allow: /\n\n"
            . "# Pages privées — accès interdit aux robots\n"
            . "Disallow: /admin\n"
            . "Disallow: /dashboard\n"
            . "Disallow: /checkout\n"
            . "Disallow: /order-confirmation\n"
            . "Disallow: /api/\n\n"
            . "# Sitemap\n"
            . "Sitemap: {$backendUrl}/sitemap.xml\n";

        return response($content, 200, [
            'Content-Type' => 'text/plain',
        ]);
    }

    /**
     * Construit la chaîne XML du sitemap.
     */
    private function buildXml(array $urls): string
    {
        $xml  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($urls as $url) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars($url['loc']) . "</loc>\n";
            $xml .= "    <lastmod>{$url['lastmod']}</lastmod>\n";
            $xml .= "    <changefreq>{$url['changefreq']}</changefreq>\n";
            $xml .= "    <priority>{$url['priority']}</priority>\n";
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';
        return $xml;
    }
}
