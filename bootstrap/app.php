<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\Permission\Middleware\RoleMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Tambahkan alias untuk middleware role
        $middleware->alias([
            'role' => RoleMiddleware::class,
        ]);

        // Tambahkan middleware CORS secara global untuk grup 'web'
        $middleware->prependToGroup('web', \Illuminate\Http\Middleware\HandleCors::class);

        // Pastikan middleware stateful API diaktifkan (untuk session dan CSRF)
        $middleware->statefulApi();

        // Kecualikan route tertentu dari verifikasi CSRF (opsional, untuk testing)
        $middleware->validateCsrfTokens(except: [
            'notifications/send',
            'notifications/reset',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
