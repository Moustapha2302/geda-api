<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    protected $middlewareGroups = [
        'web' => [
          //  \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            //\App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,

    'role' => \App\Http\Middleware\RoleGlobalScope::class,
        ],

        // ────────────────────────────────
        // TON GROUPE API MIS À JOUR ICI
        // ────────────────────────────────
        'api' => [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            \Illuminate\Session\Middleware\StartSession::class,           // si tu veux les sessions côté API (ex: avec Vue/React SPA)
            'throttle:api',                                                // ← syntaxe correcte (déjà enregistrée dans $routeMiddleware)
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
        // ────────────────────────────────
    ];

    protected $routeMiddleware = [
        // … tes autres middlewares habituels …

        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        // le reste reste identique
    ];
}
