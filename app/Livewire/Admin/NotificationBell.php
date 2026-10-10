<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\UserRequest;
use Livewire\Attributes\On;
use Livewire\Component;

class NotificationBell extends Component
{
    #[On('student-suspended')]
    #[On('student-unsuspended')]
    #[On('user-suspended')]
    #[On('user-unsuspended')]
    #[On('request-updated')]
    #[On('refresh-notifications')]
    public function handleRefresh(): void
    {
        \Illuminate\Support\Facades\Cache::forget('sidebar_pending_requests_count');
    }

    public function render()
    {
        // Auto-unsuspend check throttled to once every 30 seconds to avoid heavy DB roundtrips
        if (!\Illuminate\Support\Facades\Cache::has('auto_unsuspend_last_checked')) {
            \Illuminate\Support\Facades\Cache::put('auto_unsuspend_last_checked', true, 30);
            
            $hasExpired = User::where('status', 'Suspended')
                ->whereNotNull('suspended_until')
                ->where('suspended_until', '<=', now())
                ->exists();

            if ($hasExpired) {
                User::where(function ($q) {
                    $q->whereRaw('LOWER(role) = ?', ['student'])
                      ->orWhereNotNull('student_id');
                })
                ->where('status', 'Suspended')
                ->whereNotNull('suspended_until')
                ->where('suspended_until', '<=', now())
                ->update([
                    'status' => 'Active',
                    'suspended_until' => null,
                    'suspend_reason' => null,
                    'suspended_by_admin_id' => null,
                ]);
            }
        }

        $headerPendingCount = \Illuminate\Support\Facades\Cache::remember('sidebar_pending_requests_count', 20, function () {
            return UserRequest::where('status', 'Pending')->count();
        });

        $recentPendingRequests = $headerPendingCount > 0
            ? UserRequest::where('status', 'Pending')->latest('submitted_at')->take(4)->get()
            : collect();

        $suspendedQuery = User::where(function ($q) {
            $q->whereRaw('LOWER(role) = ?', ['student'])
              ->orWhereNotNull('student_id');
        })
        ->where(function ($q) {
            $q->whereRaw('LOWER(status) = ?', ['suspended'])
              ->orWhere(function ($sub) {
                  $sub->whereNotNull('suspended_until')->where('suspended_until', '>', now());
              });
        });

        // Scope to the logged-in admin's students only
        \App\Models\Admin::scopeForCurrentAdmin($suspendedQuery);

        $recentSuspendedStudents = (clone $suspendedQuery)
            ->latest('updated_at')
            ->take(4)
            ->get();

        $suspendedStudentsCount = (clone $suspendedQuery)->count();

        $totalNotifCount = $headerPendingCount + $suspendedStudentsCount;

        // Dispatch browser window event to keep sidebar badges synced in real time
        $this->dispatch('sidebar-counts-updated', 
            suspendedCount: $suspendedStudentsCount,
            pendingCount: $headerPendingCount
        );

        return view('admin.livewire.notification-bell', [
            'headerPendingCount' => $headerPendingCount,
            'recentPendingRequests' => $recentPendingRequests,
            'recentSuspendedStudents' => $recentSuspendedStudents,
            'suspendedStudentsCount' => $suspendedStudentsCount,
            'totalNotifCount' => $totalNotifCount,
        ]);
    }
}
