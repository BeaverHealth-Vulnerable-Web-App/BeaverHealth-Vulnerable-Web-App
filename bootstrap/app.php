<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\CheckPermissionMiddleware;
use App\Http\Middleware\TrustProxiesMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        health: '/health',
    )
    ->withMiddleware(
        function (Middleware $middleware) {
            $middleware->use([
                TrustProxiesMiddleware::class,
            ]);
            $middleware->alias([
                'check.permission' => CheckPermissionMiddleware::class,
            ]);
        }
    )
    ->withExceptions(
        function (Exceptions $exceptions) {
            //
        }
    )->create();
