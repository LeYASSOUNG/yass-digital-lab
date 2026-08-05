<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Non authentifié.'], 401);
        }

        // Super Admin a toujours accès à tout
        if ($user->role === 'super_admin') {
            return $next($request);
        }

        // Vérification si le rôle de l'utilisateur fait partie des rôles autorisés
        if (!in_array($user->role, $roles)) {
            return response()->json([
                'message' => 'Accès interdit : privilèges insuffisants.'
            ], 403);
        }

        return $next($request);
    }
}
