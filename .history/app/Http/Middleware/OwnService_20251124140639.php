<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class OwnService
{
    public function handle(Request $request, Closure $next)
    {
        // Récupère le service dans l’URL
        $service = $request->route('service');

        // Récupère le service de l’utilisateur connecté
        $userService = $request->user()->service_id;

        if ((string) $userService !== (string) $service) {
            return response()->json(['message' => 'Accès refusé à ce service'], 403);
        }

        return $next($request);
    }
}
