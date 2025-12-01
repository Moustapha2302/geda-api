<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use App\Models\MetadataType;
use App\Policies\MetadataTypePolicy;
use App\Models\Service;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        MetadataType::class => MetadataTypePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // ========================================
        // Gate pour vérifier l'accès à un service par son code
        // ========================================
        Gate::define('access-service', function ($user, $serviceCode) {
            Log::channel('audit')->info('=== GATE ACCESS-SERVICE CHECK ===', [
                'user_id' => $user->id,
                'user_service_id' => $user->service_id,
                'requested_service_code' => $serviceCode,
            ]);

            // Trouver le service par son code
            $service = Service::where('code', $serviceCode)->first();

            Log::channel('audit')->info('=== SERVICE FOUND ===', [
                'service_exists' => $service ? 'yes' : 'no',
                'service_id' => $service?->id,
                'service_code' => $service?->code,
            ]);

            if (!$service) {
                Log::channel('audit')->warning('Service not found', ['code' => $serviceCode]);
                return false;
            }

            $hasAccess = $user->service_id === $service->id;

            Log::channel('audit')->info('=== GATE ACCESS-SERVICE RESULT ===', [
                'has_access' => $hasAccess,
                'comparison' => "{$user->service_id} === {$service->id}",
            ]);

            return $hasAccess;
        });

        // ========================================
        // Gate pour l'archiviste (role 'ar')
        // ========================================
        Gate::define('ar', function ($user) {
            Log::channel('audit')->info('=== GATE AR CHECK ===', [
                'user_id' => $user->id,
                'user_role' => $user->role ?? 'no role field',
                'has_ar_role_spatie' => $user->hasRole('archivist'),
            ]);

            // Si vous utilisez Spatie, vérifier avec hasRole
            $hasArchivistRole = $user->hasRole(['admin', 'archivist']);

            Log::channel('audit')->info('=== GATE AR RESULT ===', [
                'result' => $hasArchivistRole,
            ]);

            return $hasArchivistRole;
        });

        // ========================================
        // Gate pour l'agent (role 'a')
        // ========================================
        Gate::define('a', function ($user) {
            Log::channel('audit')->info('=== GATE A CHECK ===', [
                'user_id' => $user->id,
                'user_role' => $user->role ?? 'no role field',
            ]);

            // Autoriser agents et archivistes
            $hasAccess = $user->hasRole(['admin', 'agent', 'archivist']);

            Log::channel('audit')->info('=== GATE A RESULT ===', [
                'result' => $hasAccess,
            ]);

            return $hasAccess;
        });

        // ========================================
        // Gate pour l'administrateur
        // ========================================
        Gate::define('admin', function ($user) {
            Log::channel('audit')->info('=== GATE ADMIN CHECK ===', [
                'user_id' => $user->id,
                'has_role_admin' => $user->hasRole('admin'),
                'roles' => $user->roles->pluck('name')->toArray(),
            ]);

            $hasAdminRole = $user->hasRole('admin');

            Log::channel('audit')->info('=== GATE ADMIN RESULT ===', [
                'result' => $hasAdminRole,
            ]);

            return $hasAdminRole;
        });

        // ========================================
        // Gate "before" pour donner accès complet aux admins
        // ========================================
        Gate::before(function ($user, $ability) {
            // Vérifier directement si l'utilisateur a le rôle admin dans la table roles
            $isAdmin = $user->roles()->where('name', 'admin')->exists();

            if ($isAdmin) {
                Log::channel('audit')->info('=== ADMIN BYPASS ===', [
                    'user_id' => $user->id,
                    'ability' => $ability,
                ]);
                return true;
            }
        });
    }
}
