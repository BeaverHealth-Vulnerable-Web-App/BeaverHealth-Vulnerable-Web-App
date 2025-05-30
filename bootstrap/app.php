<?php

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\CheckPermissionMiddleware;
use App\Http\Middleware\TrustProxiesMiddleware;
use App\Http\Middleware\LogUserActivityMiddleware;
use App\Http\Middleware\AjaxMiddleware;
use App\Http\Middleware\LoginPageRateLimiter;
use App\Services\UserActivityLogger;

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
                'login.page.limit' => LoginPageRateLimiter::class,
            ]);
        }
    )
    ->withExceptions(
        function (Exceptions $exceptions) {
            $exceptions->renderable(
                function (NotFoundHttpException $e) {
                    $logger = app(UserActivityLogger::class);
                    $logger->info('Unregistered route access');
                    return redirect()->route('login');
                }
            );

            $exceptions->renderable(
                function (MethodNotAllowedHttpException $e) {
                    $logger = app(UserActivityLogger::class);
                    $logger->info('Registered route accessed with wrong HTTP method');
                    return redirect()->route('login');
                }
            );
        }
    )->create();
