<?php

namespace App\Http\Controllers\Mobile\Api;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;

class AppVersionController extends Controller
{
    /**
     * Check the latest available mobile application version.
     */
    public function checkVersion(Request $request)
    {
        $activeApp = Application::where('status', 'ACTIVE')->latest()->first();

        $defaultVersion = 'v1.0.4';
        $defaultNotes = "• Full on-device offline soil scanning (6-Stage ML determination)\n• Complete offline Munsell color chart with 207 colors\n• Enhanced camera preview with auto-zoom & bounding box alignment\n• Persistent offline scan history with auto-sync\n• In-app version checker with instant one-tap update";
        $defaultSize = '59.5 MB';
        $defaultUrl = 'https://github.com/agricosite77-oss/agrico-releases/releases/download/v1.0.4/agrico-app-v1.0.4.apk';

        $version = $activeApp?->version ?: $defaultVersion;
        // Ensure version is at least v1.0.4 so older clients get the update prompt
        if (in_array($version, ['v1.0.0', 'v1.0.1', 'v1.0.2', 'v1.0.3'])) {
            $version = 'v1.0.4';
        }

        $releaseNotes = $activeApp?->release_notes ?: $defaultNotes;
        $fileSize = $activeApp?->file_size ?: $defaultSize;

        $filePath = $activeApp?->file_path;
        if ($filePath && (str_starts_with($filePath, 'http://') || str_starts_with($filePath, 'https://'))) {
            $apkUrl = $filePath;
        } else {
            $apkUrl = $defaultUrl;
        }
        // Normalize repository link to public repository agrico-releases so downloads never 404
        if (str_contains($apkUrl, 'github.com/agricosite77-oss/agrico/')) {
            $apkUrl = str_replace('github.com/agricosite77-oss/agrico/', 'github.com/agricosite77-oss/agrico-releases/', $apkUrl);
        }
        if (str_contains($apkUrl, 'v1.0.0') || str_contains($apkUrl, 'v1.0.2')) {
            $apkUrl = str_replace(['v1.0.0', 'v1.0.2'], 'v1.0.4', $apkUrl);
        }

        $isMaintenance = Application::isMaintenanceMode();

        return response()->json([
            'status'         => 'success',
            'latest_version' => $version,
            'release_notes'  => $releaseNotes,
            'file_size'      => $fileSize,
            'download_url'   => url('/download'),
            'direct_apk_url' => $apkUrl,
            'is_maintenance' => $isMaintenance,
            'published_at'   => $activeApp?->created_at?->toIso8601String() ?? now()->toIso8601String(),
        ]);
    }
}
