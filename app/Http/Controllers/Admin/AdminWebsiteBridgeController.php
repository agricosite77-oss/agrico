<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminWebsiteBridgeController extends Controller
{
    /**
     * Bridge Admin session to Web session and redirect to Website Home.
     */
    public function redirectToWebsite(Request $request): \Illuminate\Http\RedirectResponse
    {
        $admin = Auth::guard('admin')->user() ?? Auth::guard('web')->user();

        if (!$admin) {
            if ($request->session()->has('admin_id')) {
                $admin = \App\Models\Admin::where('admin_id', $request->session()->get('admin_id'))->first()
                      ?? User::where('user_id', $request->session()->get('admin_id'))->first();
            } elseif ($request->session()->has('user_id')) {
                $admin = User::where('user_id', $request->session()->get('user_id'))->first();
            }
        }

        if ($admin) {
            $email = $admin->email ?? $admin->Email ?? null;
            if ($email) {
                $userRecord = User::where('email', $email)->first();

                if (!$userRecord) {
                    $realId = $admin->admin_id ?? $admin->user_id ?? $admin->Admin_ID ?? ('ADM-' . str_pad((string) (User::count() + 1), 3, '0', STR_PAD_LEFT));
                    $userRecord = User::create([
                        'user_id'       => $realId,
                        'name'          => $admin->full_name ?? $admin->name ?? 'Admin User',
                        'email'         => $email,
                        'password'      => $admin->password ?? $admin->Password ?? \Illuminate\Support\Facades\Hash::make('Admin@Test2026!'),
                        'role'          => 'admin',
                        'status'        => 'Active',
                        'last_admin_id' => $realId,
                    ]);
                } else {
                    if (empty($userRecord->role) || $userRecord->role === 'user') {
                        $userRecord->role = 'admin';
                        $userRecord->save();
                    }
                }

                // Authenticate web guard under the CURRENT input account ONLY
                Auth::guard('web')->login($userRecord, false);
                $request->session()->put('admin_id', $userRecord->user_id);
                $request->session()->put('user_id', $userRecord->user_id);
                $request->session()->save();
            }
        }

        return redirect()->route('website.home');
    }
}
