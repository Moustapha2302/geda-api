<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\JsonResponse;

class ServiceController extends Controller
{
    public function index(): JsonResponse
    {
        $services = Service::select('id', 'code', 'name', 'logo')->get();
        return response()->json([
            'success' => true,
            'data' => $services,
            'message' => 'Liste des services'
        ]);
    }

    public function show($id): JsonResponse
    {
        $service = Service::findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $service,
            'message' => 'Détail du service'
        ]);
    }
}
