<?php

namespace App\Policies;

use App\Models\User;
use App\Models\MetadataType;
use Illuminate\Support\Facades\Log;

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

    public function create(User $user, int $serviceId): bool
{
    Log::debug('Policy create', [
        'user_id'       => $user->id,
        'user_service'  => $user->service_id,
        'route_service' => $serviceId,
        'hasRole'       => $user->hasRole('chef_' . $serviceId),
        'result'        => $user->service_id === $serviceId && $user->hasRole('chef_' . $serviceId)
    ]);

    return $user->service_id === $serviceId && $user->hasRole('chef_' . $serviceId);
}

    public function update(User $user, ?MetadataType $metadataType): bool
{
    if (! $metadataType) return false;

    return (int) $user->service_id === (int) $metadataType->service_id
           && $user->hasRole('chef_' . $metadataType->service_id);
}
    public function delete(User $user, MetadataType $metadataType): bool
    {
        return $this->update($user, $metadataType);
    }
}
