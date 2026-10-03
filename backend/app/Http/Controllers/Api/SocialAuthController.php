<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class SocialAuthController extends Controller
{
    /**
     * Redirect the user to the provider authentication page.
     */
    public function redirect(string $provider)
    {
        try {
            /** @var \Laravel\Socialite\Two\AbstractProvider $driver */
            $driver = Socialite::driver($provider);
            $url = $driver->stateless()->redirect()->getTargetUrl();
            return response()->json(['url' => $url]);
        } catch (\Exception $e) {
            Log::error("Social Auth Redirect Error: " . $e->getMessage());
            return response()->json(['message' => 'Provider non supporté ou mal configuré.'], 400);
        }
    }

    /**
     * Obtain the user information from the provider.
     */
    public function callback(string $provider)
    {
        try {
            /** @var \Laravel\Socialite\Two\AbstractProvider $driver */
            $driver = Socialite::driver($provider);
            $socialUser = $driver->stateless()->user();
            
            // Check if user already exists
            $user = User::where('email', $socialUser->getEmail())->first();

            if ($user) {
                // Update social auth details if they are empty
                if (!$user->auth_provider) {
                    $user->auth_provider = $provider;
                    $user->auth_provider_id = $socialUser->getId();
                }
                if (!$user->avatar_url && $socialUser->getAvatar()) {
                    $user->avatar_url = $socialUser->getAvatar();
                }
                // Mark email as verified if it wasn't
                if (!$user->email_verified_at) {
                    $user->email_verified_at = now();
                }
                $user->save();
            } else {
                // Create a new user
                $user = User::create([
                    'name'              => $socialUser->getName() ?? $socialUser->getNickname() ?? 'User',
                    'email'             => $socialUser->getEmail(),
                    'password'          => bcrypt(Str::random(24)), // Random password
                    'email_verified_at' => now(), // Automatically verified
                    'auth_provider'     => $provider,
                    'auth_provider_id'  => $socialUser->getId(),
                    'avatar_url'        => $socialUser->getAvatar(),
                ]);
            }

            // Generate token
            $token = $user->createToken('auth_token')->plainTextToken;
            
            // Encode the token and user data to pass via URL
            $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173') . '/auth/callback';
            
            return redirect()->away($frontendUrl . '?token=' . urlencode($token));

        } catch (\Exception $e) {
            Log::error("Social Auth Callback Error: " . $e->getMessage());
            
            $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173') . '/login';
            return redirect()->away($frontendUrl . '?error=' . urlencode('Erreur lors de la connexion sociale.'));
        }
    }
}
