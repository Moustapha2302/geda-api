<?php

namespace App\Policies;

use App\Models\User;
use App\Models\MetadataType;
use Illuminate\Support\Facades\Log;
u

class MetadataTypePolicy
{
    public function viewAny(User $user, int $serviceId): bool
{
    Log::debug('Policy viewAny', [
        'user_id'       => $user->id,
        'user_service'  => $user->service_id,
        'route_service' => $serviceId,
        'result'        => $user->service_id === $serviceId
    ]);

    return $user->service_id === $serviceId;
}

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

    public function update(User $user, MetadataType $metadataType): bool
    {
        return $user->service_id === $metadataType->service_id && $user->hasRole('chef_' . $metadataType->service_id);
    }

    public function delete(User $user, MetadataType $metadataType): bool
    {
        return $this->update($user, $metadataType);
    }
}
