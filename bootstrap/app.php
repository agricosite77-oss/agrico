<?php

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\ApiMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;

use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
    )

    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(at: '*');

        $middleware->validateCsrfTokens(except: [
            'livewire/*',
            'livewire-*/*',
            'scan/analyze',
            'api/*',
        ]);

        // Ensure auth guards are synchronized and dynamic HTML responses are not cached stale
        $middleware->web(append: [
            \App\Http\Middleware\SyncAuthGuards::class,
            \App\Http\Middleware\NoCacheHeaders::class,
        ]);

        $middleware->alias([
            'admin' => AdminMiddleware::class,
            'api.response' => ApiMiddleware::class,
            'prevent-caching' => \App\Http\Middleware\PreventResponseCaching::class,
            'no-cache' => \App\Http\Middleware\NoCacheHeaders::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (HttpException $e, Request $request) {
            if ($e->getStatusCode() === 419) {
                if ($request->hasHeader('X-Livewire') || $request->is('livewire*')) {
                    return response()->json([
                        'components' => [],
                        'effects' => ['redirect' => $request->header('Referer') ?: $request->fullUrl()]
                    ], 200);
                }
                if ($request->is('login') || $request->is('*/login')) {
                    return redirect()->route('login')->with('error', 'Your session expired. Please log in again.');
                }
                return redirect()->guest(route('login'))->with('error', 'Your session expired. Please log in again.');
            }
        });

        $exceptions->render(function (TokenMismatchException $e, Request $request) {
            if ($request->hasHeader('X-Livewire') || $request->is('livewire*')) {
                return response()->json([
                    'components' => [],
                    'effects' => ['redirect' => $request->header('Referer') ?: $request->fullUrl()]
                ], 200);
            }
            if ($request->is('login') || $request->is('*/login')) {
                return redirect()->route('login')->with('error', 'Your session expired. Please log in again.');
            }
            return redirect()->guest(route('login'))->with('error', 'Your session expired. Please log in again.');
        });

        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })
    ->create();
