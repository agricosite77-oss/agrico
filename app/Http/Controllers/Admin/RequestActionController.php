<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\LocationMapping;
use App\Models\User;
use App\Models\UserRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RequestActionController extends Controller
{
    /**
     * Resolve the authenticated admin's formatted ID.
     */
    protected function getAdminId(): string
    {
        $user = auth()->user();
        if (!$user) return 'ADM-001';

        if (!empty($user->Admin_ID)) {
            return Admin::formatId($user->Admin_ID);
        }
        if (!empty($user->last_admin_id)) {
            return Admin::formatId($user->last_admin_id);
        }

        $email = $user->Email ?? $user->email ?? '';
        if ($email) {
            $admin = Admin::where('Email', $email)->first();
            if ($admin?->Admin_ID) {
                return Admin::formatId($admin->Admin_ID);
            }
        }

        return 'ADM-001';
    }

    /**
     * Approve a user request.
     */
    public function approve(Request $request, int $id): JsonResponse
    {
        $req = UserRequest::find($id);

        if (!$req) {
            return response()->json(['success' => false, 'message' => 'Request not found.'], 404);
        }

        if ($req->status !== 'Pending') {
            return response()->json(['success' => false, 'message' => 'Already resolved.'], 422);
        }

        $adminId = $this->getAdminId();

        $req->update([
            'status'   => 'Approved',
            'admin_id' => $adminId,
        ]);

        $type = strtolower($req->type ?? '');

        if (str_contains($type, 'coordinate') || str_contains($type, 'location') || str_contains($type, 'pin')) {
            if (preg_match('/Location Pin #(\d+)/i', $req->description, $matches)) {
                LocationMapping::where('location_mapping_id', $matches[1])->update(['status' => 'Active']);
            } elseif (preg_match('/Coords:\s*\(([^,]+),\s*([^)]+)\)/i', $req->description, $c)) {
                LocationMapping::where('latitude', (float)$c[1])
                    ->where('longitude', (float)$c[2])
                    ->where('status', 'Pending')
                    ->update(['status' => 'Active']);
            }
        } elseif (str_contains($type, 'reactivat')) {
            $user = User::where('email', $req->user_email)->first();
            if ($user) {
                $user->update([
                    'status'          => 'Active',
                    'suspend_reason'  => null,
                    'suspended_until' => null,
                    'last_admin_id'   => $adminId,
                ]);
            }
        } else {
            $user = User::where('email', $req->user_email)->first();
            if ($user) {
                $updateData = ['status' => 'Active'];
                // Only non-student accounts adopt approving admin as last_admin_id.
                // Student accounts must be explicitly added to an admin's Student Records roster.
                if (strtolower($user->role ?? '') !== 'student') {
                    $updateData['last_admin_id'] = $adminId;
                }
                $user->update($updateData);
            }
        }

        return response()->json([
            'success' => true,
            'status'  => 'Approved',
            'message' => 'Request ' . $req->request_code . ' approved.',
        ]);
    }

    /**
     * Disapprove a user request.
     */
    public function disapprove(Request $request, int $id): JsonResponse
    {
        $req = UserRequest::find($id);

        if (!$req) {
            return response()->json(['success' => false, 'message' => 'Request not found.'], 404);
        }

        if ($req->status !== 'Pending') {
            return response()->json(['success' => false, 'message' => 'Already resolved.'], 422);
        }

        $adminId = $this->getAdminId();

        $req->update([
            'status'   => 'Disapproved',
            'admin_id' => $adminId,
        ]);

        $type = strtolower($req->type ?? '');

        if (str_contains($type, 'coordinate') || str_contains($type, 'location') || str_contains($type, 'pin')) {
            if (preg_match('/Location Pin #(\d+)/i', $req->description, $matches)) {
                LocationMapping::where('location_mapping_id', $matches[1])->update(['status' => 'Inactive']);
            } elseif (preg_match('/Coords:\s*\(([^,]+),\s*([^)]+)\)/i', $req->description, $c)) {
                LocationMapping::where('latitude', (float)$c[1])
                    ->where('longitude', (float)$c[2])
                    ->where('status', 'Pending')
                    ->update(['status' => 'Inactive']);
            }
        } elseif (!str_contains($type, 'reactivat')) {
            // Delete pending user account on disapproval
            $user = User::where('email', $req->user_email)->first();
            if ($user) {
                $user->delete();
            }
        }

        return response()->json([
            'success' => true,
            'status'  => 'Disapproved',
            'message' => 'Request ' . $req->request_code . ' disapproved.',
        ]);
    }
}
