<?php

/**
 * ============================================================
 * AuthController — Yass Digital Lab
 * ============================================================
 * Contrôleur responsable de l'authentification des utilisateurs.
 * Gère l'inscription, la connexion, la déconnexion, la réinitialisation
 * de mot de passe et la vérification d'email via Laravel Sanctum.
 * ============================================================
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Inscription d'un nouvel utilisateur client.
     */
    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'name'    => 'required|string|max:255',
                'email'   => 'required|email|unique:users,email',
                'password'=> 'required|string|min:6',
                'phone'   => 'nullable|string|max:30',
                'company' => 'nullable|string|max:255',
                'address' => 'nullable|string|max:500',
            ], [
                'email.unique' => 'Cet email est déjà utilisé par un autre compte.'
            ]);

            $user = User::create([
                'name'    => $validated['name'],
                'email'   => $validated['email'],
                'password'=> Hash::make($validated['password']),
                'role'    => 'client',
                'phone'   => $validated['phone']   ?? null,
                'company' => $validated['company'] ?? null,
                'address' => $validated['address'] ?? null,
            ]);

            // Déclencher la notification de vérification d'email (non bloquant si SMTP échoue)
            try {
                $user->sendEmailVerificationNotification();
            } catch (\Throwable $e) {
                Log::error('Erreur envoi notification verification email: ' . $e->getMessage());
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'access_token' => $token,
                'token_type'   => 'Bearer',
                'user'         => $user,
                'message'      => 'Compte client créé avec succès. Un email de vérification vous a été envoyé.'
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Erreur de validation',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Throwable $e) {
            Log::error('Erreur Register: '.$e->getMessage());
            return response()->json([
                'message' => 'Erreur serveur lors de l\'inscription: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Connexion d'un utilisateur existant.
     */
    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email'    => 'required|email',
                'password' => 'required'
            ]);

            if (Auth::attempt($credentials)) {
                $user  = Auth::user();
                $token = $user->createToken('auth_token')->plainTextToken;

                return response()->json([
                    'access_token' => $token,
                    'token_type'   => 'Bearer',
                    'user'         => $user
                ]);
            }

            return response()->json([
                'message' => 'Identifiants invalides'
            ], 401);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Erreur de validation',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Throwable $e) {
            Log::error('Erreur Login: '.$e->getMessage());
            return response()->json([
                'message' => 'Erreur serveur lors de la connexion: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Déconnexion de l'utilisateur.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Déconnexion réussie']);
    }

    /**
     * Envoie un lien de réinitialisation de mot de passe par email.
     * Réponses génériques pour éviter l'énumération de comptes.
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        try {
            Password::sendResetLink($request->only('email'));
        } catch (\Throwable $e) {
            Log::error('Erreur envoi réinitialisation mot de passe: ' . $e->getMessage());
        }

        return response()->json([
            'message' => 'Si cette adresse email est enregistrée dans notre système, ' .
                         'vous recevrez un lien de réinitialisation d\'ici quelques instants.'
        ]);
    }

    /**
     * Réinitialise le mot de passe via un jeton valide.
     * Révoque tous les tokens Sanctum existants de l'utilisateur après le succès.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'    => 'required|string',
            'email'    => 'required|email',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.'
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                // Déclencher l'événement standard Laravel de réinitialisation
                event(new PasswordReset($user));

                // Révocation de tous les tokens d'accès Sanctum existants
                $user->tokens()->delete();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'message' => 'Votre mot de passe a été réinitialisé avec succès. ' .
                             'Veuillez vous re-connecter avec vos nouveaux identifiants.'
            ]);
        }

        return response()->json([
            'message' => 'Le jeton de réinitialisation est invalide ou a expiré.'
        ], 400);
    }

    /**
     * Vérifie l'adresse email de l'utilisateur via une URL signée.
     */
    public function verifyEmail(Request $request)
    {
        $user = User::findOrFail($request->route('id'));

        $expectedHash = hash('sha256', $user->getEmailForVerification());
        if (!hash_equals((string) $request->route('hash'), $expectedHash)) {
            return response()->json(['message' => 'Lien de vérification invalide ou altéré.'], 403);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Cette adresse email est déjà vérifiée.']);
        }

        if ($user->markEmailAsVerified()) {
            event(new \Illuminate\Auth\Events\Verified($user));
        }

        return response()->json(['message' => 'Votre adresse email a été vérifiée avec succès !']);
    }

    /**
     * Renvoie le lien de vérification d'email pour l'utilisateur connecté.
     */
    public function resendVerificationNotification(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(['message' => 'Adresse email déjà vérifiée.']);
        }

        try {
            $request->user()->sendEmailVerificationNotification();
        } catch (\Throwable $e) {
            Log::error('Erreur renvoi verification email: ' . $e->getMessage());
        }

        return response()->json(['message' => 'Un nouveau lien de vérification a été envoyé à votre adresse email.']);
    }
}
