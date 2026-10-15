<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CloudStorageService
{
    protected ?string $activeDisk = null;

    public function __construct()
    {
        $this->activeDisk = $this->resolveActiveDisk();
    }

    /**
     * Determines the optimal storage disk based on environment variables.
     */
    protected function resolveActiveDisk(): string
    {
        // 1. Check if Cloudflare R2 credentials or endpoint are provided
        if (!empty(env('CLOUDFLARE_R2_ACCESS_KEY_ID')) || !empty(env('R2_ACCESS_KEY_ID')) || !empty(env('R2_ENDPOINT')) || !empty(env('CLOUDFLARE_R2_ENDPOINT'))) {
            return 'r2';
        }

        // 2. Check if S3 credentials are provided
        if (!empty(env('AWS_ACCESS_KEY_ID')) && !empty(env('AWS_SECRET_ACCESS_KEY'))) {
            return 's3';
        }

        // 3. Check if Supabase S3 credentials are provided
        if (!empty(env('SUPABASE_STORAGE_ACCESS_KEY_ID')) && !empty(env('SUPABASE_STORAGE_SECRET_ACCESS_KEY'))) {
            return 'supabase';
        }

        // 4. Default to filesystem default or public local disk
        $default = config('filesystems.default', 'public');
        if (isset(config('filesystems.disks')[$default])) {
            return $default;
        }

        return 'public';
    }

    /**
     * Resolves the public URL for a given relative storage path.
     */
    public function publicUrl(string $path): string
    {
        $clean = ltrim($path, '/');
        if (str_starts_with($clean, 'http://') || str_starts_with($clean, 'https://')) {
            return $clean;
        }

        $clean = preg_replace('#^(?:storage/|agrico-scans/)#', '', $clean);

        // If Cloudflare R2 has a dedicated public URL / custom domain
        $r2Url = env('CLOUDFLARE_R2_URL') ?: env('R2_URL');
        if ($this->activeDisk === 'r2' && !empty($r2Url)) {
            return rtrim($r2Url, '/') . '/' . $clean;
        }

        // Check if the configured disk can generate a URL
        try {
            if ($this->activeDisk && in_array($this->activeDisk, ['r2', 's3', 'supabase'])) {
                $diskUrl = Storage::disk($this->activeDisk)->url($clean);
                if (!empty($diskUrl) && !str_contains($diskUrl, 'localhost')) {
                    return $diskUrl;
                }
            }
        } catch (\Throwable $e) {
            // Fallthrough to standard public storage
        }

        // Fallback to local storage URL
        return asset('storage/' . $clean);
    }

    /**
     * Upload binary content to the cloud storage (or fallback).
     */
    public function upload(string $path, string $binaryContent, string $mimeType = 'image/jpeg'): ?string
    {
        $cleanPath = ltrim($path, '/');
        $cleanPath = preg_replace('#^(?:storage/|agrico-scans/)#', '', $cleanPath);

        // 1. Try Cloudflare R2 or S3 disk first
        if (in_array($this->activeDisk, ['r2', 's3', 'supabase'])) {
            try {
                $disk = Storage::disk($this->activeDisk);
                $uploaded = $disk->put($cleanPath, $binaryContent, [
                    'visibility'   => 'public',
                    'ContentType'  => $mimeType,
                ]);

                if ($uploaded) {
                    return $this->publicUrl($cleanPath);
                }
            } catch (\Throwable $e) {
                Log::warning("[CloudStorageService] {$this->activeDisk} upload failed: " . $e->getMessage());
            }
        }

        // 2. Try Supabase REST API if keys are present
        $projectRef = env('SUPABASE_PROJECT_REF', 'qbgrxnbayyykrycgtbig');
        $bucket     = env('SUPABASE_STORAGE_BUCKET', 'agrico-scans');
        $authKey    = env('SUPABASE_SERVICE_ROLE_KEY') ?: env('SUPABASE_KEY') ?: env('SUPABASE_ANON_KEY');

        if (!empty($authKey) && !empty($projectRef)) {
            try {
                $endpoint = "https://{$projectRef}.supabase.co/storage/v1/object/{$bucket}/{$cleanPath}";
                $response = Http::withHeaders([
                    'Authorization' => "Bearer {$authKey}",
                    'apikey'        => $authKey,
                    'Content-Type'  => $mimeType,
                    'x-upsert'      => 'true',
                ])->withBody($binaryContent, $mimeType)->post($endpoint);

                if ($response->successful()) {
                    return "https://{$projectRef}.supabase.co/storage/v1/object/public/{$bucket}/{$cleanPath}";
                }
            } catch (\Throwable $e) {
                Log::warning('[CloudStorageService] Supabase REST API upload failed: ' . $e->getMessage());
            }
        }

        // 3. Fallback: Save to local public storage disk
        try {
            $localDest = storage_path('app/public/' . $cleanPath);
            $localDir = dirname($localDest);
            if (!is_dir($localDir)) {
                @mkdir($localDir, 0755, true);
            }
            @file_put_contents($localDest, $binaryContent);
        } catch (\Throwable $e) {
            Log::warning('[CloudStorageService] Local fallback save error: ' . $e->getMessage());
        }

        return $this->publicUrl($cleanPath);
    }

    /**
     * Upload a local file path.
     */
    public function uploadFile(string $localFilePath, string $targetPath, ?string $mimeType = null): ?string
    {
        if (!file_exists($localFilePath) || filesize($localFilePath) === 0) {
            return null;
        }

        $content = @file_get_contents($localFilePath);
        if ($content === false) {
            return null;
        }

        if (!$mimeType) {
            $ext = strtolower(pathinfo($targetPath, PATHINFO_EXTENSION));
            $mimeType = match ($ext) {
                'png'  => 'image/png',
                'webp' => 'image/webp',
                'gif'  => 'image/gif',
                default => 'image/jpeg',
            };
        }

        return $this->upload($targetPath, $content, $mimeType);
    }

    /**
     * Delete an object (useful for user account deletion / GDPR / DPA privacy).
     */
    public function delete(string $path): bool
    {
        $cleanPath = ltrim($path, '/');
        $cleanPath = preg_replace('#^(?:storage/|agrico-scans/)#', '', $cleanPath);

        if (in_array($this->activeDisk, ['r2', 's3', 'supabase'])) {
            try {
                return Storage::disk($this->activeDisk)->delete($cleanPath);
            } catch (\Throwable $e) {
                Log::warning("[CloudStorageService] Delete error: " . $e->getMessage());
            }
        }

        $localPath = storage_path('app/public/' . $cleanPath);
        if (file_exists($localPath)) {
            return @unlink($localPath);
        }

        return true;
    }

    /**
     * Delete a directory of objects (e.g. scans/{user_id}/ on account deletion).
     */
    public function deleteDirectory(string $directory): bool
    {
        $cleanDir = trim(preg_replace('#^(?:storage/|agrico-scans/)#', '', $directory), '/');

        if (in_array($this->activeDisk, ['r2', 's3', 'supabase'])) {
            try {
                return Storage::disk($this->activeDisk)->deleteDirectory($cleanDir);
            } catch (\Throwable $e) {
                Log::warning("[CloudStorageService] Delete directory error: " . $e->getMessage());
            }
        }

        $localDir = storage_path('app/public/' . $cleanDir);
        if (is_dir($localDir)) {
            // Clean local files
            $files = glob($localDir . '/*');
            foreach ($files as $file) {
                if (is_file($file)) @unlink($file);
            }
            @rmdir($localDir);
        }

        return true;
    }
}
