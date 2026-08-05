<?php

/**
 * ============================================================
 * ProductController — Yass Digital Lab
 * ============================================================
 * Contrôleur CRUD pour la gestion des produits numériques.
 * Prend en charge la recherche texte, le filtrage par catégorie/prix/type,
 * le tri et la pagination.
 * ============================================================
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Liste les produits avec recherche, filtres, tri et pagination.
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'reviews']);

        // Recherche textuelle
        if ($request->filled('q')) {
            $q = strtolower($request->input('q'));
            $query->where(function ($sub) use ($q) {
                $sub->where('title', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            });
        }

        // Filtre par catégorie
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        // Filtre par type
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        // Filtre tranche de prix
        if ($request->filled('min_price')) {
            $query->where('price', '>=', (float) $request->input('min_price'));
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', (float) $request->input('max_price'));
        }

        // Tri
        $sort = $request->input('sort', 'default');
        if ($sort === 'price-asc') {
            $query->orderBy('price', 'asc');
        } elseif ($sort === 'price-desc') {
            $query->orderBy('price', 'desc');
        } elseif ($sort === 'title-asc') {
            $query->orderBy('title', 'asc');
        } else {
            $query->latest();
        }

        // Option pour retourner la liste non paginée (vue frontend legacy)
        if ($request->boolean('all')) {
            return response()->json($query->get());
        }

        $perPage = (int) $request->input('per_page', 10);
        return response()->json($query->paginate($perPage));
    }

    /**
     * Affiche un produit spécifique avec sa catégorie et ses avis.
     */
    public function show(int $id)
    {
        return response()->json(Product::with(['category', 'reviews'])->findOrFail($id));
    }

    /**
     * Crée un nouveau produit (Admin uniquement).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'price'       => 'required|numeric',
            'type'        => 'nullable|string',
        ]);

        $validated['slug'] = Str::slug($validated['title']);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products/images', 'public');
        }

        if ($request->hasFile('file')) {
            $validated['download_url'] = $request->file('file')->store('products/files', 'local');
        }

        $product = Product::create($validated);
        return response()->json($product, 201);
    }

    /**
     * Met à jour un produit existant (Admin uniquement).
     */
    public function update(Request $request, int $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'title'       => 'sometimes|string|max:255',
            'category_id' => 'sometimes|exists:categories,id',
            'description' => 'sometimes|string',
            'price'       => 'sometimes|numeric',
            'type'        => 'nullable|string',
        ]);

        if (isset($validated['title'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        $product->update($validated);
        return response()->json($product);
    }

    /**
     * Supprime un produit (Admin uniquement).
     */
    public function destroy(int $id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return response()->json(['message' => 'Produit supprimé avec succès']);
    }

    /**
     * Ajoute un avis client sur un produit.
     */
    public function addReview(Request $request, int $id)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'required|string',
        ]);

        $validated['product_id'] = $id;

        $review = \App\Models\Review::create($validated);
        return response()->json(['message' => 'Avis ajouté avec succès !', 'review' => $review], 201);
    }
}
