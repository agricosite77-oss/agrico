<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    protected $table = 'admins';

    protected $primaryKey = 'admin_id';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'admin_id',
        'full_name',
        'email',
        'password',
        'profile_photo_path',
        'remember_token',
        'theme',
    ];

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

    protected $hidden = [
        'password',
    ];

    public function getAuthPassword()
    {
        return $this->attributes['password'] ?? null;
    }

    public function getIdAttribute()
    {
        return $this->attributes['admin_id'] ?? $this->attributes['id'] ?? null;
    }

    public function getAdminIDAttribute()
    {
        return $this->attributes['admin_id'] ?? $this->attributes['id'] ?? null;
    }

    public function initials()
    {
        return collect(explode(' ', $this->full_name))
            ->map(fn ($n) => strtoupper(substr($n, 0, 1)))
            ->take(2)
            ->implode('');
    }

    public function getFormattedAdminIdAttribute()
    {
        return static::formatId($this->admin_id ?? $this->id);
    }

    public static function formatId($id)
    {
        if (!$id || $id === '—') return '—';
        if ($id instanceof \App\Models\Admin) {
            $id = $id->admin_id ?? $id->id ?? $id->email;
        }
        if (filter_var($id, FILTER_VALIDATE_EMAIL)) {
            $found = static::where('email', $id)->first();
            if ($found && ($found->admin_id ?? $found->id)) {
                return static::formatId($found->admin_id ?? $found->id);
            }
        }
        if (preg_match('/^(ADM|AD)-?\d+$/i', (string)$id)) {
            $num = (int) preg_replace('/\D/', '', (string)$id);
            return 'ADM-' . str_pad($num, 3, '0', STR_PAD_LEFT);
        }
        if (is_numeric($id)) {
            return 'ADM-' . str_pad((int)$id, 3, '0', STR_PAD_LEFT);
        }
        if (preg_match('/^\d{3}-\d{4}$/', (string)$id)) {
            return (string)$id;
        }
        return (string)$id;
    }

    public static function currentAdminId(): string
    {
        $user = \Illuminate\Support\Facades\Auth::guard('admin')->user() ?? \Illuminate\Support\Facades\Auth::user();
        if (!$user) {
            return 'ADM-001';
        }

        if ($user instanceof \App\Models\Admin) {
            return static::formatId($user->admin_id ?? $user->Admin_ID ?? $user->id);
        }

        if (!empty($user->email)) {
            $adminRecord = static::where('email', $user->email)->first();
            if ($adminRecord && !empty($adminRecord->admin_id ?? $adminRecord->id)) {
                return static::formatId($adminRecord->admin_id ?? $adminRecord->id);
            }
        }

        if (!empty($user->Admin_ID ?? $user->admin_id)) {
            return static::formatId($user->Admin_ID ?? $user->admin_id);
        }

        if (!empty($user->user_id)) {
            return static::formatId($user->user_id);
        }

        if (!empty($user->last_admin_id)) {
            return static::formatId($user->last_admin_id);
        }

        return static::formatId($user->id ?? 'ADM-001');
    }

    public static function currentAdminVariants(): array
    {
        $id = static::currentAdminId();
        return array_values(array_unique([
            $id,
            str_replace('-', '', $id),
            static::formatId($id),
        ]));
    }

    public static function scopeForCurrentAdmin($query, string $column = 'last_admin_id')
    {
        $myAdminId = static::currentAdminId();
        $variants = static::currentAdminVariants();

        if ($myAdminId === 'ADM-001') {
            return $query->where(function ($q) use ($column, $variants) {
                $q->whereIn($column, $variants)
                  ->orWhereNull($column)
                  ->orWhere($column, '');
            });
        }

        return $query->whereIn($column, $variants);
    }

    public function isSuperAdmin(): bool
    {
        $email = strtolower(trim((string) ($this->email ?? $this->Email ?? '')));

        if (in_array($email, ['agricosite77@gmail.com', 'admin@agrico.com'])) {
            return true;
        }

        $user = \App\Models\User::whereRaw('LOWER(email) = ?', [$email])->first();
        return $user ? $user->isSuperAdmin() : false;
    }
}

