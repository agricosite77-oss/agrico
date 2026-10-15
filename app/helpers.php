<?php

use App\Helpers\SoilImageHelper;

if (!function_exists('soil_scan_image_url')) {
    /**
     * Get the effective publicly accessible URL or embedded base64 thumbnail for a soil scan image.
     */
    function soil_scan_image_url($scan, bool $fallbackToThumbnail = true): ?string
    {
        return SoilImageHelper::scanImageUrl($scan, $fallbackToThumbnail);
    }
}

if (!function_exists('soil_before_after_image_url')) {
    /**
     * Get the effective publicly accessible URL for a soil scan before/after normalization image.
     */
    function soil_before_after_image_url($scan): ?string
    {
        return SoilImageHelper::beforeAfterImageUrl($scan);
    }
}
