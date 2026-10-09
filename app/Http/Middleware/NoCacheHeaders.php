<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NoCacheHeaders
{
    /**
     * Prevent browsers from caching dynamic HTML responses.
     *
     * This fixes the stale UI bug where login/logout/account state changes
     * require Ctrl+F5 to be reflected in the rendered page.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only apply no-cache to HTML page responses, not assets/API/Livewire
        if ($request->isMethod('GET') && !$request->expectsJson() && !$request->is('livewire/*') && !$request->hasHeader('X-Livewire')) {
            $response->headers->set('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', 'Sun, 02 Jan 1990 00:00:00 GMT');
        }

        return $response;
    }
}
