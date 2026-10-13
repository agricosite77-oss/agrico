<?php

namespace App\Http\Controllers\Mobile\Api;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AppVersionController extends Controller
{
    /**
     * Resolve direct CDN / asset URL for GitHub release so clients get HTTP 200
     * and avoid client-side URL mutation or multi-hop redirect limits in RNFS.
     */
    public static function resolveDirectAssetUrl(string $githubUrl): string
    {
        // Sanitize agrico-releases to agrico if present
        if (str_contains($githubUrl, 'agrico-releases')) {
            $githubUrl = str_replace('agrico-releases', 'agrico', $githubUrl);
        }

        return Cache::remember('resolved_direct_apk_' . md5($githubUrl), 1800, function () use ($githubUrl) {
            try {
                $ch = curl_init($githubUrl);
                curl_setopt($ch, CURLOPT_NOBODY, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 6);
                curl_exec($ch);
                $finalUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if ($httpCode === 200 && !empty($finalUrl) && str_starts_with($finalUrl, 'https://')) {
                    return $finalUrl;
                }
            } catch (\Throwable $e) {}

            return $githubUrl;
        });
    }

    /**
     * Check the latest available mobile application version.
     */
    public function checkVersion(Request $request)
    {
        $activeApp = Application::where('status', 'ACTIVE')->latest()->first();

        $defaultVersion = 'v1.0.4';
        $defaultNotes = "• Full on-device offline soil scanning (6-Stage ML determination)\n• Complete offline Munsell color chart with 207 colors\n• Enhanced camera preview with auto-zoom & bounding box alignment\n• Persistent offline scan history with auto-sync\n• In-app version checker with instant one-tap update";
        $defaultSize = '59.5 MB';
        $defaultUrl = 'https://github.com/agricosite77-oss/agrico/releases/download/v1.0.4/agrico-app-v1.0.4.apk';

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

        if (str_contains($apkUrl, 'agrico-releases')) {
            $apkUrl = str_replace('agrico-releases', 'agrico', $apkUrl);
        }
        if (str_contains($apkUrl, 'v1.0.0') || str_contains($apkUrl, 'v1.0.2')) {
            $apkUrl = str_replace(['v1.0.0', 'v1.0.2'], 'v1.0.4', $apkUrl);
        }

        $directApkUrl = self::resolveDirectAssetUrl($apkUrl);

        $isMaintenance = Application::isMaintenanceMode();
        $downloadUrl = $request->getSchemeAndHttpHost() . '/download';

        return response()->json([
            'status'         => 'success',
            'latest_version' => $version,
            'release_notes'  => $releaseNotes,
            'file_size'      => $fileSize,
            'download_url'   => $downloadUrl,
            'direct_apk_url' => $directApkUrl,
            'github_url'     => $apkUrl,
            'is_maintenance' => $isMaintenance,
            'published_at'   => $activeApp?->created_at?->toIso8601String() ?? now()->toIso8601String(),
        ]);
    }
}
