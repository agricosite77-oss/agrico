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
        'platform',
        'scan_date',
        'user_id',
        'soil_id',
    ];

    protected $casts = [
        'regions_data' => 'array',
        'scan_date'    => 'datetime',
    ];

    protected $appends = [
        'scan_image_url',
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
            $oldestIds = static::orderBy('soil_scan_id', 'asc')
                ->limit($excess)
                ->pluck('soil_scan_id')
                ->toArray();

            if (!empty($oldestIds)) {
                static::whereIn('soil_scan_id', $oldestIds)->delete();
            }
        }
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
        $note = trim($this->scan_note ?? '');
        return !empty($note) 
            && !str_starts_with($note, 'Determined Soil:') 
            && $note !== '—' 
            && $note !== '-';
    }

    public function getUserScanNoteAttribute(): string
    {
        return $this->has_user_scan_note ? trim($this->scan_note) : '—';
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
        $raw = $this->scan_image;
        $isBrokenSupabase = !empty($raw) && (str_contains($raw, 'supabase.co') || str_contains($raw, 'agrico-scans'));

        if (!$isBrokenSupabase && !empty($raw)) {
            if (str_starts_with($raw, 'data:')) {
                return $raw;
            }
            if (str_starts_with($raw, 'http://') || str_starts_with($raw, 'https://')) {
                if (preg_match('#^https?://(?:localhost|127\.0\.0\.1|10\.0\.2\.2)(?::\d+)?/(.*)$#i', $raw, $matches)) {
                    $clean = ltrim($matches[1], '/');
                    return asset($clean);
                }
                return $raw;
            }
            $clean = ltrim($raw, '/');
            if (!str_starts_with($clean, 'storage/')) {
                $clean = 'storage/' . $clean;
            }
            return asset($clean);
        }

        // Handle broken Supabase URLs or missing images:
        // 1. Resolve session normalized scan image (sibling dominant scan)
        $lookupText = ($raw ?? '') . ' ' . ($this->scan_note ?? '');
        if (preg_match('/(?:scan|region)_([a-f0-9\-]{10,})/i', $lookupText, $m)) {
            $uuid = $m[1];
            $sibling = self::where('scan_image', 'LIKE', "%{$uuid}%")
                ->where('scan_image', 'NOT LIKE', '%supabase.co%')
                ->where('scan_image', 'NOT LIKE', '%agrico-scans%')
                ->where('scan_image', 'NOT LIKE', '%region_%')
                ->first();
            if ($sibling && $sibling->scan_image) {
                return $sibling->scan_image_url;
            }
            return asset("storage/scans/scan_{$uuid}_normalized.jpg");
        }

        // 2. Fallback to embedded region_thumbnail in regions_data
        $regs = $this->regions_data ?? [];
        if (!empty($regs[0]['region_thumbnail'])) {
            $thumb = $regs[0]['region_thumbnail'];
            return str_starts_with($thumb, 'data:') ? $thumb : ('data:image/png;base64,' . $thumb);
        }

        return null;
    }

    public function getBeforeAfterImageUrlAttribute(): ?string
    {
        $rawImg = $this->scan_image;
        if (!empty($rawImg) && preg_match('/(?:scan|region)_([a-f0-9\-]{10,})/i', $rawImg, $m)) {
            $scanUuid = $m[1];
            $rel = "storage/scans/scan_{$scanUuid}_before_after.jpg";
            return asset($rel);
        }
        return null;
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
