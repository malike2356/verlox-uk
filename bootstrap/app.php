<?php

use App\Http\Middleware\EnsureMisAccess;
use App\Http\Middleware\EnsureMisSuperAdmin;
use App\Http\Middleware\EnsureUserIsAdmin;
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
        $middleware->alias([
            'admin' => EnsureUserIsAdmin::class,
            'mis.access' => EnsureMisAccess::class,
            'mis.super' => EnsureMisSuperAdmin::class,
        ]);
        $middleware->validateCsrfTokens(except: [
            'webhooks/stripe',
            'leads',
            'api/public/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
