<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends Controller
{
    /**
     * Display the Admin Login view.
     */
    public function showLoginForm()
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('dashboard');
        }

        return view('admin.auth.admin-login');
    }

    /**
     * Handle an Admin Login request.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $admin = Admin::where('email', $credentials['email'])->orWhere('Email', $credentials['email'])->first();

        if ($admin) {
            $adminPassCheck = Hash::check($credentials['password'], $admin->password ?? $admin->Password ?? '');

            if ($adminPassCheck) {
                $request->session()->invalidate();
                $request->session()->regenerate();
                Auth::guard('admin')->login($admin, false);

                // Sync Web guard user session
                $userRecord = User::where('email', $admin->email ?? $admin->Email)->first();
                if (!$userRecord) {
                    $realId = $admin->admin_id ?? $admin->Admin_ID ?? ('ADM-' . str_pad(User::count() + 1, 3, '0', STR_PAD_LEFT));
                    $userRecord = User::create([
                        'user_id' => $realId,
                        'name' => $admin->full_name ?? $admin->name ?? 'Admin User',
                        'email' => $admin->email ?? $admin->Email,
                        'password' => $admin->password ?? Hash::make('Admin@Test2026!'),
                        'role' => 'admin',
                        'status' => 'Active',
                    ]);
                } else {
                    if (empty($userRecord->role) || $userRecord->role === 'user') {
                        $userRecord->role = 'admin';
                        $userRecord->save();
                    }
                }
                Auth::guard('web')->login($userRecord, false);

                $request->session()->put('admin_id', $userRecord->user_id);
                $request->session()->put('user_id', $userRecord->user_id);
                $request->session()->save();
                return redirect()->route('dashboard');
            }
        }

        // 2. Check User model table where role is admin
        $userAdmin = User::where('email', $credentials['email'])
            ->where(function($q) {
                $q->where('role', 'admin')->orWhere('role', 'superadmin');
            })
            ->first();

        if ($userAdmin) {
            $userPassCheck = Hash::check($credentials['password'], $userAdmin->password);

            if ($userPassCheck) {
                $request->session()->invalidate();
                $request->session()->regenerate();

                // Create or find matching Admin record for guard compatibility
                $adminRecord = Admin::where('email', $credentials['email'])->first();
                if ($adminRecord) {
                    Auth::guard('admin')->login($adminRecord, false);
                } else {
                    Auth::guard('admin')->login($userAdmin, false);
                }
                Auth::guard('web')->login($userAdmin, false);

                $request->session()->put('admin_id', $adminRecord->admin_id ?? $userAdmin->user_id);
                $request->session()->put('user_id', $userAdmin->user_id);
                $request->session()->save();
                return redirect()->route('dashboard');
            }
        }

        return back()->withInput($request->only('email'))->withErrors([
            'email' => 'These credentials do not match our administrative records.',
        ]);
    }

    /**
     * Display the Admin Register view.
     */
    public function showRegisterForm()
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('dashboard');
        }

        return view('admin.auth.admin-register');
    }

    /**
     * Handle an Admin Registration request.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:admins,email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // DB Transaction for atomic admin creation with identical ID across admins & users tables
        $admin = null;
        \Illuminate\Support\Facades\DB::transaction(function () use ($request, &$admin) {
            $adminIds = Admin::pluck('admin_id')
                ->concat(User::where('user_id', 'LIKE', 'ADM-%')->pluck('user_id'));
            
            $maxNum = 0;
            foreach ($adminIds as $idStr) {
                if (preg_match('/ADM-?(\d+)/i', (string)$idStr, $matches)) {
                    $num = (int)$matches[1];
                    if ($num > $maxNum) {
                        $maxNum = $num;
                    }
                }
            }
            $adminId = 'ADM-' . str_pad($maxNum + 1, 3, '0', STR_PAD_LEFT);

            // Create Admin record
            $admin = Admin::create([
                'admin_id' => $adminId,
                'full_name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Sync with User model table as admin role using exact same ID
            User::create([
                'user_id' => $adminId,
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'admin',
                'status' => 'Active',
                'last_admin_id' => $adminId,
            ]);
        });

        Auth::guard('admin')->login($admin);
        $request->session()->regenerate();
        $request->session()->save();

        return redirect()->route('dashboard');
    }

    /**
     * Log the Admin out of the application.
     */
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        Auth::guard('web')->logout();

        $request->session()->forget('admin_id');
        $request->session()->forget('user_id');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
