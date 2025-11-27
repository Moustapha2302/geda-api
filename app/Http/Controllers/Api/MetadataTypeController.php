<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MetadataType;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class MetadataTypeController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, int $service)
{
    $this->authorize('viewAny', [MetadataType::class, $service]);

    return MetadataType::where('service_id', $service)->get(); // ← JSON auto
}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, int $service)
{
    $this->authorize('create', [MetadataType::class, $service]);

    $validated = $request->validate([
        'name'   => 'required|string|max:100|unique:metadata_types,name,NULL,id,service_id,' . $service,
        'fields' => 'required|array|min:1',
        'fields.*.label'    => 'required|string|max:100',
        'fields.*.type'     => 'required|in:text,number,date,select,checkbox',
        'fields.*.required' => 'boolean',
        'fields.*.order'    => 'integer|min:0',
    ]);

    return MetadataType::create([
        'service_id' => $service,
        'name'       => $validated['name'],
        'fields'     => $validated['fields'],
    ]);
}
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
