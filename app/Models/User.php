<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property string $user_id
 * @property string $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $role
 * @property string|null $student_id
 * @property string|null $section
 * @property string|null $student_record_status
 * @property string $status
 * @property string|null $last_admin_id
 * @property string|null $profile_photo_path
 * @property string|null $profile_photo_url
 * @property string|null $suspend_reason
 * @property \Illuminate\Support\Carbon|null $suspended_until
 * @property string|null $suspended_by_admin_id
 * @property \Illuminate\Support\Carbon|null $scheduled_deletion_at
 * @property bool $is_online
 * @property string|null $theme
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
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
                $role = strtolower(trim($user->role ?? 'user'));
                if ($role === 'member') {
                    $role = 'user';
                    $user->role = 'user';
                }
                if (in_array($role, ['admin', 'superadmin', 'super admin'])) {
                    $isSuper = in_array($role, ['superadmin', 'super admin'])
                        || in_array(strtolower(trim($user->email ?? '')), ['admin@agrico.com', 'agricosite77@gmail.com']);
                    $user->user_id = $isSuper ? 'ADM-001' : static::getNextAdminId();
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
            if (str_starts_with($this->profile_photo_path, 'http://') || str_starts_with($this->profile_photo_path, 'https://')) {
                return $this->profile_photo_path;
            }
            return asset('storage/' . ltrim($this->profile_photo_path, '/'));
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

    public static function getNextAdminId(): string
    {
        $adminIds = \App\Models\Admin::pluck('admin_id')
            ->concat(static::where('user_id', 'LIKE', 'ADM-%')->pluck('user_id'));

        $maxNum = 1; // ADM-001 is reserved for Super Admin
        foreach ($adminIds as $idStr) {
            if (preg_match('/ADM-?(\d+)/i', (string) $idStr, $matches)) {
                $num = (int) $matches[1];
                if ($num > $maxNum) {
                    $maxNum = $num;
                }
            }
        }

        return 'ADM-' . str_pad((string) ($maxNum + 1), 3, '0', STR_PAD_LEFT);
    }

    public function getFormattedIdAttribute(): string
    {
        $role = strtolower(trim($this->role ?? 'user'));

        // If the user is a student and has a student_id, use the student ID
        if ($role === 'student' && !empty($this->student_id)) {
            return $this->student_id;
        }

        $rawId = (string) ($this->attributes['user_id'] ?? $this->attributes['User_ID'] ?? $this->attributes['id'] ?? '');

        if ($this->isSuperAdmin()) {
            return 'ADM-001';
        }

        if (in_array($role, ['admin', 'superadmin', 'super admin', 'administrator'])) {
            // 1. Check if an official linked record in admins table exists with a non-001 ID
            $adminRecord = \App\Models\Admin::where('email', $this->email)->first();
            if ($adminRecord && !empty($adminRecord->admin_id) && $adminRecord->admin_id !== 'ADM-001') {
                return \App\Models\Admin::formatId($adminRecord->admin_id);
            }

            // 2. Check if user_id starts with ADM- and is NOT ADM-001
            if (preg_match('/^ADM-?(\d+)$/i', $rawId, $matches) && (int)$matches[1] > 1) {
                return 'ADM-' . str_pad((int)$matches[1], 3, '0', STR_PAD_LEFT);
            }

            // 3. If there is a colliding ADM-001 on a non-superadmin, auto-repair to next available ID
            if ($adminRecord && $adminRecord->admin_id === 'ADM-001') {
                $newId = static::getNextAdminId();
                \Illuminate\Support\Facades\DB::table('admins')
                    ->where('email', $this->email)
                    ->update(['admin_id' => $newId]);
                return $newId;
            }

            return static::getNextAdminId();
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

    public function isSuperAdmin(): bool
    {
        $email = strtolower(trim((string) ($this->email ?? '')));
        $role = strtolower(trim((string) ($this->role ?? '')));

        if (in_array($role, ['superadmin', 'super admin'])) {
            return true;
        }

        return in_array($email, ['agricosite77@gmail.com', 'admin@agrico.com']);
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
     * Synchronize user role and Admin table record when role is updated.
     */
    public function syncRoleAndAdminId(string $newRole): void
    {
        $normalizedRole = strtolower(trim($newRole));
        if ($normalizedRole === 'member') {
            $normalizedRole = 'user';
        }

        $this->role = $normalizedRole;
        $this->save();

        $isNewRoleAdmin = in_array($normalizedRole, ['admin', 'superadmin', 'super admin', 'administrator']);

        if ($isNewRoleAdmin) {
            // Sync or Create in admins table
            $matchingAdmin = \App\Models\Admin::where('email', $this->email)
                ->orWhere('admin_id', $this->user_id)
                ->first();

            $isSuperAdmin = $this->isSuperAdmin();

            if ($matchingAdmin) {
                // If this is a regular admin but incorrectly has ADM-001, reassign next unique ID
                if (!$isSuperAdmin && ($matchingAdmin->admin_id === 'ADM-001' || empty($matchingAdmin->admin_id))) {
                    $matchingAdmin->admin_id = static::getNextAdminId();
                }

                $matchingAdmin->update([
                    'admin_id' => $matchingAdmin->admin_id,
                    'full_name' => $this->name,
                    'email' => $this->email,
                    'password' => $this->password,
                    'profile_photo_path' => $this->profile_photo_path,
                ]);
            } else {
                $newAdminId = $isSuperAdmin ? 'ADM-001' : static::getNextAdminId();

                \App\Models\Admin::create([
                    'admin_id' => $newAdminId,
                    'full_name' => $this->name,
                    'email' => $this->email,
                    'password' => $this->password,
                    'profile_photo_path' => $this->profile_photo_path,
                ]);
            }
        } else {
            // Role set to non-admin (e.g. user, student)
            // Remove admin record if present, unless it's the protected super admin
            if (strtolower(trim($this->email)) !== 'admin@agrico.com' && strtolower(trim($this->email)) !== 'agricosite77@gmail.com') {
                \App\Models\Admin::where('email', $this->email)
                    ->orWhere('admin_id', $this->user_id)
                    ->delete();
            }
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
