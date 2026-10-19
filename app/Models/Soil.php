<?php

namespace App\Models;

use App\Livewire\Admin\MunsellColor\ManageMunsellColor;
use Illuminate\Database\Eloquent\Model;

class Soil extends Model
{
    protected $primaryKey = 'soil_id';

    protected $fillable = [
        'soil_id',
        'soil_name',
        'description',
        'hue',
        'full_code',
        'soil_type',
        'organic_matter',
        'hex_color',
        'status',
        'scan_count',
        'color_id',
        'admin_id',
        'last_admin_id',
    ];

    protected $casts = [
        'scan_count' => 'integer',
    ];

    protected $appends = [
        'effective_soil_type',
        'effective_hex_color',
        'effective_organic_matter',
    ];

    public function getSoilTypeAttribute($value): string
    {
        if (!empty($value) && trim($value) !== '') {
            return $value;
        }
        return $this->effective_soil_type;
    }

    public function getIdAttribute()
    {
        return $this->attributes['soil_id'] ?? $this->attributes['id'] ?? null;
    }

    public function munsellColor()
    {
        return $this->belongsTo(
            MunsellColor::class,
            'color_id',
            'munsell_color_id'
        );
    }

    public function getDynamicMunsellColorAttribute(): ?MunsellColor
    {
        if ($this->munsellColor) {
            return $this->munsellColor;
        }

        if (!empty($this->full_code)) {
            $matched = MunsellColor::where('full_code', $this->full_code)->first();
            if ($matched) {
                return $matched;
            }
        }

        if (!empty($this->hue)) {
            return MunsellColor::where('hue', $this->hue)->first();
        }

        return null;
    }

    public function getDynamicValueAttribute(): float
    {
        if ($this->dynamic_munsell_color && $this->dynamic_munsell_color->value !== null) {
            return (float) $this->dynamic_munsell_color->value;
        }
        if (!empty($this->full_code) && preg_match('/(\d+(?:\.\d+)?)\s*\/\s*(\d+(?:\.\d+)?)/', $this->full_code, $m)) {
            return (float) $m[1];
        }
        return 5.0;
    }

    public function getDynamicChromaAttribute(): float
    {
        if ($this->dynamic_munsell_color && $this->dynamic_munsell_color->chroma !== null) {
            return (float) $this->dynamic_munsell_color->chroma;
        }
        if (!empty($this->full_code) && preg_match('/(\d+(?:\.\d+)?)\s*\/\s*(\d+(?:\.\d+)?)/', $this->full_code, $m)) {
            return (float) $m[2];
        }
        return 3.0;
    }

    /**
     * Unified Soil Description:
     * Master Manage Munsell Color database (Admin side) -> Custom saved edit -> standard default.
     */
    public function getEffectiveDescriptionAttribute(): string
    {
        if ($this->dynamic_munsell_color && !empty(trim((string)$this->dynamic_munsell_color->description))) {
            return trim($this->dynamic_munsell_color->description);
        }
        if (!empty($this->attributes['description']) && trim((string)$this->attributes['description']) !== '') {
            return trim($this->attributes['description']);
        }
        return 'Sample processed successfully using AgriCo Munsell Determination Algorithm.';
    }

    public function getDynamicDescriptionAttribute(): string
    {
        return $this->effective_description;
    }

    /**
     * Unified Soil Name:
     * Master Manage Munsell Color database (Admin side) -> Custom saved edit -> standard default.
     */
    public function getEffectiveSoilNameAttribute(): string
    {
        if ($this->dynamic_munsell_color && !empty(trim((string)$this->dynamic_munsell_color->color_name))) {
            return trim($this->dynamic_munsell_color->color_name);
        }
        if (!empty($this->attributes['soil_name']) && trim((string)$this->attributes['soil_name']) !== '') {
            return trim($this->attributes['soil_name']);
        }
        return 'Determined Soil Specimen';
    }

    public function getDynamicSoilNameAttribute(): string
    {
        return $this->effective_soil_name;
    }

    /**
     * Unified Hex Color:
     * Master Munsell Hex Code (Admin side) -> Custom override -> dynamic algorithm hex.
     */
    public function getEffectiveHexColorAttribute(): string
    {
        if ($this->dynamic_munsell_color && !empty(trim((string)$this->dynamic_munsell_color->hex_code))) {
            return trim($this->dynamic_munsell_color->hex_code);
        }
        if (!empty($this->attributes['hex_color']) && trim((string)$this->attributes['hex_color']) !== '') {
            return trim($this->attributes['hex_color']);
        }
        if (class_exists(ManageMunsellColor::class)) {
            return ManageMunsellColor::munsellToHex(
                $this->hue ?: ($this->dynamic_munsell_color?->hue ?? '7.5YR'),
                $this->dynamic_value,
                $this->dynamic_chroma,
                '#8F694E'
            );
        }
        return '#8F694E';
    }

    /**
     * Computed Organic Index based on Munsell value.
     */
    public function getComputedOrganicMatterAttribute(): string
    {
        $val = $this->dynamic_value;
        if ($val <= 3.0) {
            return 'High (3.8% – 5.4%)';
        } elseif ($val >= 6.0) {
            return 'Low (1.1% – 2.0%)';
        }
        return 'Moderate (2.2% – 3.7%)';
    }

    /**
     * Unified Soil Organic Index:
     * Custom override -> dynamically computed master default.
     */
    public function getEffectiveOrganicMatterAttribute(): string
    {
        if (!empty($this->attributes['organic_matter']) && trim($this->attributes['organic_matter']) !== '') {
            return $this->attributes['organic_matter'];
        }
        return $this->computed_organic_matter;
    }

    /**
     * Computed Soil Type based on Munsell value & hue.
     */
    public function getComputedSoilTypeAttribute(): string
    {
        $val = $this->dynamic_value;
        if ($val <= 3.0) {
            return 'Organic Moist Clay';
        } elseif ($val >= 6.0) {
            return 'Sandy Loam / Alluvial';
        }
        return 'Well-Drained Clay / Latosol';
    }

    /**
     * Unified Soil Type:
     * Custom override -> Master Manage Munsell Color database default -> Munsell lookup table -> dynamically computed fallback.
     */
    public function getEffectiveSoilTypeAttribute(): string
    {
        if (!empty($this->attributes['soil_type']) && trim($this->attributes['soil_type']) !== '') {
            return $this->attributes['soil_type'];
        }
        if ($this->dynamic_munsell_color && !empty($this->dynamic_munsell_color->soil_type)) {
            return $this->dynamic_munsell_color->soil_type;
        }
        if (!empty($this->full_code)) {
            $lookup = \App\Services\MunsellLookupService::lookup($this->full_code);
            if ($lookup && !empty($lookup['soil_type'])) {
                return $lookup['soil_type'];
            }
        }
        return $this->computed_soil_type;
    }

    /**
     * Multi-Parameter Color Spaces (RGB, HEX, HSL, HWB, CMYK, XYZ, LAB, LUV)
     */
    public function getColorSpaceBadgesAttribute(): array
    {
        $hex = ltrim($this->effective_hex_color, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        if (strlen($hex) !== 6) {
            $hex = '8F694E';
        }

        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        $rNorm = $r / 255;
        $gNorm = $g / 255;
        $bNorm = $b / 255;

        // HSL
        $max = max($rNorm, $gNorm, $bNorm);
        $min = min($rNorm, $gNorm, $bNorm);
        $l = ($max + $min) / 2;
        $d = $max - $min;
        $h = 0;
        $s = 0;
        if ($d != 0) {
            $s = $l > 0.5 ? $d / (2 - $max - $min) : $d / ($max + $min);
            if ($max == $rNorm) {
                $h = ($gNorm - $bNorm) / $d + ($gNorm < $bNorm ? 6 : 0);
            } elseif ($max == $gNorm) {
                $h = ($bNorm - $rNorm) / $d + 2;
            } else {
                $h = ($rNorm - $gNorm) / $d + 4;
            }
            $h /= 6;
        }

        // HWB
        $w = $min;
        $k = 1 - $max;

        // CMYK
        $cmykK = 1 - $max;
        $c = $cmykK < 1 ? ($max - $rNorm) / (1 - $cmykK) : 0;
        $m = $cmykK < 1 ? ($max - $gNorm) / (1 - $cmykK) : 0;
        $y = $cmykK < 1 ? ($max - $bNorm) / (1 - $cmykK) : 0;

        // sRGB to XYZ (D65)
        $pivotR = ($rNorm > 0.04045) ? pow(($rNorm + 0.055) / 1.055, 2.4) : ($rNorm / 12.92);
        $pivotG = ($gNorm > 0.04045) ? pow(($gNorm + 0.055) / 1.055, 2.4) : ($gNorm / 12.92);
        $pivotB = ($bNorm > 0.04045) ? pow(($bNorm + 0.055) / 1.055, 2.4) : ($bNorm / 12.92);

        $xyzX = ($pivotR * 0.4124 + $pivotG * 0.3576 + $pivotB * 0.1805) * 100;
        $xyzY = ($pivotR * 0.2126 + $pivotG * 0.7152 + $pivotB * 0.0722) * 100;
        $xyzZ = ($pivotR * 0.0193 + $pivotG * 0.1192 + $pivotB * 0.9505) * 100;

        // XYZ to LAB
        $xr = $xyzX / 95.047;
        $yr = $xyzY / 100.0;
        $zr = $xyzZ / 108.883;

        $fx = ($xr > 0.008856) ? pow($xr, 1/3) : (7.787 * $xr + 16/116);
        $fy = ($yr > 0.008856) ? pow($yr, 1/3) : (7.787 * $yr + 16/116);
        $fz = ($zr > 0.008856) ? pow($zr, 1/3) : (7.787 * $zr + 16/116);

        $labL = max(0, min(100, (116 * $fy) - 16));
        $labA = ($fx - $fy) * 500;
        $labB = ($fy - $fz) * 200;

        // LUV
        $denom = ($xyzX + 15 * $xyzY + 3 * $xyzZ);
        $uPrime = $denom > 0 ? (4 * $xyzX) / $denom : 0;
        $vPrime = $denom > 0 ? (9 * $xyzY) / $denom : 0;

        $u0 = 0.2009;
        $v0 = 0.4610;
        $luvU = 13 * $labL * ($uPrime - $u0);
        $luvV = 13 * $labL * ($vPrime - $v0);

        return [
            'RGB'  => "{$r}, {$g}, {$b}",
            'HEX'  => '#' . strtoupper($hex),
            'HSL'  => round($h * 360) . '°, ' . round($s * 100) . '%, ' . round($l * 100) . '%',
            'HWB'  => round($h * 360) . '°, ' . round($w * 100) . '%, ' . round($k * 100) . '%',
            'CMYK' => round($c * 100) . '%, ' . round($m * 100) . '%, ' . round($y * 100) . '%, ' . round($cmykK * 100) . '%',
            'XYZ'  => number_format($xyzX / 10, 2) . ', ' . number_format($xyzY / 10, 2) . ', ' . number_format($xyzZ / 10, 2),
            'LAB'  => number_format($labL, 2) . ', ' . number_format($labA, 2) . ', ' . number_format($labB, 2),
            'LUV'  => number_format($labL, 2) . ', ' . number_format($luvU, 2) . ', ' . number_format($luvV, 2),
        ];
    }

    public function admin()
    {
        return $this->belongsTo(
            User::class,
            'admin_id',
            'user_id'
        );
    }

    public function soilScans()
    {
        return $this->hasMany(
            SoilScan::class,
            'soil_id',
            'soil_id'
        );
    }
}
