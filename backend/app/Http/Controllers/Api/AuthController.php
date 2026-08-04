<?php

/**
 * ============================================================
 * AuthController — Yass Digital Lab
 * ============================================================
 * Contrôleur responsable de l'authentification des utilisateurs.
 * Gère l'inscription, la connexion et la déconnexion via
 * Laravel Sanctum (tokens API personnels).
 *
 * Routes associées (api.php) :
 *   POST /api/register  → register()
 *   POST /api/login     → login()
 *   POST /api/logout    → logout()  [Protégé : auth:sanctum]
 * ============================================================
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Inscription d'un nouvel utilisateur client.
     *
     * Valide les données reçues, crée le compte avec le rôle 'client',
     * hash le mot de passe, génère un token Sanctum et retourne
     * le token + les informations du nouvel utilisateur.
     *
     * @param  Request $request  Données POST : name, email, password, phone?, company?, address?
     * @return \Illuminate\Http\JsonResponse  201 avec token | 422 avec erreurs de validation
     */
    public function register(Request $request)
    {
        try {
            // Validation stricte des données d'inscription
            $validated = $request->validate([
                'name'    => 'required|string|max:255',
                'email'   => 'required|email|unique:users,email', // L'email doit être unique en base
                'password'=> 'required|string|min:6',
                'phone'   => 'nullable|string|max:30',
                'company' => 'nullable|string|max:255',
                'address' => 'nullable|string|max:500',
            ], [
                'email.unique' => 'Cet email est déjà utilisé par un autre compte.'
            ]);

            // Création de l'utilisateur avec mot de passe hashé (bcrypt)
            $user = User::create([
                'name'    => $validated['name'],
                'email'   => $validated['email'],
                'password'=> Hash::make($validated['password']), // Sécurisation du mot de passe
                'role'    => 'client',                           // Rôle par défaut = client
                'phone'   => $validated['phone']   ?? null,
                'company' => $validated['company'] ?? null,
                'address' => $validated['address'] ?? null,
            ]);

            // Génération du token d'authentification API (Sanctum)
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'access_token' => $token,
                'token_type'   => 'Bearer',
                'user'         => $user,
                'message'      => 'Compte client créé avec succès'
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Erreur de validation',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Erreur Register: '.$e->getMessage());
            return response()->json([
                'message' => 'Erreur serveur lors de l\'inscription: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Connexion d'un utilisateur existant.
     *
     * Vérifie les identifiants (email + mot de passe) via Auth::attempt().
     * Si valides, génère un nouveau token Sanctum pour la session.
     *
     * @param  Request $request  Données POST : email, password
     * @return \Illuminate\Http\JsonResponse  200 avec token | 401 si identifiants incorrects
     */
    public function login(Request $request)
    {
        try {
            // Validation minimale des champs de connexion
            $credentials = $request->validate([
                'email'    => 'required|email',
                'password' => 'required'
            ]);

            // Tentative d'authentification via les guards Laravel
            if (Auth::attempt($credentials)) {
                $user  = Auth::user();
                $token = $user->createToken('auth_token')->plainTextToken;

                return response()->json([
                    'access_token' => $token,
                    'token_type'   => 'Bearer',
                    'user'         => $user
                ]);
            }

            // Retour 401 si les identifiants sont invalides
            return response()->json([
                'message' => 'Identifiants invalides'
            ], 401);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Erreur de validation',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Erreur Login: '.$e->getMessage());
            return response()->json([
                'message' => 'Erreur serveur lors de la connexion: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Déconnexion de l'utilisateur.
     *
     * Révoque uniquement le token courant (pas tous les tokens).
     * Protégé par le middleware auth:sanctum.
     *
     * @param  Request $request  Requête authentifiée avec le token actuel
     * @return \Illuminate\Http\JsonResponse  200 avec message de confirmation
     */
    public function logout(Request $request)
    {
        // Supprime uniquement le token actif utilisé pour cette requête
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Déconnexion réussie']);
    }
}
