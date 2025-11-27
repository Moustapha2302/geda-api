<?php

namespace App\Policies;

use App\Models\User;
use App\Models\MetadataType;
use Illuminate\Auth\Access\Response;

class MetadataTypePolicy
{
    public function viewAny(User $user, string $service): bool
    {
        return $user->service === $service;
    }

    public function view(User $user, MetadataType $metadataType): bool
    {
        return $user->service === $metadataType->service_id;
    }

    public function create(User $user, string $service): bool
    {
        return $user->service === $service && $user->hasRole('chef_' . $service);
    }

    public function update(User $user, MetadataType $metadataType): bool
    {
        return $user->service === $metadataType->service_id && $user->hasRole('chef_' . $metadataType->service_id);
    }

    public function delete(User $user, MetadataType $metadataType): bool
    {
        return $this->update($user, $metadataType);
    }
}
