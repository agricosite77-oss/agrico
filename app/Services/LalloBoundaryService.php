<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class LalloBoundaryService
{
    protected static ?array $cachedFeatures = null;

    /**
     * 35 Calibrated Official Barangays with Centroids
     */
    public const BARANGAYS_35 = [
        ['name' => 'Abagao',                    'lat' => 18.2431, 'lng' => 121.7410],
        ['name' => 'Alaguia',                   'lat' => 18.1564, 'lng' => 121.6358],
        ['name' => 'Bagumbayan',                'lat' => 18.1924, 'lng' => 121.6690],
        ['name' => 'Bangag',                    'lat' => 18.1176, 'lng' => 121.6632],
        ['name' => 'Bical',                     'lat' => 18.2402, 'lng' => 121.7289],
        ['name' => 'Bicud',                     'lat' => 18.1283, 'lng' => 121.5843],
        ['name' => 'Binag',                     'lat' => 18.1813, 'lng' => 121.6949],
        ['name' => 'Cabayabasan (Capacuan)',     'lat' => 18.1129, 'lng' => 121.5687],
        ['name' => 'Cagoran',                   'lat' => 18.1880, 'lng' => 121.6845],
        ['name' => 'Cambong',                   'lat' => 18.1909, 'lng' => 121.7133],
        ['name' => 'Catayauan',                 'lat' => 18.1728, 'lng' => 121.6610],
        ['name' => 'Catugan',                   'lat' => 18.1659, 'lng' => 121.6319],
        ['name' => 'Centro (Pob.)',              'lat' => 18.2007, 'lng' => 121.6647],
        ['name' => 'Cullit',                    'lat' => 18.2408, 'lng' => 121.7515],
        ['name' => 'Dagupan',                   'lat' => 18.1776, 'lng' => 121.7383],
        ['name' => 'Dalaya',                    'lat' => 18.1746, 'lng' => 121.6724],
        ['name' => 'Fabrica',                   'lat' => 18.2012, 'lng' => 121.6316],
        ['name' => 'Fusina',                    'lat' => 18.2393, 'lng' => 121.7091],
        ['name' => 'Jurisdiction',              'lat' => 18.2245, 'lng' => 121.6901],
        ['name' => 'Lalafugan',                 'lat' => 18.1495, 'lng' => 121.6142],
        ['name' => 'Logac',                     'lat' => 18.1518, 'lng' => 121.6103],
        ['name' => 'Magallungon (Sta. Teresa)', 'lat' => 18.2174, 'lng' => 121.6942],
        ['name' => 'Magapit',                   'lat' => 18.1224, 'lng' => 121.6868],
        ['name' => 'Malanao',                   'lat' => 18.1939, 'lng' => 121.6285],
        ['name' => 'Maxingal',                  'lat' => 18.2131, 'lng' => 121.6832],
        ['name' => 'Naguilian',                 'lat' => 18.2214, 'lng' => 121.7724],
        ['name' => 'Paranum',                   'lat' => 18.1595, 'lng' => 121.6705],
        ['name' => 'Rosario',                   'lat' => 18.2056, 'lng' => 121.7405],
        ['name' => 'San Antonio (Lafu)',         'lat' => 18.1832, 'lng' => 121.6428],
        ['name' => 'San Jose',                  'lat' => 18.2057, 'lng' => 121.6638],
        ['name' => 'San Juan',                  'lat' => 18.2349, 'lng' => 121.7219],
        ['name' => 'San Lorenzo',               'lat' => 18.1510, 'lng' => 121.6434],
        ['name' => 'San Mariano',               'lat' => 18.1297, 'lng' => 121.7300],
        ['name' => 'Santa Maria',               'lat' => 18.1505, 'lng' => 121.6715],
        ['name' => 'Tucalana',                  'lat' => 18.1805, 'lng' => 121.6674],
    ];

    /**
     * Load features from lal-lo-with-barangays.geojson
     */
    protected static function getFeatures(): array
    {
        if (static::$cachedFeatures !== null) {
            return static::$cachedFeatures;
        }

        $path = public_path('geojson/lal-lo-with-barangays.geojson');
        if (!File::exists($path)) {
            Log::warning("[LalloBoundaryService] GeoJSON file not found at: {$path}");
            return [];
        }

        try {
            $json = json_decode(File::get($path), true);
            static::$cachedFeatures = $json['features'] ?? [];
        } catch (\Throwable $e) {
            Log::error("[LalloBoundaryService] Error parsing GeoJSON: " . $e->getMessage());
            static::$cachedFeatures = [];
        }

        return static::$cachedFeatures;
    }

    /**
     * Check if point is inside a GeoJSON geometry using Ray-Casting.
     */
    public static function pointInPolygon(float $lat, float $lng, array $geometry, bool $withTolerance = true): bool
    {
        if (empty($geometry['coordinates'])) {
            return false;
        }

        $coords = $geometry['coordinates'];

        $raycast = function (array $pt, array $ring): bool {
            $inside = false;
            $len = count($ring);
            for ($i = 0, $j = $len - 1; $i < $len; $j = $i++) {
                $xi = $ring[$i][0]; $yi = $ring[$i][1];
                $xj = $ring[$j][0]; $yj = $ring[$j][1];
                $intersect = (($yi > $pt[1]) !== ($yj > $pt[1])) &&
                    ($pt[0] < ($xj - $xi) * ($pt[1] - $yi) / ($yj - $yi) + $xi);
                if ($intersect) {
                    $inside = !$inside;
                }
            }
            return $inside;
        };

        $testPoint = function (array $pt) use ($geometry, $coords, $raycast): bool {
            if (($geometry['type'] ?? '') === 'Polygon') {
                return $raycast($pt, $coords[0]);
            } elseif (($geometry['type'] ?? '') === 'MultiPolygon') {
                foreach ($coords as $poly) {
                    if (!empty($poly[0]) && $raycast($pt, $poly[0])) {
                        return true;
                    }
                }
            }
            return false;
        };

        if ($testPoint([$lng, $lat])) {
            return true;
        }

        if ($withTolerance) {
            $tol = 0.00025; // ~27 meters edge tolerance
            if ($testPoint([$lng + $tol, $lat])) return true;
            if ($testPoint([$lng - $tol, $lat])) return true;
            if ($testPoint([$lng, $lat + $tol])) return true;
            if ($testPoint([$lng, $lat - $tol])) return true;
        }

        return false;
    }

    /**
     * Check if coordinates are inside Lal-lo municipal boundary
     */
    public static function isInsideLallo(float $lat, float $lng): bool
    {
        $features = static::getFeatures();
        if (empty($features)) {
            // Failsafe: if GeoJSON is unavailable, Lal-lo bounding box fallback
            return ($lat >= 18.09 && $lat <= 18.26 && $lng >= 121.54 && $lng <= 122.25);
        }

        foreach ($features as $f) {
            $props = $f['properties'] ?? [];
            $isMuni = !empty($props['isMunicipality']) || (($props['admin_level'] ?? null) === '6');
            if ($isMuni && !empty($f['geometry'])) {
                return static::pointInPolygon($lat, $lng, $f['geometry'], true);
            }
        }

        return false;
    }

    /**
     * Find nearest barangay centroid
     */
    public static function findNearestBarangay(float $lat, float $lng): ?string
    {
        $nearest = null;
        $minDistSq = INF;

        foreach (static::BARANGAYS_35 as $b) {
            $dLat = $lat - $b['lat'];
            $dLng = $lng - $b['lng'];
            $distSq = ($dLat * $dLat) + ($dLng * $dLng);
            if ($distSq < $minDistSq) {
                $minDistSq = $distSq;
                $nearest = $b['name'];
            }
        }

        return $nearest;
    }

    /**
     * Determine which Barangay a point belongs to, or fallback to nearest inside municipality.
     */
    public static function findBarangayAt(float $lat, float $lng): ?string
    {
        $features = static::getFeatures();
        if (empty($features)) {
            return null;
        }

        // 1. Direct check in 35 barangays
        foreach ($features as $f) {
            $props = $f['properties'] ?? [];
            $isMuni = !empty($props['isMunicipality']) || (($props['admin_level'] ?? null) === '6');
            if (!$isMuni && !empty($f['geometry'])) {
                if (static::pointInPolygon($lat, $lng, $f['geometry'], false)) {
                    return $props['name'] ?? $props['adm4_name'] ?? null;
                }
            }
        }

        // 2. If inside municipality, return nearest barangay
        if (static::isInsideLallo($lat, $lng)) {
            return static::findNearestBarangay($lat, $lng) ?? 'Lal-lo Overall';
        }

        return null;
    }
}
