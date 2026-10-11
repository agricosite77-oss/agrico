<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    protected $primaryKey = 'application_id';

    protected $fillable = [
        'application_id',
        'application_name',
        'version',
        'file_path',
        'filename',
        'file_size',
        'release_notes',
        'status',
        'is_maintenance',
        'uploaded_by',
    ];

    protected $casts = [
        'is_maintenance' => 'boolean',
    ];

    public function getIdAttribute()
    {
        return $this->attributes['application_id'] ?? $this->attributes['id'] ?? null;
    }

    public static function isMaintenanceMode(): bool
    {
        $cached = \Illuminate\Support\Facades\Cache::get('system_maintenance_mode');
        if ($cached !== null) {
            return (bool) $cached;
        }

        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('applications') && \Illuminate\Support\Facades\Schema::hasColumn('applications', 'is_maintenance')) {
                $active = static::where('status', 'ACTIVE')->first();
                if ($active) {
                    $isMaint = (bool) ($active->is_maintenance ?? false);
                    \Illuminate\Support\Facades\Cache::put('system_maintenance_mode', $isMaint, 60);
                    return $isMaint;
                }
            }
        } catch (\Throwable $e) {}

        return false;
    }

    public static function setMaintenanceMode(bool $enabled): void
    {
        \Illuminate\Support\Facades\Cache::forever('system_maintenance_mode', $enabled);

        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('applications') && \Illuminate\Support\Facades\Schema::hasColumn('applications', 'is_maintenance')) {
                static::where('status', 'ACTIVE')->update(['is_maintenance' => $enabled]);
            }
        } catch (\Throwable $e) {}
    }

    public function downloads()
    {
        return $this->hasMany(
            Download::class,
            'application_id',
            'application_id'
        );
    }
}
