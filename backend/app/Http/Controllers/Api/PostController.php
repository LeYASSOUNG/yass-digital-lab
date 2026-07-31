<?php

/**
 * ============================================================
 * PostController — Yass Digital Lab
 * ============================================================
 * Contrôleur CRUD pour la gestion des articles de blog.
 * Gère la publication, la modification et la suppression d'articles.
 * Les articles publiés sont accessibles publiquement via leur slug SEO.
 *
 * Routes associées (api.php) :
 *   GET    /api/posts           → index()       [Public — articles publiés uniquement]
 *   GET    /api/posts/{slug}    → show($slug)   [Public — par slug SEO]
 *   POST   /api/posts           → store()       [Admin]
 *   PUT    /api/posts/{id}      → update($id)   [Admin]
 *   DELETE /api/posts/{id}      → destroy($id)  [Admin]
 * ============================================================
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    /**
     * Liste tous les articles publiés, triés par date décroissante (Public).
     *
     * Filtre uniquement les articles où is_published = true.
     * Les brouillons (is_published = false) ne sont pas visibles ici.
     *
     * @return \Illuminate\Http\JsonResponse  Articles publiés, du plus récent au plus ancien
     */
    public function index()
    {
        // Filtre : uniquement les articles publiés, triés par date de création décroissante
        return response()->json(
            Post::where('is_published', true)
                ->orderBy('created_at', 'desc')
                ->get()
        );
    }

    /**
     * Affiche un article spécifique via son slug SEO (Public).
     *
     * Le slug est utilisé pour des URLs lisibles et optimisées SEO
     * (ex: /blog/comment-creer-un-site-web au lieu de /blog/42).
     *
     * @param  string $slug  Slug SEO de l'article (ex: "mon-premier-article")
     * @return \Illuminate\Http\JsonResponse  Article correspondant | 404 si introuvable
     */
    public function show(string $slug)
    {
        // firstOrFail() retourne automatiquement un 404 si le slug n'existe pas
        return response()->json(Post::where('slug', $slug)->firstOrFail());
    }

    /**
     * Crée un nouvel article de blog (Admin uniquement).
     *
     * Génère automatiquement un slug depuis le titre.
     * Gère l'upload d'une image de couverture (stockage public).
     * Par défaut, l'article peut être créé en brouillon (is_published = false).
     *
     * @param  Request $request  POST : title, content, is_published?, image?
     * @return \Illuminate\Http\JsonResponse  201 avec l'article créé
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'content'      => 'required|string',
            'is_published' => 'boolean'
        ]);

        // Génération automatique du slug SEO depuis le titre
        // ex: "Mon Super Article" → "mon-super-article"
        $validated['slug'] = Str::slug($validated['title']);

        // Gestion de l'upload de l'image de couverture du blog
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('blog', 'public');
        }

        return response()->json(Post::create($validated), 201);
    }

    /**
     * Met à jour un article existant (Admin uniquement).
     *
     * Permet la modification partielle (champs optionnels avec 'sometimes').
     * Si le titre est modifié, le slug est automatiquement régénéré.
     *
     * @param  Request $request  PUT : title?, content?, is_published?
     * @param  int     $id       Identifiant de l'article
     * @return \Illuminate\Http\JsonResponse  Article mis à jour
     */
    public function update(Request $request, int $id)
    {
        $post = Post::findOrFail($id);

        $validated = $request->validate([
            'title'        => 'sometimes|string|max:255',
            'content'      => 'sometimes|string',
            'is_published' => 'sometimes|boolean'
        ]);

        // Régénération du slug si le titre a changé (maintenir la cohérence SEO)
        if (isset($validated['title'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        $post->update($validated);
        return response()->json($post);
    }

    /**
     * Supprime définitivement un article (Admin uniquement).
     *
     * @param  int $id  Identifiant de l'article à supprimer
     * @return \Illuminate\Http\JsonResponse  Message de confirmation | 404 si introuvable
     */
    public function destroy(int $id)
    {
        $post = Post::findOrFail($id);
        $post->delete();
        return response()->json(['message' => 'Article supprimé avec succès']);
    }
}
