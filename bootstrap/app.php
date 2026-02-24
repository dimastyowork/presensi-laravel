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
        $middleware->web(append: [
            \App\Http\Middleware\ForceChangePassword::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->respond(function ($response, $e, $request) {
            if ($e instanceof \Illuminate\Session\TokenMismatchException) {
                return redirect()
                    ->route('login')
                    ->withInput($request->except(['password', '_token']))
                    ->with('error', 'Sesi Anda telah berakhir karena terlalu lama tidak aktif. Silakan mencoba login kembali.');
            }

            return $response;
        });
    })->create();
