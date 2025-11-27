<?php

namespace App\Policies;

use App\Models\Service;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Log;

class ServicePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Service $service): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Service $service): bool
    {
return $user->service_id === $service->id && $user->role === 'chef';
  }

    /**
     * Determine whether the user can delete the model.
     */
   public function delete(User $user, Service $service): bool
{
    return $user->service_id === $service->id && $user->role === 'chef';
}

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Service $service): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Service $service): bool
    {
        return false;
    }

    // app/Policies/ServicePolicy.php
/**
 * Vérifie que l’utilisateur appartient bien au service demandé.
 */
public function access(User $user, Service $service): bool
{
    return $user->service_id === $service->id;
}

// app/Policies/ServicePolicy.php
public function chef(User $user, Service $service): bool
{
    Log::debug('Policy chef', [
        'user_id'     => $user->id,
        'user_role'   => $user->role,
        'user_svc'    => $user->service_id,
        'service_id'  => $service->id,
    ]);

    return $user->service_id === $service->id && $user->role === 'chef';
}

public function sign(User $user, Service $service): bool
{
    return $user->service_id === $service->id && $user->role === 'chef';
}

public function start(User $user, Service $service): bool
{
    return $user->service_id === $service->id && $user->role === 'chef';
}
public function validate(User $user, Service $service): bool
{
    return $user->service_id === $service->id && $user->role === 'chef';
}
public function reject(User $user, Service $service): bool
{
    return $user->service_id === $service->id && $user->role === 'chef';
}
public function pending(User $user, Service $service): bool
{
    return $user->service_id === $service->id && $user->role === 'chef';
}
 public function transfer(User $user, string $serviceId)
    {
        // Seul le chef du service peut transférer
        return $user->service_id == $serviceId && $user->role === 'chef';
    }
}
