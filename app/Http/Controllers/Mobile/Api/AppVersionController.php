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

        $defaultVersion = 'v1.0.0';
        $defaultNotes = 'Performance improvements, offline Munsell color chart, personalized welcome banner, and bug fixes.';
        $defaultSize = '62.3 MB';
        $defaultUrl = 'https://github.com/agricosite77-oss/agrico-releases/releases/download/v1.0.0/agrico-app-v1.0.0.apk';

        $version = $activeApp?->version ?: $defaultVersion;
        $releaseNotes = $activeApp?->release_notes ?: $defaultNotes;
        $fileSize = $activeApp?->file_size ?: $defaultSize;

        $filePath = $activeApp?->file_path;
        if ($filePath && (str_starts_with($filePath, 'http://') || str_starts_with($filePath, 'https://'))) {
            $apkUrl = $filePath;
        } else {
            $apkUrl = $defaultUrl;
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
