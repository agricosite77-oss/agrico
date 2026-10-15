<?php

namespace App\Services;

class SupabaseStorageService extends CloudStorageService
{
    // Inherits all upload, uploadFile, publicUrl, delete, and deleteDirectory methods
    // while natively supporting Cloudflare R2, Supabase, and S3.
}
