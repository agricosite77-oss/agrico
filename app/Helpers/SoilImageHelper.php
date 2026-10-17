<?php

namespace App\Helpers;

use App\Models\SoilScan;
use App\Models\LocationMapping;
use App\Services\CloudStorageService;

class SoilImageHelper
{
    /**
     * Resolves the verified or best-effort URL for a soil sample photo.
     * Returns null if no valid image or fallback can be resolved.
     */
    public static function scanImageUrl($scan, bool|string $typeOrFallback = true): ?string
    {
        if (!$scan) {
            return null;
        }

        // If a LocationMapping is passed, extract its underlying SoilScan
        $soilScan = ($scan instanceof SoilScan) ? $scan : ($scan->soilScan ?? null);
        $cloudStorage = app(CloudStorageService::class);

        // Check if thumbnail or clean image was requested
        $isCleanRequest = ($typeOrFallback === 'clean' || $typeOrFallback === false);
        $isThumbRequest = !$isCleanRequest && ($typeOrFallback === 'thumb' || $typeOrFallback === true);

        // If a clean image without baked-in bounding box was requested, prioritize main scan_image
        if ($isCleanRequest) {
            $raw = $soilScan?->scan_image ?? ($scan->soil_photo_path ?? ($scan->scan_image ?? null));
            if (!empty($raw)) {
                $photoUrl = static::normalizeUrl($raw, $cloudStorage);
                if ($photoUrl) return $photoUrl;
            }
        }

        // If this record has its own dedicated cropped region image (region_*),
        // prioritize it so secondary soil colors never inherit the dominant color's thumbnail!
        $rawScanImage = $soilScan?->scan_image ?? '';
        if ($rawScanImage && str_contains($rawScanImage, 'region_')) {
            $regionUrl = static::normalizeUrl($rawScanImage, $cloudStorage);
            if ($regionUrl) return $regionUrl;
        }

        // 1. Direct thumbnail_image column if present and thumbnail requested
        if ($isThumbRequest && $soilScan && !empty($soilScan->thumbnail_image)) {
            $thumbUrl = static::normalizeUrl($soilScan->thumbnail_image, $cloudStorage);
            if ($thumbUrl) return $thumbUrl;
        }

        // 2. Main scan_image
        $raw = $soilScan?->scan_image ?? ($scan->soil_photo_path ?? ($scan->scan_image ?? null));
        if (!empty($raw)) {
            $photoUrl = static::normalizeUrl($raw, $cloudStorage);
            if ($photoUrl) return $photoUrl;
        }

        // 3. Embedded Region Thumbnail fallback (From database JSON column with green ROI square)
        if ($isThumbRequest && $soilScan) {
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

        // 4. Munsell Reference Image fallback
        if ($soilScan?->munsell_color?->image) {
            $mImg = ltrim($soilScan->munsell_color->image, '/');
            if (!str_starts_with($mImg, 'storage/')) $mImg = 'storage/' . $mImg;
            if (file_exists(public_path($mImg))) {
                return asset($mImg);
            }
        }

        // 5. Soil Reference Image fallback
        $soil = $soilScan?->soil ?? ($scan->soil ?? null);
        if ($soil?->image) {
            $sImg = ltrim($soil->image, '/');
            if (!str_starts_with($sImg, 'storage/')) $sImg = 'storage/' . $sImg;
            if (file_exists(public_path($sImg))) {
                return asset($sImg);
            }
        }

        return null;
    }

    /**
     * Resolves the Before & After normalization comparison image URL.
     * Returns null if missing so UI cleanly renders the standardized placeholder.
     */
    public static function beforeAfterImageUrl($scan): ?string
    {
        if (!$scan) {
            return null;
        }

        $soilScan = ($scan instanceof SoilScan) ? $scan : ($scan->soilScan ?? null);
        $cloudStorage = app(CloudStorageService::class);

        // 1. Direct database column in soil_scans table
        if ($soilScan && !empty($soilScan->before_after_image)) {
            $url = static::normalizeUrl($soilScan->before_after_image, $cloudStorage);
            if ($url) return $url;
        }

        // 2. Candidate derived from scan UUID
        $rawImg = $soilScan?->scan_image ?? ($scan->soil_photo_path ?? ($scan->soil?->image ?? $soilScan?->scan_note));
        if (!empty($rawImg) && preg_match('/(?:scan|region)_([a-f0-9\-]{8,})/i', $rawImg, $m)) {
            $scanUuid = $m[1];
            $rel = "scans/scan_{$scanUuid}_before_after.jpg";
            $clean = "storage/{$rel}";

            if (file_exists(public_path($clean)) || file_exists(storage_path('app/public/' . $rel))) {
                return asset($clean);
            }

            // Check if R2 or Cloud Storage has this path
            $cloudUrl = $cloudStorage->publicUrl($rel);
            if ($cloudUrl && !str_contains($cloudUrl, 'localhost')) {
                return $cloudUrl;
            }
        }

        return null;
    }

    /**
     * Normalizes a storage path, URL, or data URI into a secure HTTPS / asset URL.
     */
    protected static function normalizeUrl(string $raw, CloudStorageService $cloudStorage): ?string
    {
        $raw = trim($raw);
        if (empty($raw)) return null;

        // Base64 Data URI
        if (str_starts_with($raw, 'data:image')) {
            return $raw;
        }

        // Full HTTPS or Cloud URL (Cloudflare R2, Supabase, S3)
        if (str_starts_with($raw, 'http://') || str_starts_with($raw, 'https://')) {
            // Rewrite localhost URLs to current asset
            if (preg_match('#^https?://(?:localhost|127\.0\.0\.1|10\.0\.2\.2)(?::\d+)?/(.*)$#i', $raw, $matches)) {
                $clean = ltrim($matches[1], '/');
                if (file_exists(public_path($clean))) {
                    return asset($clean);
                }
            } else {
                return $raw;
            }
        }

        // Relative storage path
        $clean = ltrim($raw, '/');
        if (!str_starts_with($clean, 'storage/')) {
            $clean = 'storage/' . $clean;
        }
        $rel = preg_replace('#^storage/#', '', $clean);

        if (file_exists(public_path($clean)) || file_exists(storage_path('app/public/' . $rel))) {
            return asset($clean);
        }

        // Return cloud public URL (e.g. Cloudflare R2 / Supabase)
        return $cloudStorage->publicUrl($rel);
    }
}
