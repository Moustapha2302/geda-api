<?php

namespace App\Policies;

use App\Models\User;
use App\Models\MetadataType;

class MetadataTypePolicy
{
    public function viewAny(User $user, int $serviceId): bool
    {
        return $user->service_id === $serviceId;
    }

    public function create(User $user, int $serviceId): bool
    {
        return $user->service_id === $serviceId && $user->hasRole('chef_' . $serviceId);
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
