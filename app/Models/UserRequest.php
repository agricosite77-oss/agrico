<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRequest extends Model
{
    use HasFactory;

    protected $table = 'user_requests';
    protected $primaryKey = 'user_request_id';

    protected $fillable = [
        'user_request_id',
        'request_code',
        'user_name',
        'user_email',
        'type',
        'description',
        'status',
        'admin_id',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    public function getIdAttribute()
    {
        return $this->attributes['user_request_id'] ?? $this->attributes['id'] ?? null;
    }

    /**
     * Approve a user request and execute relevant domain updates.
     *
     * @param int|string $id
     * @param string|null $adminId
     * @return array{success: bool, message: string, status_code: int, status?: string, request?: UserRequest}
     */
    public static function approveRequest(int|string $id, ?string $adminId = null): array
    {
        $req = static::find($id);

        if (!$req) {
            return [
                'success'     => false,
                'message'     => 'Request not found.',
                'status_code' => 404,
            ];
        }

        if ($req->status !== 'Pending') {
            return [
                'success'     => false,
                'message'     => 'Request has already been resolved.',
                'status_code' => 422,
            ];
        }

        $adminId = $adminId ?: Admin::currentAdminId();

        $req->update([
            'status'   => 'Approved',
            'admin_id' => $adminId,
        ]);

        $type = strtolower((string) ($req->type ?? ''));
        $desc = (string) ($req->description ?? '');

        if (str_contains($type, 'coordinate') || str_contains($type, 'location') || str_contains($type, 'pin') || str_contains($type, 'soil')) {
            if (preg_match('/Location Pin #(\d+)/i', $desc, $matches)) {
                LocationMapping::where('location_mapping_id', $matches[1])->update(['status' => 'Active']);
            } elseif (preg_match('/Coords:\s*\(([^,]+),\s*([^)]+)\)/i', $desc, $c)) {
                LocationMapping::where('latitude', (float) trim($c[1]))
                    ->where('longitude', (float) trim($c[2]))
                    ->where('status', 'Pending')
                    ->update(['status' => 'Active']);
            }
        } elseif (str_contains($type, 'delet')) {
            $user = User::whereRaw('LOWER(email) = ?', [strtolower(trim((string) $req->user_email))])->first();
            if ($user) {
                // Never delete last superadmin
                if (in_array(strtolower($user->email), ['agricosite77@gmail.com', 'admin@agrico.com']) || $user->user_id === 'ADM-001') {
                    $req->update(['status' => 'Pending']);
                    return [
                        'success'     => false,
                        'message'     => 'Primary Super Admin account cannot be deleted.',
                        'status_code' => 403,
                    ];
                }

                // 1. Send final approval deletion email before wiping
                \App\Services\AgriCoMailService::send(
                    $user->email,
                    'Your AgriCo account has been permanently deleted',
                    'emails.account-deletion-approved',
                    [
                        'requestCode' => $req->request_code ?: ('REQ-' . str_pad((string)$req->id, 3, '0', STR_PAD_LEFT)),
                        'userEmail'   => $user->email,
                        'userName'    => $user->name,
                        'deletedAt'   => now()->format('M d, Y h:i A'),
                    ],
                    new \App\Mail\AccountDeletionApprovedMail(
                        $req->request_code ?: ('REQ-' . str_pad((string)$req->id, 3, '0', STR_PAD_LEFT)),
                        $user->email,
                        $user->name
                    )
                );

                // 2. Anonymize user's soil scans (decouples all public map telemetry from user identity)
                \App\Models\SoilScan::where('user_id', $user->user_id)->update(['user_id' => null]);

                // 3. Delete all personal user notifications
                \App\Models\UserNotification::where('user_id', $user->user_id)->delete();

                // 4. Revoke all mobile Sanctum tokens
                if (method_exists($user, 'tokens')) {
                    $user->tokens()->delete();
                }

                // 5. Delete profile photo from disk if stored
                if ($user->profile_photo_path) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($user->profile_photo_path);
                }

                // 6. Delete any other pending requests for this user to protect privacy
                \App\Models\UserRequest::where('user_email', $user->email)
                    ->where('user_request_id', '!=', $req->user_request_id)
                    ->delete();

                // 7. Expunge user record completely from database
                $user->delete();

                // Also delete matching non-superadmin Admin record if exists
                \App\Models\Admin::where('email', $req->user_email)->whereNotIn('email', ['agricosite77@gmail.com', 'admin@agrico.com'])->delete();

                \Illuminate\Support\Facades\Log::info("Admin {$adminId} approved account deletion for {$req->user_email} (Request: {$req->request_code}). All personal user information expunged.");
            }
        } elseif (str_contains($type, 'reactivat')) {
            $user = User::whereRaw('LOWER(email) = ?', [strtolower(trim((string) $req->user_email))])->first();
            if ($user) {
                $user->update([
                    'status'          => 'Active',
                    'suspend_reason'  => null,
                    'suspended_until' => null,
                    'last_admin_id'   => $adminId,
                ]);
                \App\Services\NotificationService::notifyAccountApproved($user, $adminId);
            }
        } else {
            $user = User::whereRaw('LOWER(email) = ?', [strtolower(trim((string) $req->user_email))])->first();
            if ($user) {
                $updateData = ['status' => 'Active'];
                if (strtolower($user->role ?? '') !== 'student') {
                    $updateData['last_admin_id'] = $adminId;
                }
                $user->update($updateData);
                \App\Services\NotificationService::notifyAccountApproved($user, $adminId);
            }
        }

        return [
            'success'     => true,
            'status'      => 'Approved',
            'message'     => 'Request ' . ($req->request_code ?: ('REQ-' . str_pad((string)$req->id, 3, '0', STR_PAD_LEFT))) . ' approved.',
            'status_code' => 200,
            'request'     => $req,
        ];
    }

    /**
     * Disapprove a user request and execute relevant domain updates.
     *
     * @param int|string $id
     * @param string|null $adminId
     * @return array{success: bool, message: string, status_code: int, status?: string, request?: UserRequest}
     */
    public static function disapproveRequest(int|string $id, ?string $adminId = null): array
    {
        $req = static::find($id);

        if (!$req) {
            return [
                'success'     => false,
                'message'     => 'Request not found.',
                'status_code' => 404,
            ];
        }

        if ($req->status !== 'Pending') {
            return [
                'success'     => false,
                'message'     => 'Request has already been resolved.',
                'status_code' => 422,
            ];
        }

        $adminId = $adminId ?: Admin::currentAdminId();

        $req->update([
            'status'   => 'Disapproved',
            'admin_id' => $adminId,
        ]);

        $type = strtolower((string) ($req->type ?? ''));
        $desc = (string) ($req->description ?? '');

        if (str_contains($type, 'coordinate') || str_contains($type, 'location') || str_contains($type, 'pin') || str_contains($type, 'soil')) {
            if (preg_match('/Location Pin #(\d+)/i', $desc, $matches)) {
                LocationMapping::where('location_mapping_id', $matches[1])->update(['status' => 'Inactive']);
            } elseif (preg_match('/Coords:\s*\(([^,]+),\s*([^)]+)\)/i', $desc, $c)) {
                LocationMapping::where('latitude', (float) trim($c[1]))
                    ->where('longitude', (float) trim($c[2]))
                    ->where('status', 'Pending')
                    ->update(['status' => 'Inactive']);
            }
        } elseif (str_contains($type, 'delet')) {
            $user = User::whereRaw('LOWER(email) = ?', [strtolower(trim((string) $req->user_email))])->first();
            $userName = $user?->name ?? $req->user_name ?? 'AgriCo User';
            $userEmail = $user?->email ?? $req->user_email;

            // Send rejection email to user with admin reason
            $adminReason = request('admin_notes') ?? request('reason') ?? null;
            \App\Services\AgriCoMailService::send(
                $userEmail,
                "Update on your AgriCo account deletion request (" . ($req->request_code ?: ('REQ-' . str_pad((string)$req->id, 3, '0', STR_PAD_LEFT))) . ")",
                'emails.account-deletion-rejected',
                [
                    'requestCode' => $req->request_code ?: ('REQ-' . str_pad((string)$req->id, 3, '0', STR_PAD_LEFT)),
                    'userEmail'   => $userEmail,
                    'userName'    => $userName,
                    'adminReason' => $adminReason,
                    'reviewedAt'  => now()->format('M d, Y h:i A'),
                ],
                new \App\Mail\AccountDeletionRejectedMail(
                    $req->request_code ?: ('REQ-' . str_pad((string)$req->id, 3, '0', STR_PAD_LEFT)),
                    $userEmail,
                    $userName,
                    $adminReason
                )
            );

            \Illuminate\Support\Facades\Log::info("Admin {$adminId} disapproved account deletion for {$userEmail} (Request: {$req->request_code})");
        } elseif (str_contains($type, 'account') || str_contains($type, 'register') || str_contains($type, 'signup')) {
            // Only unapproved new account registrations are removed upon disapproval
            $user = User::whereRaw('LOWER(email) = ?', [strtolower(trim((string) $req->user_email))])->first();
            $applicantName = $user?->name ?? $req->user_name ?? 'AgriCo Applicant';
            $applicantEmail = $user?->email ?? $req->user_email;

            \App\Services\NotificationService::notifyAccountDisapproved(
                userOrEmail: $applicantEmail,
                userName: $applicantName,
                adminId: $adminId
            );

            if ($user && $user->status === 'Pending') {
                $user->delete();
            }
        }

        return [
            'success'     => true,
            'status'      => 'Disapproved',
            'message'     => 'Request ' . ($req->request_code ?: ('REQ-' . str_pad((string)$req->id, 3, '0', STR_PAD_LEFT))) . ' disapproved.',
            'status_code' => 200,
            'request'     => $req,
        ];
    }
}
