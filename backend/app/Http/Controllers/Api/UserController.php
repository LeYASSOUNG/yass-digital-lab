<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // Liste tous les utilisateurs (Réservé Super Admin / Admin)
    public function index(Request $request)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }

        $users = User::orderBy('created_at', 'desc')->get();
        return response()->json($users);
    }

    // Modifier le rôle d'un utilisateur (Exclusif Super Admin)
    public function updateRole(Request $request, int $id)
    {
        if (!$request->user()->isSuperAdmin()) {
            return response()->json(['message' => 'Réservé au Super Administrateur.'], 403);
        }

        $validated = $request->validate([
            'role' => 'required|in:client,creator,editor,support,admin,super_admin'
        ]);

        $user = User::findOrFail($id);
        $user->role = $validated['role'];
        $user->save();

        return response()->json([
            'message' => "Le rôle de {$user->name} a été mis à jour en {$user->role}.",
            'user' => $user
        ]);
    }

    // Supprimer un utilisateur (Exclusif Super Admin)
    public function destroy(Request $request, int $id)
    {
        if (!$request->user()->isSuperAdmin()) {
            return response()->json(['message' => 'Réservé au Super Administrateur.'], 403);
        }

        $user = User::findOrFail($id);

        if ($user->id === $request->user()->id) {
            return response()->json(['message' => 'Vous ne pouvez pas supprimer votre propre compte.'], 400);
        }

        $user->delete();

        return response()->json(['message' => 'Utilisateur supprimé avec succès.']);
    }
}
