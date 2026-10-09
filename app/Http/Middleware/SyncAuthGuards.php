<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SyncAuthGuards
{
    /**
     * Handle an incoming request.
     *
     * Ensure each browser session strictly reads ONLY the account authenticated in that session.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('logout') || $request->is('*/logout')) {
            return $next($request);
        }

        // Strictly align web guard user to match admin guard user if admin guard is authenticated in THIS session
        if (Auth::guard('admin')->check()) {
            $admin = Auth::guard('admin')->user();
            if ($admin) {
                $email = $admin->email ?? $admin->Email ?? null;
                $webUser = Auth::guard('web')->user();
                if ($email && (!$webUser || strtolower($webUser->email ?? '') !== strtolower($email))) {
                    $userRecord = \App\Models\User::where('email', $email)->first();
                    if (!$userRecord) {
                        $realId = $admin->admin_id ?? $admin->user_id ?? $admin->Admin_ID ?? ('ADM-' . str_pad(\App\Models\User::count() + 1, 3, '0', STR_PAD_LEFT));
                        $userRecord = \App\Models\User::create([
                            'user_id'       => $realId,
                            'name'          => $admin->full_name ?? $admin->name ?? 'Admin User',
                            'email'         => $email,
                            'password'      => $admin->password ?? $admin->Password ?? \Illuminate\Support\Facades\Hash::make('Admin@Test2026!'),
                            'role'          => 'admin',
                            'status'        => 'Active',
                            'last_admin_id' => $realId,
                        ]);
                    }
                    Auth::guard('web')->login($userRecord, false);
                    $request->session()->put('admin_id', $userRecord->user_id);
                    $request->session()->put('user_id', $userRecord->user_id);
                }
            }
        }

        return $next($request);
    }
}
