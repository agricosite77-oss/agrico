<?php

namespace App\Http\Controllers\Mobile\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class RealSoilController extends Controller
{
    protected string $realSoilRoot;

    public function __construct()
    {
        $this->realSoilRoot = base_path('Scan_Soil_Image/Real_Soil');
    }

    /**
     * Query real soil images from the Real_Soil store.
     * Supports filtering by Munsell code (e.g. "5YR 4/2") or Hue (e.g. "5YR").
     */
    public function getImages(Request $request)
    {
        $hue = trim((string) $request->query('hue', ''));
        $code = trim((string) $request->query('code', ''));
        $limit = max(1, min(50, (int) $request->query('limit', 15)));

        $cacheKey = 'real_soil_img_' . md5($hue . '|' . $code . '|' . $limit);

        $results = Cache::remember($cacheKey, 3600, function () use ($hue, $code, $limit) {
            return $this->findRealSoilImages($hue, $code, $limit);
        });

        return response()->json([
            'status' => 'success',
            'count'  => count($results),
            'data'   => $results,
        ]);
    }

    /**
     * Serve a real soil image file directly with caching headers.
     */
    public function serveFile(string $path)
    {
        // Sanitize and prevent directory traversal
        $cleanPath = str_replace(['..', "\0"], '', $path);
        $fullPath = $this->realSoilRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $cleanPath);

        if (!file_exists($fullPath) || is_dir($fullPath)) {
            return response()->json(['status' => 'error', 'message' => 'Image not found'], 404);
        }

        $mimeType = match (strtolower(pathinfo($fullPath, PATHINFO_EXTENSION))) {
            'png' => 'image/png',
            'jpeg', 'jpg' => 'image/jpeg',
            'webp' => 'image/webp',
            default => 'application/octet-stream',
        };

        return new BinaryFileResponse($fullPath, 200, [
            'Content-Type'  => $mimeType,
            'Cache-Control' => 'public, max-age=604800, immutable',
        ]);
    }

    /**
     * Find real soil images from the folder hierarchy.
     */
    protected function findRealSoilImages(string $hue, string $code, int $limit): array
    {
        if (!is_dir($this->realSoilRoot)) {
            return [];
        }

        $files = [];

        // 1. If exact Munsell code requested (e.g. "5YR 4/2" or "5YR 4-2")
        if (!empty($code)) {
            $parsed = $this->parseMunsellCode($code);
            if ($parsed) {
                $codeHue = $parsed['hue'];
                $codeVal = $parsed['value'];
                $codeChr = $parsed['chroma'];

                $targetDir = $this->realSoilRoot . DIRECTORY_SEPARATOR . $codeHue . DIRECTORY_SEPARATOR . $codeHue . ' ' . $codeVal . DIRECTORY_SEPARATOR . $codeChr;
                if (is_dir($targetDir)) {
                    $pattern = $targetDir . DIRECTORY_SEPARATOR . '*.{jpg,jpeg,png,JPG,JPEG,PNG}';
                    $matched = glob($pattern, GLOB_BRACE) ?: [];
                    foreach ($matched as $file) {
                        $files[] = [
                            'path' => $file,
                            'hue' => $codeHue,
                            'value' => $codeVal,
                            'chroma' => $codeChr,
                            'code' => "{$codeHue} {$codeVal}/{$codeChr}",
                        ];
                        if (count($files) >= $limit) {
                            break;
                        }
                    }
                }

                // If fewer than limit found, widen search to same hue
                if (count($files) < $limit) {
                    $hue = $codeHue;
                }
            }
        }

        // 2. If hue is specified and not 'All'
        if (empty($files) && !empty($hue) && $hue !== 'All') {
            $hueDir = $this->realSoilRoot . DIRECTORY_SEPARATOR . $hue;
            if (is_dir($hueDir)) {
                $pattern = $hueDir . DIRECTORY_SEPARATOR . '*' . DIRECTORY_SEPARATOR . '*' . DIRECTORY_SEPARATOR . '*.{jpg,jpeg,png,JPG,JPEG,PNG}';
                $matched = glob($pattern, GLOB_BRACE) ?: [];
                // Shuffle deterministically or sample evenly
                foreach ($matched as $file) {
                    $meta = $this->extractMetaFromPath($file);
                    $files[] = [
                        'path' => $file,
                        'hue' => $meta['hue'] ?: $hue,
                        'value' => $meta['value'] ?: '',
                        'chroma' => $meta['chroma'] ?: '',
                        'code' => $meta['code'] ?: $hue,
                    ];
                    if (count($files) >= $limit) {
                        break;
                    }
                }
            }
        }

        // 3. Fallback / "All": Sample across all available hues
        if (empty($files)) {
            $allHues = ['10YR', '7.5YR', '5YR', '2.5YR', '10R', '2.5Y'];
            $perHue = max(2, (int) ceil($limit / count($allHues)));

            foreach ($allHues as $h) {
                $hDir = $this->realSoilRoot . DIRECTORY_SEPARATOR . $h;
                if (is_dir($hDir)) {
                    $pattern = $hDir . DIRECTORY_SEPARATOR . '*' . DIRECTORY_SEPARATOR . '*' . DIRECTORY_SEPARATOR . '*.{jpg,jpeg,png,JPG,JPEG,PNG}';
                    $matched = glob($pattern, GLOB_BRACE) ?: [];
                    $count = 0;
                    foreach ($matched as $file) {
                        $meta = $this->extractMetaFromPath($file);
                        $files[] = [
                            'path' => $file,
                            'hue' => $meta['hue'] ?: $h,
                            'value' => $meta['value'] ?: '',
                            'chroma' => $meta['chroma'] ?: '',
                            'code' => $meta['code'] ?: $h,
                        ];
                        $count++;
                        if ($count >= $perHue) {
                            break;
                        }
                    }
                }
                if (count($files) >= $limit) {
                    break;
                }
            }
        }

        // Format results
        $output = [];
        foreach ($files as $idx => $item) {
            $relPath = ltrim(str_replace([$this->realSoilRoot, '\\'], ['', '/'], $item['path']), '/');
            $filename = basename($item['path']);
            $output[] = [
                'id'          => 'rs_' . md5($relPath),
                'url'         => request()->root() . '/api/real-soil/file/' . $relPath,
                'munsellCode' => $item['code'],
                'hue'         => $item['hue'],
                'value'       => $item['value'],
                'chroma'      => $item['chroma'],
                'filename'    => $filename,
            ];
        }

        return $output;
    }

    /**
     * Parse string like "5YR 4/2", "5YR 4-2", or "5YR4/2".
     */
    protected function parseMunsellCode(string $code): ?array
    {
        if (preg_match('/^([0-9\.]+[A-Z]+)\s*([0-9\.]+)\s*[\/\-]\s*([0-9\.]+)$/i', trim($code), $matches)) {
            return [
                'hue'    => strtoupper($matches[1]),
                'value'  => $matches[2],
                'chroma' => $matches[3],
            ];
        }
        return null;
    }

    /**
     * Extract metadata from directory structure.
     * e.g. .../Real_Soil/5YR/5YR 4/2/real_soil_...
     */
    protected function extractMetaFromPath(string $file): array
    {
        $parts = explode(DIRECTORY_SEPARATOR, $file);
        $count = count($parts);
        if ($count >= 3) {
            $chroma = $parts[$count - 2];
            $hueVal = $parts[$count - 3];
            $hue = $parts[$count - 4] ?? '';

            // If $hueVal is e.g. "5YR 4"
            $valParts = explode(' ', $hueVal);
            $value = $valParts[1] ?? '';

            return [
                'hue'    => $hue ?: ($valParts[0] ?? ''),
                'value'  => $value,
                'chroma' => $chroma,
                'code'   => ($hue && $value && $chroma) ? "{$hue} {$value}/{$chroma}" : '',
            ];
        }

        return ['hue' => '', 'value' => '', 'chroma' => '', 'code' => ''];
    }
}
