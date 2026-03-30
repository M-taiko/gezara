<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Register custom route middleware
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
            'permission' => \App\Http\Middleware\CheckPermission::class,
        ]);

        // Register global middleware
        $middleware->append(\App\Http\Middleware\LogActivity::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Redirect method-not-allowed (e.g. GET /login) to signin
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException $e, \Illuminate\Http\Request $request) {
            return redirect()->route('signin');
        });
    })->create();
