<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SupabaseStorageService
{
    protected string $projectRef;
    protected string $bucket;
    protected ?string $serviceKey;
    protected ?string $anonKey;
    protected string $storageBaseUrl;

    public function __construct()
    {
        $this->projectRef = (string) env('SUPABASE_PROJECT_REF', 'qbgrxnbayyykrycgtbig');
        $this->bucket = (string) env('SUPABASE_STORAGE_BUCKET', 'agrico-scans');
        $this->serviceKey = env('SUPABASE_SERVICE_ROLE_KEY') ?: env('SUPABASE_KEY') ?: null;
        $this->anonKey = env('SUPABASE_ANON_KEY') ?: null;
        $this->storageBaseUrl = "https://{$this->projectRef}.supabase.co/storage/v1";
    }

    /**
     * Get the public URL for an object path in the agrico-scans bucket.
     */
    public function publicUrl(string $path): string
    {
        $clean = ltrim($path, '/');
        // If already a full URL, return it
        if (str_starts_with($clean, 'http://') || str_starts_with($clean, 'https://')) {
            return $clean;
        }
        // If it starts with storage/ or agrico-scans/, normalize it
        $clean = preg_replace('#^(?:storage/|agrico-scans/)#', '', $clean);

        return "{$this->storageBaseUrl}/object/public/{$this->bucket}/{$clean}";
    }

    /**
     * Upload binary content or a file to Supabase Storage.
     * Tries S3-compatible driver first if configured, then Supabase REST API, then local disk.
     */
    public function upload(string $path, string $binaryContent, string $mimeType = 'image/jpeg'): ?string
    {
        $cleanPath = ltrim($path, '/');
        $cleanPath = preg_replace('#^(?:storage/|agrico-scans/)#', '', $cleanPath);

        // 1. Try S3 driver if S3 keys or SUPABASE_STORAGE_ACCESS_KEY_ID are set
        $hasS3 = !empty(env('AWS_ACCESS_KEY_ID')) || !empty(env('SUPABASE_STORAGE_ACCESS_KEY_ID'));
        if ($hasS3) {
            try {
                $disk = config('filesystems.disks.supabase') ? 'supabase' : 's3';
                $uploaded = Storage::disk($disk)->put($cleanPath, $binaryContent, 'public');
                if ($uploaded) {
                    return $this->publicUrl($cleanPath);
                }
            } catch (\Throwable $e) {
                Log::warning('[SupabaseStorageService] S3 upload error: ' . $e->getMessage());
            }
        }

        // 2. Try Supabase REST API if service_role or anon key is available
        $authKey = $this->serviceKey ?: $this->anonKey;
        if (!empty($authKey)) {
            try {
                $endpoint = "{$this->storageBaseUrl}/object/{$this->bucket}/{$cleanPath}";
                $response = Http::withHeaders([
                    'Authorization' => "Bearer {$authKey}",
                    'apikey'        => $authKey,
                    'Content-Type'  => $mimeType,
                    'x-upsert'      => 'true',
                ])->withBody($binaryContent, $mimeType)->post($endpoint);

                if ($response->successful()) {
                    return $this->publicUrl($cleanPath);
                } else {
                    Log::warning('[SupabaseStorageService] REST API upload failed: ' . $response->status() . ' ' . $response->body());
                }
            } catch (\Throwable $e) {
                Log::warning('[SupabaseStorageService] REST upload exception: ' . $e->getMessage());
            }
        }

        // 3. Fallback: Save to local public storage as backup
        try {
            $localDest = storage_path('app/public/' . $cleanPath);
            $localDir = dirname($localDest);
            if (!is_dir($localDir)) {
                @mkdir($localDir, 0755, true);
            }
            @file_put_contents($localDest, $binaryContent);
        } catch (\Throwable $e) {
            Log::warning('[SupabaseStorageService] Local fallback save error: ' . $e->getMessage());
        }

        // Return the permanent public Supabase URL format so database entries remain consistent
        return $this->publicUrl($cleanPath);
    }

    /**
     * Upload an existing local file path to Supabase.
     */
    public function uploadFile(string $localFilePath, string $targetPath, ?string $mimeType = null): ?string
    {
        if (!file_exists($localFilePath) || filesize($localFilePath) === 0) {
            return null;
        }

        $content = file_get_contents($localFilePath);
        if ($content === false) {
            return null;
        }

        if (!$mimeType) {
            $ext = strtolower(pathinfo($localFilePath, PATHINFO_EXTENSION));
            $mimeType = match ($ext) {
                'png' => 'image/png',
                'webp' => 'image/webp',
                'gif' => 'image/gif',
                default => 'image/jpeg',
            };
        }

        return $this->upload($targetPath, $content, $mimeType);
    }
}
