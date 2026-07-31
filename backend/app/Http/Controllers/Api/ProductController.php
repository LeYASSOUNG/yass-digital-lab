<?php

/**
 * ============================================================
 * ProductController — Yass Digital Lab
 * ============================================================
 * Contrôleur CRUD pour la gestion des produits numériques.
 * Gère la liste, l'affichage, la création, la modification,
 * la suppression et l'ajout d'avis clients.
 *
 * Routes associées (api.php) :
 *   GET    /api/products             → index()           [Public]
 *   GET    /api/products/{id}        → show($id)         [Public]
 *   POST   /api/products             → store()           [Admin]
 *   PUT    /api/products/{id}        → update($id)       [Admin]
 *   DELETE /api/products/{id}        → destroy($id)      [Admin]
 *   POST   /api/products/{id}/reviews → addReview($id)   [Public]
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
     * Liste tous les produits avec leur catégorie et leurs avis clients.
     *
     * Utilise eager loading (with) pour éviter le problème N+1.
     *
     * @return \Illuminate\Http\JsonResponse  Tableau de produits avec category et reviews
     */
    public function index()
    {
        // Eager loading des relations : catégorie + avis pour chaque produit
        return response()->json(Product::with(['category', 'reviews'])->get());
    }

    /**
     * Affiche un produit spécifique avec sa catégorie et ses avis.
     *
     * @param  int $id  Identifiant du produit
     * @return \Illuminate\Http\JsonResponse  Produit + category + reviews | 404 si introuvable
     */
    public function show(int $id)
    {
        return response()->json(Product::with(['category', 'reviews'])->findOrFail($id));
    }

    /**
     * Crée un nouveau produit (Admin uniquement).
     *
     * Génère automatiquement un slug depuis le titre.
     * Gère l'upload d'image (stockage public) et de fichier (stockage privé local).
     *
     * @param  Request $request  POST : title, category_id, description, price, type?, image?, file?
     * @return \Illuminate\Http\JsonResponse  201 avec le produit créé
     */
    public function store(Request $request)
    {
        // Validation des champs obligatoires et optionnels
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id', // Vérification que la catégorie existe
            'description' => 'required|string',
            'price'       => 'required|numeric',
            'type'        => 'nullable|string',
        ]);

        // Génération du slug SEO depuis le titre (ex: "Mon Produit" → "mon-produit")
        $validated['slug'] = Str::slug($validated['title']);

        // Gestion de l'upload de l'image de couverture du produit (stockage public)
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products/images', 'public');
        }

        // Gestion de l'upload du fichier téléchargeable (stockage privé local)
        if ($request->hasFile('file')) {
            $validated['download_url'] = $request->file('file')->store('products/files', 'local');
        }

        $product = Product::create($validated);
        return response()->json($product, 201);
    }

    /**
     * Met à jour un produit existant (Admin uniquement).
     *
     * Utilise 'sometimes' pour autoriser les mises à jour partielles
     * (on ne doit pas obligatoirement envoyer tous les champs).
     *
     * @param  Request $request  PUT : champs à mettre à jour
     * @param  int     $id       Identifiant du produit
     * @return \Illuminate\Http\JsonResponse  Produit mis à jour
     */
    public function update(Request $request, int $id)
    {
        $product = Product::findOrFail($id);

        // 'sometimes' = le champ est facultatif, validé seulement s'il est présent
        $validated = $request->validate([
            'title'       => 'sometimes|string|max:255',
            'category_id' => 'sometimes|exists:categories,id',
            'description' => 'sometimes|string',
            'price'       => 'sometimes|numeric',
            'type'        => 'nullable|string',
        ]);

        // Si le titre est modifié, on régénère le slug automatiquement
        if (isset($validated['title'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        $product->update($validated);
        return response()->json($product);
    }

    /**
     * Supprime un produit (Admin uniquement).
     *
     * @param  int $id  Identifiant du produit à supprimer
     * @return \Illuminate\Http\JsonResponse  Message de confirmation | 404 si introuvable
     */
    public function destroy(int $id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return response()->json(['message' => 'Produit supprimé avec succès']);
    }

    /**
     * Ajoute un avis client sur un produit (Public).
     *
     * Crée un enregistrement Review lié au produit ciblé.
     * Les avis sont visibles immédiatement (pas de modération actuellement).
     *
     * @param  Request $request  POST : name, rating (1-5), comment
     * @param  int     $id       Identifiant du produit à noter
     * @return \Illuminate\Http\JsonResponse  201 avec l'avis créé
     */
    public function addReview(Request $request, int $id)
    {
        // Validation de l'avis : note obligatoire entre 1 et 5 étoiles
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'required|string',
        ]);

        // Liaison de l'avis au produit via son ID
        $validated['product_id'] = $id;

        $review = \App\Models\Review::create($validated);
        return response()->json(['message' => 'Avis ajouté avec succès !', 'review' => $review], 201);
    }
}
