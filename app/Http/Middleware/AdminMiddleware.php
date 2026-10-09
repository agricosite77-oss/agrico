<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request for Admin Panel routes.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::guard('admin')->check()) {
            return $next($request);
        }

        if (Auth::check()) {
            $user = Auth::user();
            $role = strtolower($user->role ?? '');
            if (in_array($role, ['admin', 'super admin', 'superadmin'])) {
                return $next($request);
            }

            // Member trying to access Admin panel -> Redirect to public website
            return redirect()->route('website.home')->with('error', 'Access denied. Administrator privileges required.');
        }

        // Unauthenticated -> Redirect to unified login form
        return redirect()->route('login');
    }
}
