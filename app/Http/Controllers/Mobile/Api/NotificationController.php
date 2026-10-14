<?php

namespace App\Http\Controllers\Mobile\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Retrieve notifications for the authenticated user or specified user_id.
     */
    public function index(Request $request): JsonResponse
    {
        $userId = Auth::guard('sanctum')->id() 
            ?? Auth::id() 
            ?? $request->input('user_id')
            ?? session('user_id');

        if (!$userId) {
            $firstUser = User::first();
            $userId = $firstUser?->user_id;
        }

        $query = UserNotification::query();
        if ($userId) {
            $query->where('user_id', $userId);
        }

        $unreadCount = (clone $query)->where('is_read', false)->count();
        $notifications = $query->orderBy('created_at', 'desc')->limit(50)->get();

        return response()->json([
            'status'       => 'success',
            'unread_count' => $unreadCount,
            'data'         => $notifications,
        ]);
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead(Request $request, int|string $id): JsonResponse
    {
        $notification = UserNotification::find($id);
        if (!$notification) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Notification not found.',
            ], 404);
        }

        $notification->markAsRead();

        return response()->json([
            'status'  => 'success',
            'message' => 'Notification marked as read.',
            'data'    => $notification,
        ]);
    }

    /**
     * Mark all unread notifications as read for current user.
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        $userId = Auth::guard('sanctum')->id() 
            ?? Auth::id() 
            ?? $request->input('user_id')
            ?? session('user_id');

        if ($userId) {
            UserNotification::where('user_id', $userId)
                ->where('is_read', false)
                ->update([
                    'is_read' => true,
                    'read_at' => now(),
                ]);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'All notifications marked as read.',
        ]);
    }
}
