<?php

use App\Http\Middleware\CheckRole;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => CheckRole::class,
        ]);

        // When an already-authenticated user visits a 'guest'-only route
        // (e.g. /login), redirect them to their role-specific dashboard.
        RedirectIfAuthenticated::redirectUsing(function ($request) {
            $user = $request->user();
            return match ($user?->role) {
                'admin' => '/admin/dashboard',
                'owner' => '/owner/dashboard',
                default => '/',
            };
        });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

