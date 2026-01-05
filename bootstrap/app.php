<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        channels: __DIR__ . '/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Redirect guests based on guard
        $middleware->redirectGuestsTo(function ($request) {
            // If trying to access admin routes, redirect to admin login
            if ($request->is('admin', 'admin/*')) {
                return route('admin.login');
            }
            // Otherwise redirect to member login
            return route('login');
        });

        // Redirect authenticated users based on guard
        $middleware->redirectUsersTo(function ($request) {
            // Admin users go to admin dashboard
            if (auth()->guard('web')->check()) {
                return route('admin.dashboard');
            }
            // Member users go to messages
            if (auth()->guard('member')->check()) {
                return route('messages');
            }
            // Default fallback
            return '/';
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
