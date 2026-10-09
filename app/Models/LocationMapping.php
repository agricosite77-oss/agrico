<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class LocationMapping extends Model
{
    use HasFactory;

    protected $primaryKey = 'location_mapping_id';

    protected $fillable = [
        'location_mapping_id',
        'latitude',
        'longitude',
        'barangay',
        'timestamp',
        'status',
        'soil_scan_id',
        'soil_id',
    ];

    protected $appends = [
        'detected_soils',
        'detected_soils_count',
        'scan_image_url',
        'before_after_image_url',
        'regions',
        'scan_date_formatted',
        'scanned_by',
        'scan_platform',
        'scan_note_text',
    ];

    public static function resolveNearestBarangay(float $lat, float $lng): string
    {
        $barangays = [
            ['name' => 'Centro (Poblacion)', 'lat' => 18.1983, 'lng' => 121.6625],
            ['name' => 'Bagumbayan',         'lat' => 18.2045, 'lng' => 121.6580],
            ['name' => 'Catugan',            'lat' => 18.2120, 'lng' => 121.6550],
            ['name' => 'Alaguia',            'lat' => 18.2350, 'lng' => 121.6720],
            ['name' => 'Bical',              'lat' => 18.2280, 'lng' => 121.6650],
            ['name' => 'Bicud',              'lat' => 18.1820, 'lng' => 121.6480],
            ['name' => 'Binag',              'lat' => 18.1750, 'lng' => 121.6520],
            ['name' => 'Cabayabasan',        'lat' => 18.2150, 'lng' => 121.6850],
            ['name' => 'Cagoran',            'lat' => 18.1650, 'lng' => 121.6380],
            ['name' => 'Cambaja',            'lat' => 18.1880, 'lng' => 121.6750],
            ['name' => 'Casambalangan',      'lat' => 18.2020, 'lng' => 121.6700],
            ['name' => 'Cullit',             'lat' => 18.2220, 'lng' => 121.6420],
            ['name' => 'Dagupan',            'lat' => 18.1910, 'lng' => 121.6590],
            ['name' => 'Dalaya',             'lat' => 18.2080, 'lng' => 121.6490],
            ['name' => 'Fabrica',            'lat' => 18.1780, 'lng' => 121.6630],
            ['name' => 'Fusina',             'lat' => 18.2420, 'lng' => 121.6800],
            ['name' => 'Jurisdiction',       'lat' => 18.1850, 'lng' => 121.6680],
            ['name' => 'Lalafugan',          'lat' => 18.2510, 'lng' => 121.6910],
            ['name' => 'Logac',              'lat' => 18.1580, 'lng' => 121.6450],
            ['name' => 'Magallungan',        'lat' => 18.1720, 'lng' => 121.6790],
            ['name' => 'Malanao',            'lat' => 18.2180, 'lng' => 121.6780],
            ['name' => 'Maxingal',           'lat' => 18.2250, 'lng' => 121.6950],
            ['name' => 'Naguilian',          'lat' => 18.1620, 'lng' => 121.6550],
            ['name' => 'Paranum',            'lat' => 18.2300, 'lng' => 121.7100],
            ['name' => 'Rosario',            'lat' => 18.1890, 'lng' => 121.6820],
            ['name' => 'San Antonio (Culit)','lat' => 18.2050, 'lng' => 121.6880],
            ['name' => 'San Jose',           'lat' => 18.2450, 'lng' => 121.7020],
            ['name' => 'San Juan',           'lat' => 18.1950, 'lng' => 121.6530],
            ['name' => 'San Lorenzo',        'lat' => 18.1800, 'lng' => 121.6720],
            ['name' => 'San Mariano',        'lat' => 18.1700, 'lng' => 121.6600],
            ['name' => 'Santa Maria',        'lat' => 18.2380, 'lng' => 121.6880],
            ['name' => 'Simpatuyo',          'lat' => 18.2100, 'lng' => 121.7050],
            ['name' => 'Sta. Teresa',        'lat' => 18.1680, 'lng' => 121.6700],
            ['name' => 'Tucalana',           'lat' => 18.1920, 'lng' => 121.6450],
            ['name' => 'Balza',              'lat' => 18.2010, 'lng' => 121.6510],
        ];

        $closest = 'Centro (Poblacion)';
        $minDist = INF;
        foreach ($barangays as $b) {
            $d = hypot($b['lat'] - $lat, $b['lng'] - $lng);
            if ($d < $minDist) {
                $minDist = $d;
                $closest = $b['name'];
            }
        }
        return $closest;
    }

    public function getBarangayAttribute($value): string
    {
        if (empty($value) || strtolower($value) === 'lal-lo overall' || strtolower($value) === 'all barangays') {
            if (!empty($this->attributes['latitude']) && !empty($this->attributes['longitude'])) {
                return static::resolveNearestBarangay((float) $this->attributes['latitude'], (float) $this->attributes['longitude']);
            }
            return 'Centro (Poblacion)';
        }
        return $value;
    }

    public function getIdAttribute()
    {
        return $this->attributes['location_mapping_id'] ?? $this->attributes['id'] ?? null;
    }

    public function soilScan()
    {
        return $this->belongsTo(
            SoilScan::class,
            'soil_scan_id',
            'soil_scan_id'
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

    public function getDetectedSoilsAttribute(): array
    {
        $scan = $this->soilScan;
        $detectedSoils = [];

        if ($scan) {
            // Check if scan has a session UUID in scan_image
            if ($scan->scan_image && preg_match('/(?:scan|region)_([a-f0-9\-]{10,})/i', $scan->scan_image, $uuidMatch)) {
                $uuid = $uuidMatch[1];
                $sessionScans = SoilScan::with('soil.munsellColor')
                    ->where('scan_image', 'LIKE', "%{$uuid}%")
                    ->orderBy('soil_scan_id', 'asc')
                    ->get();

                foreach ($sessionScans as $sScan) {
                    $sSoil = $sScan->soil;
                    $sMColor = $sSoil?->munsellColor;
                    $sHex = $sSoil?->effective_hex_color ?: ($sSoil?->hex_color ?: ($sMColor?->hex_code ?: '#8F694E'));
                    if ($sHex && !str_starts_with($sHex, '#')) $sHex = '#' . $sHex;

                    $pct = null;
                    $conf = null;
                    if ($sScan->scan_note && preg_match('/Soil Percentage:\s*([0-9.]+%?)/i', $sScan->scan_note, $pm)) {
                        $pct = $pm[1];
                    }
                    if ($sScan->scan_note && preg_match('/Confidence:\s*([0-9.]+%?)/i', $sScan->scan_note, $cm)) {
                        $conf = $cm[1];
                    }
                    if (!$pct && !empty($sScan->regions_data)) {
                        $rd = is_array($sScan->regions_data) ? $sScan->regions_data : json_decode($sScan->regions_data, true);
                        if (!empty($rd[0]['percent'])) $pct = $rd[0]['percent'] . '%';
                        if (!empty($rd[0]['confidence'])) $conf = round($rd[0]['confidence'], 1) . '%';
                    }

                    $detectedSoils[] = [
                        'scan_id'       => $sScan->soil_scan_id,
                        'is_primary'    => ($sScan->soil_scan_id === $scan->soil_scan_id),
                        'soil_name'     => $sSoil?->soil_name ?: ($sMColor?->color_name ?: 'Soil Color'),
                        'full_code'     => $sSoil?->full_code ?: ($sMColor?->full_code ?: '—'),
                        'hex_color'     => $sHex,
                        'soil_type'     => $sSoil?->effective_soil_type ?: ($sSoil?->soil_type ?: ($sMColor?->soil_type ?: 'Leached grayish brown')),
                        'organic_matter'=> $sSoil?->effective_organic_matter ?: ($sMColor?->organic_matter ?: 'Moderate (2.2% – 3.7%)'),
                        'drainage'      => $sSoil?->drainage ?: ($sMColor?->drainage_profile ?: 'Well Drained'),
                        'percentage'    => $pct ?: ($sScan->soil_scan_id === $scan->soil_scan_id ? '57.2%' : '15%'),
                        'confidence'    => $conf ?: '95%',
                        'image_url'     => $sScan->scan_image ? (str_starts_with($sScan->scan_image, 'http') ? $sScan->scan_image : asset($sScan->scan_image)) : null,
                        'description'   => $sSoil?->description ?: ($sScan->scan_note ?: ''),
                    ];
                }
            }

            if (empty($detectedSoils) && !empty($scan->regions_data)) {
                $rd = is_array($scan->regions_data) ? $scan->regions_data : json_decode($scan->regions_data, true);
                if (is_array($rd)) {
                    foreach ($rd as $rIdx => $r) {
                        $rHex = $r['hex'] ?? $this->soil?->effective_hex_color ?? '#8F694E';
                        if (!str_starts_with($rHex, '#')) $rHex = '#' . $rHex;
                        $detectedSoils[] = [
                            'scan_id'       => $scan->soil_scan_id,
                            'is_primary'    => ($rIdx === 0),
                            'soil_name'     => $r['soil_name'] ?? $r['label'] ?? $this->soil?->soil_name ?? 'Detected Soil',
                            'full_code'     => $r['munsell_code'] ?? $r['munsell'] ?? $this->soil?->full_code ?? '—',
                            'hex_color'     => $rHex,
                            'soil_type'     => $this->soil?->effective_soil_type ?: ($this->soil?->soil_type ?: ($this->soil?->munsellColor?->soil_type ?: 'Leached grayish brown')),
                            'organic_matter'=> $this->soil?->effective_organic_matter ?: 'Moderate (2.2% – 3.7%)',
                            'drainage'      => $this->soil?->drainage ?: 'Well Drained',
                            'percentage'    => isset($r['percent']) ? ($r['percent'] . '%') : '—',
                            'confidence'    => isset($r['confidence']) ? ($r['confidence'] . '%') : '95%',
                            'image_url'     => null,
                            'description'   => $this->soil?->description ?? '',
                        ];
                    }
                }
            }
        }

        if (!empty($detectedSoils)) {
            usort($detectedSoils, function($a, $b) {
                if ($a['is_primary'] && !$b['is_primary']) return -1;
                if (!$a['is_primary'] && $b['is_primary']) return 1;
                $pA = (float) filter_var($a['percentage'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                $pB = (float) filter_var($b['percentage'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                return $pB <=> $pA;
            });
        } else {
            $detectedSoils[] = [
                'scan_id'       => $scan?->soil_scan_id ?? $this->location_mapping_id,
                'is_primary'    => true,
                'soil_name'     => $this->soil?->soil_name ?? 'Soil Specimen',
                'full_code'     => $this->soil?->full_code ?? '—',
                'hex_color'     => $this->soil?->effective_hex_color ?? '#8F694E',
                'soil_type'     => $this->soil?->effective_soil_type ?: ($this->soil?->soil_type ?: ($this->soil?->munsellColor?->soil_type ?: 'Leached grayish brown')),
                'organic_matter'=> $this->soil?->effective_organic_matter ?? 'Moderate (2.2% – 3.7%)',
                'drainage'      => $this->soil?->drainage ?? 'Well Drained',
                'percentage'    => '100%',
                'confidence'    => '95%',
                'image_url'     => $scan?->scan_image ? (str_starts_with($scan->scan_image, 'http') ? $scan->scan_image : asset($scan->scan_image)) : null,
                'description'   => $this->soil?->description ?? '',
            ];
        }

        return $detectedSoils;
    }

    public function getDetectedSoilsCountAttribute(): int
    {
        return count($this->detected_soils);
    }

    public function getScanImageUrlAttribute(): ?string
    {
        if ($this->soilScan?->scan_image) {
            return str_starts_with($this->soilScan->scan_image, 'http')
                ? $this->soilScan->scan_image
                : asset($this->soilScan->scan_image);
        }
        return null;
    }

    public function getScanDateFormattedAttribute(): string
    {
        if ($this->soilScan?->scan_date) {
            return Carbon::parse($this->soilScan->scan_date)->format('M d, Y h:i A');
        }
        if ($this->timestamp) {
            return Carbon::parse($this->timestamp)->format('M d, Y h:i A');
        }
        return '—';
    }

    public function getScannedByAttribute(): string
    {
        return $this->soilScan?->user?->name ?? 'Admin Analyst';
    }

    public function getScanPlatformAttribute(): string
    {
        return ucfirst($this->soilScan?->platform ?? 'Website');
    }

    public function getBeforeAfterImageUrlAttribute(): ?string
    {
        if ($this->soilScan?->before_after_image_url) {
            return $this->soilScan->before_after_image_url;
        }
        $rawImg = $this->soilScan?->scan_image ?? $this->soil?->image;
        if (!empty($rawImg) && preg_match('/(?:scan|region)_([a-f0-9\-]{10,})/i', $rawImg, $m)) {
            $scanUuid = $m[1];
            $rel = "storage/scans/scan_{$scanUuid}_before_after.jpg";
            if (file_exists(public_path($rel)) || file_exists(storage_path("app/public/scans/scan_{$scanUuid}_before_after.jpg"))) {
                return asset($rel);
            }
        }
        return null;
    }

    public function getRegionsAttribute(): array
    {
        $scan = $this->soilScan;
        $allRegions = [];

        if ($scan) {
            // 1. If scan belongs to a multi-scan session (grouped by UUID in scan_image)
            if ($scan->scan_image && preg_match('/(?:scan|region)_([a-f0-9\-]{10,})/i', $scan->scan_image, $uuidMatch)) {
                $uuid = $uuidMatch[1];
                $sessionScans = SoilScan::with('soil.munsellColor')
                    ->where('scan_image', 'LIKE', "%{$uuid}%")
                    ->get();

                if ($sessionScans->count() > 1) {
                    foreach ($sessionScans as $sScan) {
                        $sSoil = $sScan->soil;
                        $sMColor = $sSoil?->munsellColor;
                        $sHex = $sSoil?->effective_hex_color ?: ($sSoil?->hex_color ?: ($sMColor?->hex_code ?: '#8F694E'));
                        if ($sHex && !str_starts_with($sHex, '#')) $sHex = '#' . $sHex;

                        $rd = $sScan->regions_data;
                        $r0 = (!empty($rd) && is_array($rd)) ? ($rd[0] ?? []) : [];

                        $mCode = $r0['munsell'] ?? ($r0['munsell_code'] ?? ($sSoil?->full_code ?? '—'));
                        $label = $r0['label'] ?? ($r0['color_name'] ?? ($sSoil?->soil_name ?: ($sMColor?->color_name ?: 'Soil Color')));
                        $hex = $r0['hex'] ?? $sHex;
                        if ($hex && !str_starts_with($hex, '#')) $hex = '#' . $hex;

                        $pct = $r0['percent'] ?? (!empty($r0['proportion']) ? round($r0['proportion'] * 100, 1) : null);
                        if ($pct === null && $sScan->scan_note && preg_match('/Soil Percentage:\s*([0-9.]+%?)/i', $sScan->scan_note, $pm)) {
                            $pct = (float) filter_var($pm[1], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                        }
                        $pct = $pct ?? 25;

                        $conf = $r0['confidence_formatted'] ?? ($r0['confidence'] ?? null);
                        if (!$conf && $sScan->scan_note && preg_match('/Confidence:\s*([0-9.]+%?)/i', $sScan->scan_note, $cm)) {
                            $conf = $cm[1];
                        }
                        if ($conf && is_numeric($conf)) $conf = round($conf, 1) . '%';
                        $conf = $conf ?? '96.5%';

                        $box = $r0['box'] ?? ($r0['bounding_box'] ?? null);

                        $allRegions[] = [
                            'scan_id'        => $sScan->soil_scan_id,
                            'is_primary'     => ($sScan->soil_scan_id === $scan->soil_scan_id),
                            'munsell'        => $mCode,
                            'munsell_code'   => $mCode,
                            'label'          => $label,
                            'color_name'     => $label,
                            'hex'            => $hex,
                            'percent'        => (float)$pct,
                            'proportion'     => ((float)$pct) / 100,
                            'confidence'     => $conf,
                            'near_match'     => $r0['near_match'] ?? $conf,
                            'organic_matter' => $sSoil?->effective_organic_matter ?: ($sMColor?->organic_matter ?: 'Moderate (2.2% – 3.7%)'),
                            'soil_name'      => $sSoil?->soil_type_label ?: ($sSoil?->soil_type ?: ($sMColor?->soil_type ?: 'Unspecified')),
                            'description'    => $sSoil?->description ?: ($sScan->scan_note ?: ''),
                            'box'            => $box,
                            'bounding_box'   => $box,
                            'image'          => $sScan->scan_image ? (str_starts_with($sScan->scan_image, 'http') ? $sScan->scan_image : asset($sScan->scan_image)) : null,
                        ];
                    }

                    // Sort: primary first, then higher percent
                    usort($allRegions, function($a, $b) {
                        if ($a['is_primary'] && !$b['is_primary']) return -1;
                        if (!$a['is_primary'] && $b['is_primary']) return 1;
                        return ($b['percent'] <=> $a['percent']);
                    });

                    return $allRegions;
                }
            }

            // 2. If single scan already has multiple regions in regions_data
            if (!empty($scan->regions) && count($scan->regions) > 1) {
                return $scan->regions;
            }
        }

        // 3. Fallback to detected_soils if multiple items exist
        if (!empty($this->detected_soils) && count($this->detected_soils) > 1) {
            $out = [];
            foreach ($this->detected_soils as $idx => $ds) {
                $pct = (float) filter_var($ds['percentage'] ?? '25', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                $out[] = [
                    'scan_id'       => $ds['scan_id'] ?? null,
                    'is_primary'    => !empty($ds['is_primary']),
                    'munsell'       => $ds['full_code'] ?? '—',
                    'munsell_code'  => $ds['full_code'] ?? '—',
                    'hex'           => $ds['hex_color'] ?? '#8F694E',
                    'label'         => $ds['soil_name'] ?? 'Soil Layer',
                    'color_name'    => $ds['soil_name'] ?? 'Soil Layer',
                    'percent'       => $pct,
                    'proportion'    => $pct / 100,
                    'confidence'    => $ds['confidence'] ?? '95%',
                    'near_match'    => $ds['confidence'] ?? '95%',
                    'organic_matter'=> $ds['organic_matter'] ?? 'Low (1.1% – 2.0%)',
                    'soil_name'     => $ds['soil_type'] ?? 'Unspecified',
                    'description'   => $ds['description'] ?? '',
                    'box'           => [
                        'x' => 0.15 + (($idx % 2) * 0.4),
                        'y' => 0.2 + (floor($idx / 2) * 0.35),
                        'w' => 0.3,
                        'h' => 0.3,
                    ],
                ];
            }
            return $out;
        }

        if (!empty($scan?->regions)) {
            return $scan->regions;
        }

        return [];
    }

    public function getScanNoteTextAttribute(): string
    {
        return $this->soilScan?->scan_note ?? 'Standard municipal soil classification sampling.';
    }
}
