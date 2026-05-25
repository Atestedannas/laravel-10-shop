<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        api: null,
        health: '/up',
        then: function () {
            Route::middleware('api')
                ->group(base_path('routes/mineadmin.php'));

            if (env('LOAD_LEGACY_API', false)) {
                Route::prefix('api')
                    ->middleware('api')
                    ->group(base_path('routes/api.php'));
            }

            if (env('LOAD_APP_API', false)) {
                Route::prefix('app-api')
                    ->middleware('api')
                    ->group(base_path('routes/app-api.php'));
            }
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'check.permission' => \App\Http\Middleware\CheckPermission::class,
            'api.auth'         => \App\Http\Middleware\ApiAuthMiddleware::class,
        ]);

        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);

        $middleware->statefulApi();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
