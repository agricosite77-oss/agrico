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

        // Check if thumbnail was requested
        $isThumbRequest = ($typeOrFallback === 'thumb' || $typeOrFallback === true);

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

        // 4. Sibling scan with photo for the same soil
        $soil = $soilScan?->soil ?? ($scan->soil ?? null);
        if ($soil) {
            $sibling = SoilScan::where('soil_id', $soil->soil_id)
                ->where(function ($q) {
                    $q->whereNotNull('scan_image')->where('scan_image', '!=', '')
                      ->orWhereNotNull('thumbnail_image')->where('thumbnail_image', '!=', '');
                })
                ->where('soil_scan_id', '!=', $soilScan?->soil_scan_id ?? 0)
                ->latest('soil_scan_id')
                ->first();
            if ($sibling) {
                if ($isThumbRequest && !empty($sibling->thumbnail_image)) {
                    $thumbUrl = static::normalizeUrl($sibling->thumbnail_image, $cloudStorage);
                    if ($thumbUrl) return $thumbUrl;
                }
                if (!empty($sibling->scan_image)) {
                    $photoUrl = static::normalizeUrl($sibling->scan_image, $cloudStorage);
                    if ($photoUrl) return $photoUrl;
                }
            }
        }

        // 5. Munsell Reference Image fallback
        $mColor = $soilScan?->munsellColor ?? ($soil?->munsellColor ?? null);
        if ($mColor?->image) {
            $mImg = static::normalizeUrl($mColor->image, $cloudStorage);
            if ($mImg) return $mImg;
        }

        // 6. Soil Reference Image fallback
        if ($soil?->image) {
            $sImg = static::normalizeUrl($soil->image, $cloudStorage);
            if ($sImg) return $sImg;
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

        // 3. Sibling scan with Before & After image for the same soil
        $soil = $soilScan?->soil ?? ($scan->soil ?? null);
        if ($soil) {
            $sibling = SoilScan::where('soil_id', $soil->soil_id)
                ->whereNotNull('before_after_image')
                ->where('before_after_image', '!=', '')
                ->where('soil_scan_id', '!=', $soilScan?->soil_scan_id ?? 0)
                ->latest('soil_scan_id')
                ->first();
            if ($sibling && !empty($sibling->before_after_image)) {
                $url = static::normalizeUrl($sibling->before_after_image, $cloudStorage);
                if ($url) return $url;
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
