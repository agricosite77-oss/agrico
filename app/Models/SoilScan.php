<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoilScan extends Model
{
    use HasFactory;

    protected $primaryKey = 'soil_scan_id';

    protected $fillable = [
        'soil_scan_id',
        'scan_note',
        'regions_data',
        'scan_image',
        'before_after_image',
        'thumbnail_image',
        'platform',
        'scan_date',
        'user_id',
        'soil_id',
        'verification_status',
        'moderated_by_admin_id',
    ];

    protected $casts = [
        'regions_data' => 'array',
        'scan_date'    => 'datetime',
    ];

    protected $appends = [
        'scan_image_url',
        'before_after_image_url',
        'thumbnail_image_url',
    ];

    protected static function booted(): void
    {
        // Enforce limit periodically
        static::created(function () {
            if (mt_rand(1, 10) === 1) {
                static::enforceLimit(300);
            }
        });
    }

    public static function enforceLimit(int $limit = 300): void
    {
        $total = static::count();
        if ($total > $limit) {
            $excess = $total - $limit;
            $pinnedScanIds = LocationMapping::whereNotNull('soil_scan_id')->pluck('soil_scan_id')->toArray();
            $oldestIds = static::whereNotIn('soil_scan_id', $pinnedScanIds)
                ->orderBy('soil_scan_id', 'asc')
                ->limit($excess)
                ->pluck('soil_scan_id')
                ->toArray();

            if (!empty($oldestIds)) {
                static::whereIn('soil_scan_id', $oldestIds)->delete();
            }
        }
    }

    public static function approveScan(int|string $scanId, ?string $adminId = null): ?self
    {
        $scan = static::with(['soil', 'location'])->find($scanId);
        if (!$scan) {
            return null;
        }

        $adminId = $adminId ?: \App\Models\Admin::currentAdminId();

        $scan->verification_status = 'Verified';
        $scan->moderated_by_admin_id = $adminId;
        $scan->save();

        if ($scan->soil) {
            $scan->soil->status = 'Active';
            $scan->soil->last_admin_id = $adminId;
            $scan->soil->save();
        }

        $locations = \App\Models\LocationMapping::where('soil_scan_id', $scan->soil_scan_id)->get();
        if ($locations->isEmpty() && $scan->soil_id) {
            $locations = \App\Models\LocationMapping::where('soil_id', $scan->soil_id)->get();
        }

        foreach ($locations as $loc) {
            $loc->status = 'Active';
            $loc->save();

            \App\Models\UserRequest::where('status', 'Pending')
                ->where(function ($q) use ($loc) {
                    $q->where('description', 'like', "%#{$loc->location_mapping_id}%")
                      ->orWhere('description', 'like', "%({$loc->latitude}, {$loc->longitude})%");
                })
                ->update([
                    'status'   => 'Approved',
                    'admin_id' => $adminId,
                ]);
        }

        // Trigger notifications across Mobile App, Website, and Email
        \App\Services\NotificationService::notifyScanModerated($scan, 'Verified', $adminId);

        return $scan;
    }

    public static function rejectScan(int|string $scanId, ?string $adminId = null): ?self
    {
        $scan = static::with(['soil', 'location'])->find($scanId);
        if (!$scan) {
            return null;
        }

        $adminId = $adminId ?: \App\Models\Admin::currentAdminId();

        $scan->verification_status = 'Rejected';
        $scan->moderated_by_admin_id = $adminId;
        $scan->save();

        $locations = \App\Models\LocationMapping::where('soil_scan_id', $scan->soil_scan_id)->get();
        if ($locations->isEmpty() && $scan->soil_id) {
            $locations = \App\Models\LocationMapping::where('soil_id', $scan->soil_id)->get();
        }

        foreach ($locations as $loc) {
            $loc->status = 'Inactive';
            $loc->save();

            \App\Models\UserRequest::where('status', 'Pending')
                ->where(function ($q) use ($loc) {
                    $q->where('description', 'like', "%#{$loc->location_mapping_id}%")
                      ->orWhere('description', 'like', "%({$loc->latitude}, {$loc->longitude})%");
                })
                ->update([
                    'status'   => 'Disapproved',
                    'admin_id' => $adminId,
                ]);
        }

        // Trigger notifications across Mobile App, Website, and Email
        \App\Services\NotificationService::notifyScanModerated($scan, 'Rejected', $adminId);

        return $scan;
    }

    public function getIdAttribute()
    {
        return $this->attributes['soil_scan_id'] ?? $this->attributes['id'] ?? null;
    }

    public function user()
    {
        return $this->belongsTo(
            User::class,
            'user_id',
            'user_id'
        );
    }

    public function soil()
    {
        return $this->belongsTo(
            Soil::class,
            'soil_id',
            'soil_id'
        );
    }

    public function getMunsellColorAttribute(): ?MunsellColor
    {
        return $this->soil?->dynamic_munsell_color 
            ?? MunsellColor::where('full_code', $this->soil?->full_code)->first();
    }

    public function getEffectiveDescriptionAttribute(): string
    {
        return $this->soil?->effective_description 
            ?? ($this->munsell_color?->description 
            ?? 'Sample processed successfully using AgriCo Munsell Determination Algorithm.');
    }

    public function getDynamicDescriptionAttribute(): string
    {
        return $this->effective_description;
    }

    public function getEffectiveSoilNameAttribute(): string
    {
        return $this->soil?->effective_soil_name 
            ?? ($this->munsell_color?->color_name 
            ?? 'Determined Soil Specimen');
    }

    public function getDynamicSoilNameAttribute(): string
    {
        return $this->effective_soil_name;
    }

    public function getEffectiveHexColorAttribute(): string
    {
        return $this->soil?->effective_hex_color ?? '#8F694E';
    }

    public function getEffectiveOrganicMatterAttribute(): string
    {
        return $this->soil?->effective_organic_matter ?? 'Moderate (2.2% – 3.7%)';
    }

    public function getEffectiveSoilTypeAttribute(): string
    {
        return $this->soil?->effective_soil_type ?? 'Well-Drained Clay / Latosol';
    }

    public function getColorSpaceBadgesAttribute(): array
    {
        return $this->soil?->color_space_badges ?? [];
    }

    public function getHasUserScanNoteAttribute(): bool
    {
        $hasLocation = !empty($this->location_id) || ($this->relationLoaded('location') && !empty($this->location));
        if (!$hasLocation && !empty($this->soil_scan_id)) {
            $hasLocation = \App\Models\LocationMapping::where('soil_scan_id', $this->soil_scan_id)->exists();
        }
        if (!$hasLocation) {
            return false;
        }

        $note = trim($this->scan_note ?? '');
        return !empty($note) 
            && !str_starts_with($note, 'Determined Soil:') 
            && $note !== '—' 
            && $note !== '-';
    }

    public function getUserScanNoteAttribute(): string
    {
        return $this->has_user_scan_note ? trim($this->scan_note) : '-';
    }

    public function getRegionsAttribute(): array
    {
        if (!empty($this->regions_data)) {
            return is_array($this->regions_data) ? $this->regions_data : (json_decode($this->regions_data, true) ?: []);
        }
        return [];
    }

    public function getScanImageUrlAttribute(): ?string
    {
        return \App\Helpers\SoilImageHelper::scanImageUrl($this, false);
    }

    public function getBeforeAfterImageUrlAttribute(): ?string
    {
        return \App\Helpers\SoilImageHelper::beforeAfterImageUrl($this);
    }

    public function getThumbnailImageUrlAttribute(): ?string
    {
        return \App\Helpers\SoilImageHelper::scanImageUrl($this, true);
    }

    public function location()
    {
        return $this->hasOne(
            LocationMapping::class,
            'soil_scan_id',
            'soil_scan_id'
        );
    }
}
