<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ManageUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->has('role')) {
            $query->where('role', $request->query('role'));
        }

        if ($request->has('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('user_id', 'like', "%{$search}%");
            });
        }

        return response()->json([
            'status' => 'success',
            'data' => $query->paginate(15),
        ]);
    }

    public function show(User $user)
    {
        return response()->json([
            'status' => 'success',
            'data' => $user->loadCount('soilScans'),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => [
                'sometimes',
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->user_id, 'user_id'),
            ],
            'role' => ['sometimes', 'required', 'string', Rule::in(['member', 'student', 'admin'])],
            'status' => 'sometimes|required|string',
            'password' => 'nullable|string|min:6',
        ]);

        if (strtolower(trim($user->email)) === 'admin@agrico.com' && isset($validated['role']) && strtolower(trim($validated['role'])) !== 'superadmin') {
            return response()->json([
                'status' => 'error',
                'message' => 'The main Super Admin role cannot be changed.',
            ], 422);
        }

        if (isset($validated['name'])) {
            $user->name = $validated['name'];
        }

        if (isset($validated['email'])) {
            $user->email = $validated['email'];
        }

        if (isset($validated['status'])) {
            $user->status = $validated['status'];
        }

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        if (isset($validated['role'])) {
            $user->syncRoleAndAdminId($validated['role']);
        } else {
            $user->save();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'User account updated successfully.',
            'data' => $user->fresh(),
        ]);
    }

    public function destroy(User $user)
    {
        if (strtolower(trim($user->email)) === 'admin@agrico.com') {
            return response()->json([
                'status' => 'error',
                'message' => 'Main Super Admin account cannot be deleted.',
            ], 403);
        }

        $user->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'User deleted successfully.',
        ]);
    }

    /**
     * Fast direct 1-day suspension (bypasses Livewire to prevent request queuing).
     */
    public function suspendOneDay($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found.'], 404);
        }

        // Prevent suspending super admins
        if (strtolower(trim($user->email)) === 'admin@agrico.com' || in_array(strtolower(trim($user->role)), ['superadmin', 'super admin'])) {
            return response()->json(['success' => false, 'message' => 'Super admin cannot be suspended.'], 403);
        }

        $admin = auth()->user();
        $adminId = 'ADM-001';
        if ($admin) {
            if (!empty($admin->Admin_ID)) {
                $adminId = \App\Models\Admin::formatId($admin->Admin_ID);
            } elseif (!empty($admin->last_admin_id)) {
                $adminId = \App\Models\Admin::formatId($admin->last_admin_id);
            }
        }

        $user->status = 'Suspended';
        $user->suspend_reason = 'Quick 1-day suspension';
        $user->suspended_until = now()->addDay();
        $user->suspended_by_admin_id = $adminId;
        $user->save();

        if (strtolower(trim($user->role)) === 'student' || !empty($user->student_id)) {
            $user->notifyAdminOfSuspension(
                reason: 'Quick 1-day suspension',
                until: $user->suspended_until,
                suspendedBy: $user->suspended_by_admin_detail
            );
        }

        return response()->json([
            'success' => true,
            'status' => 'Suspended',
            'message' => 'User suspended for 1 day.'
        ]);
    }
}
