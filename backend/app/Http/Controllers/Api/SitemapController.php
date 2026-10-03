<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class SitemapController extends Controller
{
    public function generate()
    {
        $products = Product::all();
        $baseUrl = env('FRONTEND_URL', 'https://yass-digital-lab.com');

        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        // Accueil
        $xml .= '<url>';
        $xml .= '<loc>' . $baseUrl . '/</loc>';
        $xml .= '<changefreq>daily</changefreq>';
        $xml .= '<priority>1.0</priority>';
        $xml .= '</url>';

        // Produits (Catalogue)
        $xml .= '<url>';
        $xml .= '<loc>' . $baseUrl . '/products</loc>';
        $xml .= '<changefreq>daily</changefreq>';
        $xml .= '<priority>0.9</priority>';
        $xml .= '</url>';

        // Pages produits dynamiques
        foreach ($products as $product) {
            $xml .= '<url>';
            $xml .= '<loc>' . $baseUrl . '/products/' . $product->id . '</loc>';
            $xml .= '<lastmod>' . $product->updated_at->toAtomString() . '</lastmod>';
            $xml .= '<changefreq>weekly</changefreq>';
            $xml .= '<priority>0.8</priority>';
            $xml .= '</url>';
        }

        $xml .= '</urlset>';

        return response($xml, 200)->header('Content-Type', 'text/xml');
    }
}
