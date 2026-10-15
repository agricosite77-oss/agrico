<?php

namespace App\Helpers;

use App\Models\SoilScan;
use App\Models\LocationMapping;

class SoilImageHelper
{
    /**
     * Resolves the verified or best-effort URL for a soil sample photo.
     * Returns null if no valid image or fallback can be resolved.
     */
    public static function scanImageUrl($scan, bool $fallbackToThumbnail = true): ?string
    {
        if (!$scan) {
            return null;
        }

        // If a LocationMapping is passed, extract its underlying SoilScan
        $soilScan = ($scan instanceof SoilScan) ? $scan : ($scan->soilScan ?? null);
        $raw = $soilScan?->scan_image ?? ($scan->soil_photo_path ?? ($scan->scan_image ?? null));

        if (!empty($raw)) {
            // 1. Base64 Data URI
            if (str_starts_with($raw, 'data:image')) {
                return $raw;
            }

            // 2. Full HTTPS or Supabase URL
            if (str_starts_with($raw, 'http://') || str_starts_with($raw, 'https://')) {
                // If it's a localhost link from an old database dump, re-route to current asset
                if (preg_match('#^https?://(?:localhost|127\.0\.0\.1|10\.0\.2\.2)(?::\d+)?/(.*)$#i', $raw, $matches)) {
                    $clean = ltrim($matches[1], '/');
                    if (file_exists(public_path($clean))) {
                        return asset($clean);
                    }
                } else {
                    return $raw;
                }
            }

            // 3. Local storage path (check if it actually exists on disk)
            $clean = ltrim($raw, '/');
            if (!str_starts_with($clean, 'storage/')) {
                $clean = 'storage/' . $clean;
            }
            $rel = preg_replace('#^storage/#', '', $clean);

            if (file_exists(public_path($clean)) || file_exists(storage_path('app/public/' . $rel))) {
                return asset($clean);
            }

            // 4. If files are stored in Supabase Storage, build the Supabase public URL
            $projectRef = env('SUPABASE_PROJECT_REF', 'qbgrxnbayyykrycgtbig');
            $bucket = env('SUPABASE_STORAGE_BUCKET', 'agrico-scans');
            if (env('FILESYSTEM_DISK') === 's3' || env('USE_SUPABASE_STORAGE', true)) {
                $supabaseUrl = "https://{$projectRef}.supabase.co/storage/v1/object/public/{$bucket}/{$rel}";
                // If we're not falling back to thumbnail, we can return the Supabase URL candidate
                if (!$fallbackToThumbnail) {
                    return $supabaseUrl;
                }
            }
        }

        // 5. Authentic Embedded Region Thumbnail fallback (From database JSON column)
        if ($fallbackToThumbnail && $soilScan) {
            $regs = $soilScan->regions ?? [];
            if (!empty($regs[0]['region_thumbnail'])) {
                $thumb = $regs[0]['region_thumbnail'];
                return str_starts_with($thumb, 'data:') ? $thumb : ('data:image/png;base64,' . $thumb);
            }
            foreach ($regs as $r) {
                if (!empty($r['region_thumbnail'])) {
                    $thumb = $r['region_thumbnail'];
                    return str_starts_with($thumb, 'data:') ? $thumb : ('data:image/png;base64,' . $thumb);
                }
            }
        }

        // 6. Munsell Reference Image fallback
        if ($soilScan?->munsell_color?->image) {
            $mImg = ltrim($soilScan->munsell_color->image, '/');
            if (!str_starts_with($mImg, 'storage/')) $mImg = 'storage/' . $mImg;
            if (file_exists(public_path($mImg))) {
                return asset($mImg);
            }
        }

        // 7. Soil Reference Image fallback
        $soil = $soilScan?->soil ?? ($scan->soil ?? null);
        if ($soil?->image) {
            $sImg = ltrim($soil->image, '/');
            if (!str_starts_with($sImg, 'storage/')) $sImg = 'storage/' . $sImg;
            if (file_exists(public_path($sImg))) {
                return asset($sImg);
            }
        }

        // If file is physically missing and no authentic thumbnail exists, return null so UI shows clean placeholder
        return null;
    }

    /**
     * Resolves the Before & After normalization image URL.
     * Returns null if missing or unverifiable so UI cleanly replaces it.
     */
    public static function beforeAfterImageUrl($scan): ?string
    {
        if (!$scan) {
            return null;
        }

        $soilScan = ($scan instanceof SoilScan) ? $scan : ($scan->soilScan ?? null);
        $rawImg = $soilScan?->scan_image ?? ($scan->soil_photo_path ?? ($scan->soil?->image ?? $soilScan?->scan_note));

        if (!empty($rawImg) && preg_match('/(?:scan|region)_([a-f0-9\-]{10,})/i', $rawImg, $m)) {
            $scanUuid = $m[1];
            $rel = "scans/scan_{$scanUuid}_before_after.jpg";
            $clean = "storage/{$rel}";

            if (file_exists(public_path($clean)) || file_exists(storage_path('app/public/' . $rel))) {
                return asset($clean);
            }

            // Check Supabase public URL candidate
            $projectRef = env('SUPABASE_PROJECT_REF', 'qbgrxnbayyykrycgtbig');
            $bucket = env('SUPABASE_STORAGE_BUCKET', 'agrico-scans');
            if (env('FILESYSTEM_DISK') === 's3') {
                return "https://{$projectRef}.supabase.co/storage/v1/object/public/{$bucket}/{$rel}";
            }
        }

        return null;
    }
}
