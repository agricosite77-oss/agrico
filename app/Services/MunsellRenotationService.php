<?php

namespace App\Services;

class MunsellRenotationService
{
    protected static ?array $palette = null;

    /**
     * Load the comprehensive Munsell ASTM D1535 renotation hex map.
     */
    public static function getPalette(): array
    {
        if (static::$palette !== null) {
            return static::$palette;
        }

        $path = resource_path('data/munsell_palette.json');
        if (file_exists($path)) {
            static::$palette = json_decode(file_get_contents($path), true) ?: [];
        } else {
            static::$palette = [];
        }

        return static::$palette;
    }

    /**
     * Look up exact hex for a Munsell notation (e.g. "2.5YR 6/8" or "2YR 6.0/8.0").
     */
    public static function notationToHex(string $hue, float|int|string $value, float|int|string $chroma): ?string
    {
        $palette = static::getPalette();
        if (empty($palette)) {
            return null;
        }

        $h = strtoupper(trim($hue));
        $v = (float) $value;
        $c = (float) $chroma;

        $vInt = (int) round($v);
        $cInt = (int) round($c);

        $vClean = (floor($v) == $v) ? (string) $vInt : (string) $v;
        $cClean = (floor($c) == $c) ? (string) $cInt : (string) $c;

        // Try candidate formats
        $candidates = [
            "{$h} {$vClean}/{$cClean}",
            "{$h} {$vInt}/{$cInt}",
            sprintf('%s %.1f/%.1f', $h, $v, $c),
            "{$h} {$vClean}/" . sprintf('%.1f', $c),
            sprintf('%s %.1f/%s', $h, $v, $cClean),
        ];

        // Handle neutral N
        if ($h === 'N' || str_starts_with($h, 'N')) {
            $candidates[] = "N {$vClean}";
            $candidates[] = "N {$vClean}/0";
            $candidates[] = sprintf('N %.1f', $v);
        }

        foreach ($candidates as $cand) {
            if (isset($palette[$cand])) {
                return strtolower($palette[$cand]);
            }
        }

        return null;
    }

    /**
     * Convert HEX to RGB components array.
     */
    public static function hexToRgb(string $hex): array
    {
        $clean = ltrim(trim($hex), '#');
        if (strlen($clean) === 3) {
            $clean = $clean[0].$clean[0].$clean[1].$clean[1].$clean[2].$clean[2];
        }

        $r = hexdec(substr($clean, 0, 2));
        $g = hexdec(substr($clean, 2, 2));
        $b = hexdec(substr($clean, 4, 2));

        return [
            'r' => $r,
            'g' => $g,
            'b' => $b,
            'hex' => '#' . strtolower($clean),
        ];
    }
}
