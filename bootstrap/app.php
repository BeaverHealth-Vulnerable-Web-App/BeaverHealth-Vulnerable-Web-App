<?php

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\CheckPermissionMiddleware;
use App\Http\Middleware\LogUserActivityMiddleware;
use App\Services\UserActivityLogger;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        health: '/up',
    )
    ->withMiddleware(
        function (Middleware $middleware) {
            $middleware->use([
                LogUserActivityMiddleware::class,
            ]);
            $middleware->alias([
                'check.permission' => CheckPermissionMiddleware::class,
            ]);
        }
    )
    ->withExceptions(
        function (Exceptions $exceptions) {
            $exceptions->renderable(
                function (NotFoundHttpException $e) {
                    $logger = app(UserActivityLogger::class);
                    if (Auth::check()) {
                        $logger->info('User accessed unregistered route');
                        return redirect()->route('dashboard');
                    } else {
                        $logger->warning('Guest accessed unregistered route');
                        return redirect()->route('login');
                    }
                }
            );
        }
    )->create();
