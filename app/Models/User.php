<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, HasFactory;

    protected $primaryKey = 'user_id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'password',
        'role',
        'student_id',
        'section',
        'student_record_status',
        'status',
        'last_admin_id',
        'scheduled_deletion_at',
        'suspend_reason',
        'suspended_until',
        'suspended_by_admin_id',
        'profile_photo_path',
        'is_online',
        'theme',
    ];

    protected static function booted(): void
    {
        static::creating(function (User $user) {
            if (empty($user->user_id) && empty($user->id)) {
                $role = strtolower(trim($user->role ?? 'member'));
                if (in_array($role, ['admin', 'superadmin', 'super admin'])) {
                    $adminIds = User::where('user_id', 'LIKE', 'ADM-%')->pluck('user_id');
                    $maxNum = 0;
                    foreach ($adminIds as $idStr) {
                        if (preg_match('/ADM-?(\d+)/i', (string)$idStr, $matches)) {
                            $num = (int)$matches[1];
                            if ($num > $maxNum) {
                                $maxNum = $num;
                            }
                        }
                    }
                    $user->user_id = 'ADM-' . str_pad($maxNum + 1, 3, '0', STR_PAD_LEFT);
                } else {
                    $userIds = User::where('user_id', 'LIKE', 'USR-%')->pluck('user_id');
                    $maxNum = 0;
                    foreach ($userIds as $idStr) {
                        if (preg_match('/USR-?(\d+)/i', (string)$idStr, $matches)) {
                            $num = (int)$matches[1];
                            if ($num > $maxNum) {
                                $maxNum = $num;
                            }
                        }
                    }
                    $user->user_id = 'USR-' . str_pad($maxNum + 1, 3, '0', STR_PAD_LEFT);
                }
            } elseif (empty($user->user_id) && !empty($user->id)) {
                $user->user_id = $user->id;
            }

            if (empty($user->last_admin_id)) {
                if (strtolower($user->role ?? '') !== 'student' && !request()->is('register*') && !request()->is('user/register*')) {
                    $adminUser = \Illuminate\Support\Facades\Auth::guard('admin')->user() ?? \Illuminate\Support\Facades\Auth::user();
                    if ($adminUser) {
                        $email = $adminUser->email ?? $adminUser->Email ?? '';
                        $adminRec = \App\Models\Admin::where('email', $email)->first();
                        $user->last_admin_id = $adminRec ? $adminRec->id : ($adminUser->Admin_ID ?? $adminUser->admin_id ?? null);
                    }
                }
            }

            if (strtolower($user->role ?? '') === 'student' && empty($user->student_record_status)) {
                $user->student_record_status = 'Pending';
            }
        });

        static::updating(function (User $user) {
            // For students not yet active in Student Records, do not auto-stamp last_admin_id
            if (strtolower($user->role ?? '') === 'student' && $user->student_record_status !== 'Active') {
                return;
            }

            $adminUser = \Illuminate\Support\Facades\Auth::guard('admin')->user() ?? \Illuminate\Support\Facades\Auth::user();
            if ($adminUser) {
                $email = $adminUser->email ?? $adminUser->Email ?? '';
                $adminRec = \App\Models\Admin::where('email', $email)->first();
                if ($adminRec) {
                    $user->last_admin_id = $adminRec->id;
                } elseif (!empty($adminUser->Admin_ID ?? $adminUser->admin_id ?? '')) {
                    $user->last_admin_id = $adminUser->Admin_ID ?? $adminUser->admin_id;
                }
            }
        });
    }

    public function getProfilePhotoUrlAttribute(): ?string
    {
        if ($this->profile_photo_path) {
            return asset('storage/' . $this->profile_photo_path);
        }
        return null;
    }

    protected $appends = [
        'profile_photo_url',
    ];

    protected $casts = [
        'scheduled_deletion_at' => 'datetime',
        'suspended_until' => 'datetime',
        'is_online' => 'boolean',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function soilScans()
    {
        return $this->hasMany(SoilScan::class, 'user_id', 'user_id');
    }

    public function locationMappings()
    {
        return $this->hasManyThrough(
            LocationMapping::class,
            SoilScan::class,
            'user_id',
            'soil_scan_id',
            'user_id',
            'soil_scan_id'
        );
    }

    public function pinMaps()
    {
        return $this->locationMappings();
    }

    public function initials(): string
    {
        return collect(explode(' ', $this->name))
            ->map(fn ($word) => strtoupper(substr($word, 0, 1)))
            ->join('');
    }

    public function getIdAttribute()
    {
        return $this->attributes['user_id'] ?? $this->attributes['User_ID'] ?? $this->attributes['id'] ?? null;
    }

    public function getFormattedIdAttribute(): string
    {
        $role = strtolower(trim($this->role ?? 'member'));

        // If the user is a student and has a student_id, use the student ID
        if ($role === 'student' && !empty($this->student_id)) {
            return $this->student_id;
        }

        $rawId = (string) ($this->attributes['user_id'] ?? $this->attributes['User_ID'] ?? $this->attributes['id'] ?? '');

        if (in_array($role, ['admin', 'superadmin', 'super admin', 'administrator'])) {
            if (preg_match('/(\d+)$/', $rawId, $matches)) {
                $num = (int) $matches[1];
            } else {
                $num = (int) preg_replace('/\D/', '', $rawId) ?: 1;
            }
            return 'ADM-' . str_pad($num, 3, '0', STR_PAD_LEFT);
        }

        if (str_starts_with($rawId, 'ADM-')) {
            $num = (int) substr($rawId, 4);
            $year = date('Y');
            $yearPrefix = substr($year, 0, 1) . substr($year, -2);
            return $yearPrefix . '-' . str_pad($num, 4, '0', STR_PAD_LEFT);
        }

        return $rawId;
    }

    public function getAdminIdAttribute(): ?string
    {
        if (!empty($this->attributes['admin_id'] ?? null)) {
            return $this->attributes['admin_id'];
        }

        if (!empty($this->last_admin_id)) {
            return $this->last_admin_id;
        }

        $adminRecord = \App\Models\Admin::where('email', $this->email)->first();
        if ($adminRecord?->admin_id) {
            return $adminRecord->admin_id;
        }

        return null;
    }

    public function getIsPendingDeletionAttribute(): bool
    {
        return !is_null($this->scheduled_deletion_at) && $this->scheduled_deletion_at->isFuture();
    }

    public function getDeletionCountdownAttribute(): string
    {
        if (!$this->scheduled_deletion_at || $this->scheduled_deletion_at->isPast()) {
            return '0d 0h 0m';
        }

        $now = now();
        $diff = $now->diff($this->scheduled_deletion_at);

        $parts = [];
        if ($diff->d > 0) $parts[] = $diff->d . 'd';
        if ($diff->h > 0 || $diff->d > 0) $parts[] = $diff->h . 'h';
        $parts[] = $diff->i . 'm';

        return implode(' ', $parts);
    }

    public function getIsSuspendedWithTimerAttribute(): bool
    {
        return strtolower($this->status) === 'suspended' && !is_null($this->suspended_until) && $this->suspended_until->isFuture();
    }

    public function getSuspensionCountdownAttribute(): string
    {
        if (!$this->suspended_until || $this->suspended_until->isPast()) {
            return '0d 0h 0m';
        }

        $now = now();
        $diff = $now->diff($this->suspended_until);

        $parts = [];
        if ($diff->d > 0) $parts[] = $diff->d . 'd';
        if ($diff->h > 0 || $diff->d > 0) $parts[] = $diff->h . 'h';
        $parts[] = $diff->i . 'm';

        return implode(' ', $parts);
    }

    public function getSuspendedByAdminDetailAttribute(): ?string
    {
        if (!$this->suspended_by_admin_id) {
            $firstAdmin = \App\Models\Admin::first();
            return $firstAdmin ? ($firstAdmin->full_name . ' (' . $firstAdmin->admin_id . ')') : 'ADM-001';
        }

        $admin = \App\Models\Admin::where('email', $this->suspended_by_admin_id)
            ->orWhere('admin_id', $this->suspended_by_admin_id)
            ->first();

        if ($admin) {
            return $admin->full_name . ' (' . $admin->admin_id . ')';
        }

        return $this->suspended_by_admin_id;
    }

    public function getAssignedAdminAttribute(): ?\App\Models\Admin
    {
        $adminId = $this->last_admin_id ?: $this->suspended_by_admin_id;
        if ($adminId) {
            $admin = \App\Models\Admin::where('admin_id', $adminId)
                ->orWhere('email', $adminId)
                ->first();
            if ($admin) {
                return $admin;
            }
        }
        return \App\Models\Admin::first();
    }

    public function notifyAdminOfSuspension(?string $reason = null, ?\DateTimeInterface $until = null, ?string $suspendedBy = null): void
    {
        $admin = $this->assigned_admin;
        $adminEmail = $admin?->email ?: config('mail.from.address', 'admin@agrico.com');

        $reason = $reason ?: $this->suspend_reason ?: 'Administrative policy violation';
        $until = $until ?: $this->suspended_until;
        $suspendedBy = $suspendedBy ?: $this->suspended_by_admin_detail ?: 'System Administrator';

        try {
            if ($adminEmail && filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) {
                \Illuminate\Support\Facades\Mail::to($adminEmail)->send(
                    new \App\Mail\StudentSuspendedMail(
                        student: $this,
                        admin: $admin,
                        reason: $reason,
                        until: $until ? \Carbon\Carbon::parse($until) : null,
                        suspendedBy: $suspendedBy
                    )
                );
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Failed to send student suspension notification email: ' . $e->getMessage());
        }
    }

    /**
     * Synchronize user role, user_id format, and Admin table record when role is updated.
     */
    public function syncRoleAndAdminId(string $newRole): void
    {
        $isNewRoleAdmin = in_array(strtolower(trim($newRole)), ['admin', 'superadmin']);
        $isCurrentAdminId = preg_match('/^ADM-?\d+/i', (string)$this->user_id);

        $oldUserId = $this->user_id;

        if ($isNewRoleAdmin) {
            $targetAdminId = $this->user_id;

            if (!$isCurrentAdminId) {
                // Find next available ADM-xxx ID across admins and users tables
                $adminIds = \App\Models\Admin::pluck('admin_id')
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
                $targetAdminId = 'ADM-' . str_pad($maxNum + 1, 3, '0', STR_PAD_LEFT);
            }

            if ($oldUserId !== $targetAdminId) {
                \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                \Illuminate\Support\Facades\DB::table('users')->where('user_id', $oldUserId)->update(['user_id' => $targetAdminId]);
                \Illuminate\Support\Facades\DB::table('soil_scans')->where('user_id', $oldUserId)->update(['user_id' => $targetAdminId]);
                \Illuminate\Support\Facades\DB::table('downloads')->where('user_id', $oldUserId)->update(['user_id' => $targetAdminId]);
                \Illuminate\Support\Facades\DB::table('faq_interactions')->where('user_id', $oldUserId)->update(['user_id' => $targetAdminId]);
                \Illuminate\Support\Facades\DB::table('sessions')->where('user_id', $oldUserId)->update(['user_id' => $targetAdminId]);
                \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                $this->user_id = $targetAdminId;
                $this->syncOriginalAttribute('user_id');
            }

            $this->role = $newRole;
            \Illuminate\Support\Facades\DB::table('users')->where('email', $this->email)->update(['role' => $newRole]);

            // Sync or Create in admins table
            $matchingAdmin = \App\Models\Admin::where('email', $this->email)
                ->orWhere('admin_id', $oldUserId)
                ->orWhere('admin_id', $targetAdminId)
                ->first();

            if ($matchingAdmin) {
                $matchingAdmin->update([
                    'admin_id' => $targetAdminId,
                    'full_name' => $this->name,
                    'email' => $this->email,
                    'password' => $this->password,
                    'profile_photo_path' => $this->profile_photo_path,
                ]);
            } else {
                \App\Models\Admin::create([
                    'admin_id' => $targetAdminId,
                    'full_name' => $this->name,
                    'email' => $this->email,
                    'password' => $this->password,
                    'profile_photo_path' => $this->profile_photo_path,
                ]);
            }
        } else {
            // Role set to non-admin (e.g. member, user)
            if ($isCurrentAdminId) {
                $count = User::where('user_id', 'NOT LIKE', 'ADM-%')->count() + 1;
                $year = date('Y');
                $yearPrefix = substr($year, 0, 1) . substr($year, -2);
                $newUserId = $yearPrefix . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

                \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                \Illuminate\Support\Facades\DB::table('users')->where('user_id', $oldUserId)->update(['user_id' => $newUserId]);
                \Illuminate\Support\Facades\DB::table('soil_scans')->where('user_id', $oldUserId)->update(['user_id' => $newUserId]);
                \Illuminate\Support\Facades\DB::table('downloads')->where('user_id', $oldUserId)->update(['user_id' => $newUserId]);
                \Illuminate\Support\Facades\DB::table('faq_interactions')->where('user_id', $oldUserId)->update(['user_id' => $newUserId]);
                \Illuminate\Support\Facades\DB::table('sessions')->where('user_id', $oldUserId)->update(['user_id' => $newUserId]);
                \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                $this->user_id = $newUserId;
            }

            $this->role = $newRole;

            // Remove admin record if present
            \App\Models\Admin::where('email', $this->email)
                ->orWhere('admin_id', $oldUserId)
                ->delete();
        }
    }

    /**
     * Check if user account is approved/active for full platform privileges.
     */
    public function isApproved(): bool
    {
        $role = strtolower(trim($this->role ?? 'user'));
        if (in_array($role, ['admin', 'superadmin', 'super admin', 'administrator'])) {
            return true;
        }

        $status = strtolower(trim($this->status ?? ''));
        return in_array($status, ['active', 'approved']);
    }

    /**
     * Check if user account is in a pending or unapproved status.
     */
    public function isPending(): bool
    {
        return !$this->isApproved();
    }

    /**
     * Get user requests associated with this user's email.
     */
    public function userRequests()
    {
        return $this->hasMany(\App\Models\UserRequest::class, 'user_email', 'email');
    }
}
