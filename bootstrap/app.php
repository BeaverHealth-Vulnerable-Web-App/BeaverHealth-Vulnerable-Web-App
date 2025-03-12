<?php

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\CheckPermissionMiddleware;
use App\Http\Middleware\TrustProxiesMiddleware;
use App\Http\Middleware\LogUserActivityMiddleware;
use App\Http\Middleware\AjaxMiddleware;
use App\Providers\EventServiceProvider;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        health: '/health',
    )
    ->withMiddleware(
        function (Middleware $middleware) {
            $middleware->use([
                TrustProxiesMiddleware::class,
                LogUserActivityMiddleware::class,
            ]);
            $middleware->alias([
                'check.permission' => CheckPermissionMiddleware::class,
                'ajax' => AjaxMiddleware::class,
            ]);
        }
    )
    ->withProviders([
        EventServiceProvider::class,
    ])
    ->withExceptions(
        function (Exceptions $exceptions) {
            $exceptions->renderable(function (NotFoundHttpException $e, $request) {
                if (auth()->check()) {
                    return redirect()->route('dashboard');
                }

                return redirect()->route('login');
            });
        }
    )->create();
