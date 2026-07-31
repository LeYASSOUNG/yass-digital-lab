<?php

/**
 * ============================================================
 * CouponController — Yass Digital Lab
 * ============================================================
 * Contrôleur de gestion des codes promo.
 * Permet de valider, lister, créer, modifier et supprimer
 * des coupons de réduction (montant fixe ou pourcentage).
 *
 * Routes associées (api.php) :
 *   POST   /api/coupons/validate   → validateCoupon()  [Public — vérification au checkout]
 *   GET    /api/coupons            → index()           [Admin]
 *   POST   /api/coupons            → store()           [Admin]
 *   PUT    /api/coupons/{id}       → update($id)       [Admin]
 *   DELETE /api/coupons/{id}       → destroy($id)      [Admin]
 * ============================================================
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CouponController extends Controller
{
    /**
     * Valide un code promo saisi par le client au moment du checkout.
     *
     * Vérifie l'existence du code ET sa date d'expiration.
     * Retourne les valeurs de réduction si le code est valide.
     *
     * @param  Request $request  POST : code (le code promo saisi par l'utilisateur)
     * @return \Illuminate\Http\JsonResponse  Réduction ou message d'erreur (404/400)
     */
    public function validateCoupon(Request $request)
    {
        // Validation minimale : le code est obligatoire
        $request->validate([
            'code' => 'required|string'
        ]);

        // Recherche du coupon en base de données par son code
        $coupon = Coupon::where('code', $request->code)->first();

        // Retourne 404 si le code n'existe pas
        if (!$coupon) {
            return response()->json(['message' => 'Code promo invalide'], 404);
        }

        // Vérification de la date d'expiration avec Carbon
        // Si expires_at est défini ET que la date est passée → code expiré
        if ($coupon->expires_at && Carbon::parse($coupon->expires_at)->isPast()) {
            return response()->json(['message' => 'Ce code promo a expiré'], 400);
        }

        // Retourne les détails de la réduction (montant fixe OU pourcentage)
        return response()->json([
            'discount_amount'     => $coupon->discount_amount,      // Réduction en euros (ex: -5€)
            'discount_percentage' => $coupon->discount_percentage,  // Réduction en % (ex: -10%)
            'message'             => 'Code promo appliqué avec succès'
        ]);
    }

    /**
     * Liste tous les coupons disponibles (Admin uniquement).
     *
     * @return \Illuminate\Http\JsonResponse  Tableau de tous les coupons
     */
    public function index()
    {
        return response()->json(Coupon::all());
    }

    /**
     * Crée un nouveau code promo (Admin uniquement).
     *
     * Un coupon peut avoir soit un montant fixe (discount_amount)
     * soit un pourcentage (discount_percentage), ou les deux.
     *
     * @param  Request $request  POST : code, discount_amount?, discount_percentage?, expires_at?
     * @return \Illuminate\Http\JsonResponse  201 avec le coupon créé
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code'                => 'required|string|unique:coupons,code', // Le code doit être unique
            'discount_amount'     => 'nullable|numeric',   // Réduction fixe en euros
            'discount_percentage' => 'nullable|numeric',   // Réduction en pourcentage
            'expires_at'          => 'nullable|date'       // Date d'expiration optionnelle
        ]);

        return response()->json(Coupon::create($validated), 201);
    }

    /**
     * Met à jour un coupon existant (Admin uniquement).
     *
     * Utilise 'sometimes' pour les mises à jour partielles.
     * La règle unique ignore l'enregistrement courant pour éviter
     * les faux conflits (unique:coupons,code,$id).
     *
     * @param  Request $request  PUT : champs à modifier
     * @param  int     $id       Identifiant du coupon
     * @return \Illuminate\Http\JsonResponse  Coupon mis à jour
     */
    public function update(Request $request, int $id)
    {
        $coupon = Coupon::findOrFail($id);

        $validated = $request->validate([
            // La règle unique ignore l'enregistrement courant pour éviter les faux conflits
            'code'                => 'sometimes|string|unique:coupons,code,' . $id,
            'discount_amount'     => 'nullable|numeric',
            'discount_percentage' => 'nullable|numeric',
            'expires_at'          => 'nullable|date'
        ]);

        $coupon->update($validated);
        return response()->json($coupon);
    }

    /**
     * Supprime un coupon (Admin uniquement).
     *
     * @param  int $id  Identifiant du coupon à supprimer
     * @return \Illuminate\Http\JsonResponse  Message de confirmation | 404 si introuvable
     */
    public function destroy(int $id)
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->delete();
        return response()->json(['message' => 'Coupon supprimé avec succès']);
    }
}
