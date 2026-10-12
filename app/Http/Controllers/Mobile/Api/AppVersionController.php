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

        $defaultVersion = 'v1.0.2';
        $defaultNotes = "• Full on-device offline soil scanning (6-Stage ML determination)\n• Complete offline Munsell color chart with 207 colors\n• Persistent offline scan history with auto-sync\n• Check for Updates button in Profile";
        $defaultSize = '59.5 MB';
        $defaultUrl = 'https://github.com/agricosite77-oss/agrico/releases/download/v1.0.0/agrico-app-v1.0.0.apk';

        $version = $activeApp?->version ?: $defaultVersion;
        // Ensure version is at least v1.0.2 to deliver full offline scanning capabilities
        if (in_array($version, ['v1.0.0', 'v1.0.1'])) {
            $version = 'v1.0.2';
        }

        $releaseNotes = $activeApp?->release_notes ?: $defaultNotes;
        $fileSize = $activeApp?->file_size ?: $defaultSize;

        $filePath = $activeApp?->file_path;
        if ($filePath && (str_starts_with($filePath, 'http://') || str_starts_with($filePath, 'https://'))) {
            $apkUrl = $filePath;
        } else {
            $apkUrl = $defaultUrl;
        }
        // Normalize repository link to canonical release
        if (str_contains($apkUrl, 'agrico-releases')) {
            $apkUrl = str_replace('agrico-releases', 'agrico', $apkUrl);
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
