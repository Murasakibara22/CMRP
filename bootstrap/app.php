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
        $middleware->alias([
            // 'jwt.verify' => JWTVerify::class,
            // 'admin.verify' => \App\Http\Middleware\VerifyAdmin::class,
            'customer.verify' => \App\Http\Middleware\CheckIfCustomerExist::class,
            'check.customer.login' => \App\Http\Middleware\checkIfCustomerLogin::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
