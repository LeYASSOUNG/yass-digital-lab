<?php

/**
 * ============================================================
 * CategoryController — Yass Digital Lab
 * ============================================================
 * Contrôleur de gestion des catégories de produits.
 * Expose une API publique pour lister les catégories
 * avec leur nombre de produits associés.
 *
 * Routes associées (api.php — publiques) :
 *   GET  /api/categories        → index()   [Public]
 *   GET  /api/categories/{id}   → show($id) [Public]
 * ============================================================
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CategoryController extends Controller
{
    /**
     * Liste toutes les catégories avec le nombre de produits associés.
     * Résultat mis en cache 10 minutes pour performance.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $categories = Cache::remember('categories.all', 600, function () {
            return Category::withCount('products')
                ->orderBy('name', 'asc')
                ->get();
        });

        return response()->json($categories);
    }

    /**
     * Affiche une catégorie spécifique avec ses produits.
     *
     * @param  int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id)
    {
        $category = Category::with(['products' => function ($q) {
            $q->latest()->limit(20);
        }])->findOrFail($id);

        return response()->json($category);
    }
}
