<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Service;
use Illuminate\Support\Facades\Log;

class OwnService
{
    public function handle(Request $request, Closure $next)
    {
        // Récupère le CODE du service dans l'URL (ex: "s02")
        $serviceCode = $request->route('service');

        // Récupère l'utilisateur connecté
        $user = $request->user();

        if (!$user) {
            Log::channel('audit')->error('OwnService: User not authenticated');
            return response()->json(['message' => 'Non authentifié'], 401);
        }

        // Récupère le service_id de l'utilisateur (ex: 2)
        $userServiceId = $user->service_id;

        // Trouve le service correspondant au code dans l'URL
        $service = Service::where('code', $serviceCode)->first();

        if (!$service) {
            Log::channel('audit')->error('OwnService: Service not found', [
                'code' => $serviceCode,
            ]);
            return response()->json(['message' => 'Service introuvable'], 404);
        }

        // Compare l'ID du service trouvé avec l'ID du service de l'utilisateur
        if ($userServiceId !== $service->id) {
            Log::channel('audit')->warning('OwnService: Access denied', [
                'user_id' => $user->id,
                'user_service_id' => $userServiceId,
                'requested_service_code' => $serviceCode,
                'requested_service_id' => $service->id,
            ]);
            return response()->json(['message' => 'Accès refusé à ce service'], 403);
        }

        Log::channel('audit')->info('OwnService: Access granted', [
            'user_id' => $user->id,
            'service_code' => $serviceCode,
            'service_id' => $service->id,
        ]);

        return $next($request);
    }
}
