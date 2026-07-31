<?php

/**
 * ============================================================
 * UserController — Yass Digital Lab
 * ============================================================
 * Contrôleur de gestion des utilisateurs.
 * Permet aux Admins de lister/supprimer des utilisateurs,
 * au Super Admin de changer les rôles, et à chaque utilisateur
 * de mettre à jour son propre profil.
 *
 * Routes associées (api.php — toutes protégées par auth:sanctum) :
 *   GET    /api/users             → index()            [Admin / Super Admin]
 *   PUT    /api/users/{id}/role   → updateRole($id)    [Super Admin uniquement]
 *   DELETE /api/users/{id}        → destroy($id)       [Super Admin uniquement]
 *   PUT    /api/user/profile      → updateProfile()    [Tout utilisateur connecté]
 *
 * Rôles disponibles :
 *   client | creator | editor | support | admin | super_admin
 * ============================================================
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Liste tous les utilisateurs inscrits (Admin / Super Admin uniquement).
     *
     * Vérifie que l'utilisateur connecté a au minimum le rôle Admin
     * avant de retourner la liste complète, triée par date d'inscription décroissante.
     *
     * @param  Request $request  Requête authentifiée
     * @return \Illuminate\Http\JsonResponse  Liste des utilisateurs | 403 si accès refusé
     */
    public function index(Request $request)
    {
        // Vérification du rôle Admin via la méthode helper du modèle User
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }

        // Récupération triée par date d'inscription (les plus récents en premier)
        $users = User::orderBy('created_at', 'desc')->get();
        return response()->json($users);
    }

    /**
     * Modifie le rôle d'un utilisateur (Super Admin exclusivement).
     *
     * Seul le Super Admin peut élever ou rétrograder un rôle.
     * Les rôles possibles sont : client, creator, editor, support, admin, super_admin.
     *
     * @param  Request $request  PUT : role (nouveau rôle à attribuer)
     * @param  int     $id       Identifiant de l'utilisateur à modifier
     * @return \Illuminate\Http\JsonResponse  Confirmation + utilisateur mis à jour | 403 si accès refusé
     */
    public function updateRole(Request $request, int $id)
    {
        // Seul le Super Admin peut modifier les rôles
        if (!$request->user()->isSuperAdmin()) {
            return response()->json(['message' => 'Réservé au Super Administrateur.'], 403);
        }

        // Validation : le rôle doit faire partie de la liste autorisée
        $validated = $request->validate([
            'role' => 'required|in:client,creator,editor,support,admin,super_admin'
        ]);

        $user = User::findOrFail($id);
        $user->role = $validated['role'];
        $user->save();

        return response()->json([
            'message' => "Le rôle de {$user->name} a été mis à jour en {$user->role}.",
            'user'    => $user
        ]);
    }

    /**
     * Supprime un utilisateur (Super Admin exclusivement).
     *
     * Inclut une protection anti-suicide : on ne peut pas supprimer
     * son propre compte pour éviter de se bloquer soi-même.
     *
     * @param  Request $request  Requête authentifiée
     * @param  int     $id       Identifiant de l'utilisateur à supprimer
     * @return \Illuminate\Http\JsonResponse  Confirmation | 403 si accès refusé | 400 si auto-suppression
     */
    public function destroy(Request $request, int $id)
    {
        // Seul le Super Admin peut supprimer un compte
        if (!$request->user()->isSuperAdmin()) {
            return response()->json(['message' => 'Réservé au Super Administrateur.'], 403);
        }

        $user = User::findOrFail($id);

        // Protection : un Super Admin ne peut pas se supprimer lui-même
        if ($user->id === $request->user()->id) {
            return response()->json(['message' => 'Vous ne pouvez pas supprimer votre propre compte.'], 400);
        }

        $user->delete();

        return response()->json(['message' => 'Utilisateur supprimé avec succès.']);
    }

    /**
     * Met à jour le profil de l'utilisateur connecté.
     *
     * Permet à tout utilisateur authentifié de modifier ses coordonnées :
     * nom, téléphone, entreprise, adresse et photo de profil (avatar URL).
     * La mise à jour du mot de passe est gérée séparément.
     *
     * @param  Request $request  PUT : name?, phone?, company?, address?, avatar?
     * @return \Illuminate\Http\JsonResponse  Message de confirmation + utilisateur mis à jour
     */
    public function updateProfile(Request $request)
    {
        // Récupération de l'utilisateur connecté depuis le token Sanctum
        $user = $request->user();

        // Validation avec 'sometimes' = champs optionnels (mise à jour partielle possible)
        $validated = $request->validate([
            'name'    => 'sometimes|string|max:255',
            'phone'   => 'nullable|string|max:50',
            'company' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'avatar'  => 'nullable|string',              // URL de l'avatar (image ou URL externe)
        ]);

        // Mise à jour uniquement des champs fournis
        $user->update($validated);

        return response()->json([
            'message' => 'Profil mis à jour avec succès.',
            'user'    => $user
        ]);
    }
}
