<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\VerifyAdminApiKey;
use App\Http\Middleware\VerifyUserApiKey;
use App\Http\Middleware\CheckIfBlocked;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
        $middleware->alias([
            'Admin-middleware'=>VerifyAdminApiKey::class,
            'User-middleware'=>VerifyUserApiKey::class,
            'Blocked'=>CheckIfBlocked::class
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
