<?php

namespace App\Http\Controllers\Api;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => Service::select('id', 'code', 'name', 'logo')->get(),
            'message' => 'Liste des services'
        ]);
    }

    public function show($id)
    {
        $service = Service::findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $service->only('id', 'code', 'name', 'logo'),
            'message' => 'Détail du service'
        ]);
    }
}
