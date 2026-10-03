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

            return response()->json([
                'user'         => $user,
                'message'      => 'Compte client créé avec succès. Un code de vérification vous a été envoyé.'
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Erreur de validation',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Throwable $e) {
            Log::error('Erreur Register: '.$e->getMessage());
            
            if (app()->environment('local') && (str_contains($e->getMessage(), 'Connection refused') || str_contains($e->getMessage(), 'could not connect to server') || str_contains($e->getMessage(), 'recovery'))) {
                return response()->json([
                    'user' => [
                        'id' => random_int(10, 999),
                        'name' => 'Utilisateur Mock',
                        'email' => 'mock@yass.com',
                        'role' => 'client'
                    ],
                    'message' => 'Compte client créé (MOCK OFFLINE).'
                ], 201);
            }

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
                \Illuminate\Support\Facades\Log::info("Login success: ", $credentials);
                /** @var User $user */
                $user  = Auth::user();
                $token = $user->createToken('auth_token')->plainTextToken;

                return response()->json([
                    'access_token' => $token,
                    'token_type'   => 'Bearer',
                    'user'         => $user
                ]);
            }

            \Illuminate\Support\Facades\Log::info("Login attempt failed for: ", $credentials);
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
            
            // Fallback fictif si la base de données est down (pour tester le frontend)
            if (app()->environment('local') && (str_contains($e->getMessage(), 'Connection refused') || str_contains($e->getMessage(), 'could not connect to server') || str_contains($e->getMessage(), 'recovery'))) {
                return response()->json([
                    'access_token' => 'mock_token_12345',
                    'token_type'   => 'Bearer',
                    'user'         => [
                        'id' => 1,
                        'name' => 'John Doe (Mock)',
                        'email' => $credentials['email'] ?? 'mock@yass.com',
                        'role' => 'client',
                        'email_verified_at' => now()
                    ]
                ]);
            }

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
            $user = User::where('email', $request->email)->first();
            if ($user) {
                $user->generateOtp();
                $user->notify(new \App\Notifications\ResetPasswordOtpNotification());
            }
        } catch (\Throwable $e) {
            Log::error('Erreur envoi réinitialisation mot de passe: ' . $e->getMessage());
        }

        return response()->json([
            'message' => 'Si cette adresse email est enregistrée dans notre système, ' .
                         'vous recevrez un code OTP d\'ici quelques instants.'
        ]);
    }

    /**
     * Réinitialise le mot de passe via un jeton valide.
     * Révoque tous les tokens Sanctum existants de l'utilisateur après le succès.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'otp'      => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || $user->otp_code !== $request->otp || now()->greaterThan($user->otp_expires_at)) {
            return response()->json([
                'message' => 'Le code OTP est invalide ou a expiré.'
            ], 400);
        }

        // Met à jour le mot de passe
        $user->forceFill([
            'password' => Hash::make($request->password)
        ])->setRememberToken(Str::random(60));

        $user->otp_code = null;
        $user->otp_expires_at = null;
        $user->save();

        // Déclencher l'événement standard Laravel
        event(new PasswordReset($user));

        // Révocation de tous les tokens d'accès Sanctum existants
        $user->tokens()->delete();

        return response()->json([
            'message' => 'Votre mot de passe a été réinitialisé avec succès. ' .
                         'Veuillez vous re-connecter avec vos nouveaux identifiants.'
        ]);
    }

    /**
     * Vérifie l'adresse email de l'utilisateur via une URL signée.
     */
    public function verifyEmail(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);

        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return response()->json(['message' => 'Lien de vérification invalide.'], 403);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email déjà vérifié.'], 400);
        }

        $user->markEmailAsVerified();

        // Envoi de la notification in-app de bienvenue
        $user->notify(new \App\Notifications\SystemNotification(
            'promo',
            'Bienvenue sur Yass Digital Lab !',
            'Votre compte est désormais activé. Explorez notre catalogue de templates SaaS et packs IA.',
            '/products'
        ));

        return response()->json(['message' => 'Votre adresse email a été vérifiée avec succès !']);
    }

    /**
     * Vérifie l'adresse email de l'utilisateur via le code OTP.
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp'   => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'Utilisateur introuvable.'], 404);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email déjà vérifié.'], 400);
        }

        if ($user->otp_code !== $request->otp || now()->greaterThan($user->otp_expires_at)) {
            return response()->json(['message' => 'Code OTP invalide ou expiré.'], 400);
        }

        $user->markEmailAsVerified();
        $user->otp_code = null;
        $user->otp_expires_at = null;
        $user->save();

        // Envoi de la notification in-app de bienvenue
        $user->notify(new \App\Notifications\SystemNotification(
            'promo',
            'Bienvenue sur Yass Digital Lab !',
            'Votre compte est désormais activé. Explorez notre catalogue de templates SaaS et packs IA.',
            '/products'
        ));

        // Create token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Votre adresse email a été vérifiée avec succès !',
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => $user
        ]);
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

        return response()->json(['message' => 'Un nouveau code de vérification a été envoyé à votre adresse email.']);
    }
}
