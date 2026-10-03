<?php

/**
 * ============================================================
 * WishlistController — Yass Digital Lab
 * ============================================================
 * Gère les favoris produits de l'utilisateur connecté.
 * Synchronise la wishlist entre la base de données et le frontend.
 * ============================================================
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WishlistController extends Controller
{
    /**
     * Retourne la liste des produits favoris de l'utilisateur connecté.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $wishlist = DB::table('wishlists')
            ->join('products', 'wishlists.product_id', '=', 'products.id')
            ->where('wishlists.user_id', $user->id)
            ->select(
                'products.id',
                'products.title',
                'products.description',
                'products.price',
                'products.type',
                'products.image',
                'products.slug',
                'wishlists.created_at as added_at'
            )
            ->orderBy('wishlists.created_at', 'desc')
            ->get();

        return response()->json($wishlist);
    }

    /**
     * Ajoute ou retire un produit des favoris (toggle).
     * Retourne le nouvel état ('added' ou 'removed').
     */
    public function toggle(Request $request, int $productId)
    {
        $user = $request->user();

        // Vérifie que le produit existe
        $product = Product::findOrFail($productId);

        $exists = DB::table('wishlists')
            ->where('user_id', $user->id)
            ->where('product_id', $productId)
            ->exists();

        if ($exists) {
            // Retrait des favoris
            DB::table('wishlists')
                ->where('user_id', $user->id)
                ->where('product_id', $productId)
                ->delete();

            return response()->json([
                'status'  => 'removed',
                'message' => "\"{$product->title}\" retiré de vos favoris.",
                'product_id' => $productId,
            ]);
        }

        // Ajout aux favoris
        DB::table('wishlists')->insert([
            'user_id'    => $user->id,
            'product_id' => $productId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'status'  => 'added',
            'message' => "\"{$product->title}\" ajouté à vos favoris !",
            'product_id' => $productId,
        ], 201);
    }

    /**
     * Vide entièrement la wishlist de l'utilisateur.
     */
    public function clear(Request $request)
    {
        DB::table('wishlists')
            ->where('user_id', $request->user()->id)
            ->delete();

        return response()->json(['message' => 'Wishlist vidée avec succès.']);
    }
}
