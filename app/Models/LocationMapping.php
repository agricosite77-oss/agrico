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
        'pinned_soil_index',
        'soil_percentage',
        'soil_confidence',
        'soil_layer_meta',
    ];

    protected $casts = [
        'pinned_soil_index' => 'integer',
        'soil_confidence'   => 'float',
        'soil_layer_meta'   => 'array',
        'latitude'          => 'float',
        'longitude'         => 'float',
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
        'is_single_soil_pin',
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
        $meta = is_array($this->soil_layer_meta) ? $this->soil_layer_meta : json_decode($this->soil_layer_meta ?? '[]', true);
        $sSoil = $this->soil;
        $sMColor = $sSoil?->munsellColor;
        $scan = $this->soilScan;

        $sHex = $meta['hex'] ?? ($sSoil?->effective_hex_color ?: ($sSoil?->hex_color ?: ($sMColor?->hex_code ?: '#8F694E')));
        if ($sHex && !str_starts_with($sHex, '#')) $sHex = '#' . $sHex;

        $pct = $this->soil_percentage ?? (!empty($meta['percent']) ? ($meta['percent'] . '%') : null);
        $conf = $this->soil_confidence ?? ($meta['confidence'] ?? null);

        // Fallback to scan regions_data if percentage/confidence not explicitly set
        if ((!$pct || !$conf) && $scan && !empty($scan->regions_data)) {
            $rd = is_array($scan->regions_data) ? $scan->regions_data : json_decode($scan->regions_data, true);
            if (is_array($rd)) {
                $match = null;
                if ($this->pinned_soil_index !== null && isset($rd[$this->pinned_soil_index])) {
                    $match = $rd[$this->pinned_soil_index];
                } else {
                    foreach ($rd as $r) {
                        if ((isset($r['soil_id']) && $r['soil_id'] == $this->soil_id) ||
                            (isset($r['munsell']) && $r['munsell'] == ($sSoil?->full_code ?? '')) ||
                            (isset($r['munsell_code']) && $r['munsell_code'] == ($sSoil?->full_code ?? ''))) {
                            $match = $r;
                            break;
                        }
                    }
                    if (!$match && isset($rd[0])) {
                        $match = $rd[0];
                    }
                }
                if ($match) {
                    if (!$pct && isset($match['percent'])) $pct = $match['percent'] . '%';
                    if (!$conf && isset($match['confidence'])) $conf = is_numeric($match['confidence']) ? round($match['confidence'], 1) . '%' : $match['confidence'];
                }
            }
        }

        if (!$pct) $pct = '100%';
        if (is_numeric($pct)) $pct = $pct . '%';
        if (!$conf) $conf = '95%';
        if (is_numeric($conf)) $conf = round((float)$conf, 1) . '%';

        return [[
            'scan_id'        => $this->soil_scan_id ?: $this->location_mapping_id,
            'is_primary'     => true,
            'soil_name'      => $meta['label'] ?? ($meta['color_name'] ?? ($sSoil?->soil_name ?: ($sMColor?->color_name ?: 'Soil Color'))),
            'full_code'      => $meta['munsell_code'] ?? ($meta['munsell'] ?? ($sSoil?->full_code ?: ($sMColor?->full_code ?: '—'))),
            'hex_color'      => $sHex,
            'soil_type'      => $meta['soil_name'] ?? ($sSoil?->effective_soil_type ?: ($sSoil?->soil_type ?: ($sMColor?->soil_type ?: 'Leached grayish brown'))),
            'organic_matter' => $meta['organic_matter'] ?? ($sSoil?->effective_organic_matter ?: ($sMColor?->organic_matter ?: 'Moderate (2.2% – 3.7%)')),
            'drainage'       => $sSoil?->drainage ?: ($sMColor?->drainage_profile ?: 'Well Drained'),
            'percentage'     => $pct,
            'confidence'     => $conf,
            'image_url'      => $scan ? \App\Helpers\SoilImageHelper::scanImageUrl($scan) : \App\Helpers\SoilImageHelper::scanImageUrl($this),
            'description'    => $sSoil?->description ?: ($meta['description'] ?? ($scan?->scan_note ?: '')),
            'pinned_index'   => $this->pinned_soil_index,
        ]];
    }

    public function getDetectedSoilsCountAttribute(): int
    {
        return count($this->detected_soils);
    }

    public function getScanImageUrlAttribute(): ?string
    {
        return \App\Helpers\SoilImageHelper::scanImageUrl($this);
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
        return \App\Helpers\SoilImageHelper::beforeAfterImageUrl($this);
    }

    public function getRegionsAttribute(): array
    {
        $meta = is_array($this->soil_layer_meta) ? $this->soil_layer_meta : json_decode($this->soil_layer_meta ?? '[]', true);
        $sSoil = $this->soil;
        $sMColor = $sSoil?->munsellColor;
        $scan = $this->soilScan;

        $sHex = $meta['hex'] ?? ($sSoil?->effective_hex_color ?: ($sSoil?->hex_color ?: ($sMColor?->hex_code ?: '#8F694E')));
        if ($sHex && !str_starts_with($sHex, '#')) $sHex = '#' . $sHex;

        $pctVal = (float) filter_var($this->soil_percentage ?? ($meta['percent'] ?? 0), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $conf = $this->soil_confidence ?? ($meta['confidence'] ?? null);
        $box = $meta['box'] ?? ($meta['bounding_box'] ?? null);

        // Fallback to scan regions_data if needed
        if ((!$pctVal || !$conf || !$box) && $scan && !empty($scan->regions_data)) {
            $rd = is_array($scan->regions_data) ? $scan->regions_data : json_decode($scan->regions_data, true);
            if (is_array($rd)) {
                $match = null;
                if ($this->pinned_soil_index !== null && isset($rd[$this->pinned_soil_index])) {
                    $match = $rd[$this->pinned_soil_index];
                } else {
                    foreach ($rd as $r) {
                        if ((isset($r['soil_id']) && $r['soil_id'] == $this->soil_id) ||
                            (isset($r['munsell']) && $r['munsell'] == ($sSoil?->full_code ?? '')) ||
                            (isset($r['munsell_code']) && $r['munsell_code'] == ($sSoil?->full_code ?? ''))) {
                            $match = $r;
                            break;
                        }
                    }
                    if (!$match && isset($rd[0])) {
                        $match = $rd[0];
                    }
                }
                if ($match) {
                    if (!$pctVal && isset($match['percent'])) $pctVal = (float) $match['percent'];
                    if (!$conf && isset($match['confidence'])) $conf = is_numeric($match['confidence']) ? round($match['confidence'], 1) . '%' : $match['confidence'];
                    if (!$box) $box = $match['box'] ?? ($match['bounding_box'] ?? null);
                }
            }
        }

        if (!$pctVal) $pctVal = 100.0;
        if (!$conf) $conf = '95%';
        if (is_numeric($conf)) $conf = round((float)$conf, 1) . '%';

        $mCode = $meta['munsell_code'] ?? ($meta['munsell'] ?? ($sSoil?->full_code ?: ($sMColor?->full_code ?: '—')));
        $label = $meta['label'] ?? ($meta['color_name'] ?? ($sSoil?->soil_name ?: ($sMColor?->color_name ?: 'Soil Color')));

        return [[
            'scan_id'        => $this->soil_scan_id ?: $this->location_mapping_id,
            'is_primary'     => true,
            'munsell'        => $mCode,
            'munsell_code'   => $mCode,
            'label'          => $label,
            'color_name'     => $label,
            'hex'            => $sHex,
            'percent'        => $pctVal,
            'proportion'     => $pctVal / 100,
            'confidence'     => $conf,
            'near_match'     => $meta['near_match'] ?? $conf,
            'organic_matter' => $meta['organic_matter'] ?? ($sSoil?->effective_organic_matter ?: 'Moderate (2.2% – 3.7%)'),
            'soil_name'      => $meta['soil_name'] ?? ($sSoil?->soil_type_label ?: ($sSoil?->soil_type ?: 'Unspecified')),
            'description'    => $sSoil?->description ?: ($meta['description'] ?? ($scan?->scan_note ?: '')),
            'box'            => $box,
            'bounding_box'   => $box,
            'image'          => $scan ? \App\Helpers\SoilImageHelper::scanImageUrl($scan) : null,
            'pinned_index'   => $this->pinned_soil_index,
        ]];
    }

    public function getScanNoteTextAttribute(): string
    {
        return $this->soilScan?->scan_note ?? 'Standard municipal soil classification sampling.';
    }

    public function getIsSingleSoilPinAttribute(): bool
    {
        return true;
    }
}
