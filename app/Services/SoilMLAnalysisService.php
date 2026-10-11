<?php

namespace App\Services;

use App\Models\LocationMapping;
use App\Models\MunsellColor;
use App\Models\Soil;
use App\Models\SoilScan;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;

class SoilMLAnalysisService
{
    public function __construct(
        protected SoilScanService        $fallbackService,
        protected SoilValidationService  $validator
    ) {}

    /**
     * Analyze soil image via ML Model (Option B) with automatic fallback to Option A (SoilScanService).
     *
     * Stage 1's ML gate (soil_gate.py, trained on Fake_Soil/ + Real_Soil/) is the sole
     * soil/not-soil gatekeeper. Tier A–D (SoilValidationService) has been removed from
     * this path — the classifier alone decides acceptance or rejection, and the controller
     * enforces the soft-reject (422) when label !== 'real_soil'.
     *
     * SoilValidationService is retained in the constructor and file but is no longer called
     * here. Remove it entirely once Stage 1 is confirmed reliable end-to-end.
     *
     * @param mixed $imageInput UploadedFile, file path, or base64 string
     * @return array Analysis metrics
     */
    public function analyzeImage(mixed $imageInput, ?string $scanId = null): array
    {
        // Tier A–D removed — Stage 1's ML gate (soil_gate.py) is now the sole gatekeeper.
        // $this->validator->validate($imageInput);

        $mlEndpoint = config('services.soil_ml.endpoint', env('SOIL_ML_API_URL'));

        if ($mlEndpoint && is_string($mlEndpoint) && filter_var($mlEndpoint, FILTER_VALIDATE_URL)) {
            try {
                $mlResult = $this->callMlInferenceApi($mlEndpoint, $imageInput);
                if ($mlResult && isset($mlResult['success']) && $mlResult['success'] === true) {
                    $mlResult['engine'] = 'Option B (TFLite / Machine Learning Model)';
                    return $mlResult;
                }
            } catch (\Throwable $e) {
                Log::warning('Soil ML Inference API unavailable or failed. Falling back to Python Microservice / Option A. Error: ' . $e->getMessage());
            }
        }

        $scanId = $scanId ?: (string) \Illuminate\Support\Str::uuid();

        // Try local Python Microservice Pipeline (non-blocking; result read from scan_result_{id}.json)
        $this->callPythonMicroservice($imageInput, $scanId);

        // Return a sentinel so the controller knows polling is needed
        return ['success' => true, 'async' => true, 'scan_id' => $scanId];
    }

    /**
     * Resolve the scan status from Python result, falling back to dominant region or 'unclassified'.
     */
    public function resolveStatus(array $pythonResult): string
    {
        $rawStatus = $pythonResult['status'] ?? ($pythonResult['regions'][0]['status'] ?? null);
        return (!empty($rawStatus) && is_string($rawStatus)) ? $rawStatus : 'unclassified';
    }

    /**
     * Build the enriched analysis payload from Python result and base fallback analysis.
     */
    public function buildAnalysis(array $pythonResult, array $baseAnalysis = []): array
    {
        $analysis = $baseAnalysis;

        if (isset($pythonResult['label'])) {
            $analysis['label'] = $pythonResult['label'];
            $analysis['classification'] = $pythonResult['label'];
        }
        $deltaE = $pythonResult['munsell']['color_delta_e'] ?? ($pythonResult['color_delta_e'] ?? ($pythonResult['delta_e'] ?? null));
        if ($deltaE === null && !empty($pythonResult['regions'][0]['color_delta_e'])) {
            $deltaE = $pythonResult['regions'][0]['color_delta_e'];
        }
        if ($deltaE !== null && is_numeric($deltaE) && (float)$deltaE >= 0) {
            $nearScore = round(max(65.0, min(90.0, 90.0 - ((float)$deltaE * 1.25))), 1);
            $analysis['confidence'] = $nearScore;
            $analysis['confidence_formatted'] = $nearScore . '%';
            $analysis['near_match'] = $nearScore . '%';
        } elseif (isset($pythonResult['confidence']) && (float)$pythonResult['confidence'] <= 1.0) {
            $nearScore = 84.0;
            $analysis['confidence'] = $nearScore;
            $analysis['confidence_formatted'] = $nearScore . '%';
            $analysis['near_match'] = $nearScore . '%';
        } elseif (isset($pythonResult['confidence'])) {
            $analysis['confidence'] = round($pythonResult['confidence'], 1);
            $analysis['confidence_formatted'] = $analysis['confidence'] . '%';
        }
        if (isset($pythonResult['munsell']['notation'])) {
            $analysis['munsell_code'] = $pythonResult['munsell']['notation'];
            $analysis['munsell_details'] = $pythonResult['munsell'];
        }
        if (isset($pythonResult['image_id'])) {
            $analysis['dataset_image_id'] = $pythonResult['image_id'];
        }
        if (isset($pythonResult['stored_path'])) {
            $analysis['stored_dataset_path'] = $pythonResult['stored_path'];
        }
        if (isset($pythonResult['stage2_comparison'])) {
            $analysis['stage2_comparison'] = $pythonResult['stage2_comparison'];
        }
        if (isset($pythonResult['stage2_normalized_path'])) {
            $analysis['stage2_normalized_path'] = $pythonResult['stage2_normalized_path'];
        }
        if (isset($pythonResult['stage2_normalized_color'])) {
            $analysis['stage2_normalized_color'] = $pythonResult['stage2_normalized_color'];
        }
        if (isset($pythonResult['stage2_mode'])) {
            $analysis['stage2_mode'] = $pythonResult['stage2_mode'];
            $analysis['stage2_engine'] = $pythonResult['stage2_mode'];
        }
        $analysis['status'] = $this->resolveStatus($pythonResult);

        // ── regions[] — new multi-color result set (Stages 3–6) ──────────────
        // If the Python script emits a non-empty regions array, use it directly.
        // Backward compat: if absent (old script version), synthesize a single
        // region from the flat munsell key so the API shape is always consistent.
        if (!empty($pythonResult['regions']) && is_array($pythonResult['regions'])) {
            $analysis['regions'] = array_map(function ($r) use ($analysis) {
                $munsell = $r['munsell'] ?? ($r['notation'] ?? '');
                $hex = null;
                if (!empty($r['rgb']) && is_array($r['rgb']) && count($r['rgb']) >= 3) {
                    $hex = sprintf('#%02x%02x%02x', $r['rgb'][0], $r['rgb'][1], $r['rgb'][2]);
                }
                if (!$hex && $munsell) {
                    $mc = \App\Models\MunsellColor::where('full_code', $munsell)->first();
                    $hex = $mc?->hex_code;
                }
                $r['hex'] = $hex ?: ($analysis['hex'] ?? '#8F694E');
                $proportion = (float)($r['proportion'] ?? 1.0);
                $r['percent'] = round($proportion * 100, 1);

                $rDeltaE = isset($r['color_delta_e']) && is_numeric($r['color_delta_e']) ? (float)$r['color_delta_e'] : 3.0;
                $regNearScore = round(max(65.0, min(90.0, 90.0 - ($rDeltaE * 1.25))), 1);
                $r['confidence'] = $regNearScore;
                $r['confidence_formatted'] = $regNearScore . '%';
                $r['near_match'] = $regNearScore . '%';

                return $r;
            }, $pythonResult['regions']);
        } elseif (isset($pythonResult['munsell']['notation'])) {
            // Older script — wrap flat munsell result as a single legacy region
            $legacyDeltaE = isset($pythonResult['munsell']['color_delta_e']) && is_numeric($pythonResult['munsell']['color_delta_e']) ? (float)$pythonResult['munsell']['color_delta_e'] : 3.0;
            $legacyNearScore = round(max(65.0, min(90.0, 90.0 - ($legacyDeltaE * 1.25))), 1);
            $analysis['regions'] = [[
                'munsell'                 => $pythonResult['munsell']['notation'],
                'label'                   => $pythonResult['label'] ?? 'unknown',
                'proportion'              => 1.0,
                'percent'                 => 100.0,
                'hex'                     => $analysis['hex'] ?? '#8F694E',
                'region_thumbnail'        => '',
                'verification_confidence' => null,
                'status'                  => 'legacy',
                'color_delta_e'           => $pythonResult['munsell']['color_delta_e'] ?? null,
                'gate_confidence'         => $pythonResult['confidence'] ?? null,
                'confidence'              => $legacyNearScore,
                'confidence_formatted'    => $legacyNearScore . '%',
                'near_match'              => $legacyNearScore . '%',
            ]];
        } else {
            $analysis['regions'] = [];
        }

        return $analysis;
    }

    /**
     * Call external TFLite / Python ML Inference REST API.
     */
    protected function callMlInferenceApi(string $endpoint, mixed $imageInput): ?array
    {
        $response = Http::timeout(4)
            ->attach('image', is_string($imageInput) ? $imageInput : file_get_contents($imageInput->getRealPath()), 'soil_sample.jpg')
            ->post($endpoint);

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }

    /**
     * Bridge call to python/colorimetry_service.py microservice script.
     *
     * Runs Python non-blocking (fire-and-forget). Python writes progress to
     * /tmp/scan_progress_{scanId}.json and the final result to
     * /tmp/scan_result_{scanId}.json. The controller reads those files via
     * the scanProgress() endpoint instead of waiting for stdout.
     */
    public function callPythonMicroservice(mixed $imageInput, ?string $scanId = null): void
    {
        $scriptPath = base_path('python/colorimetry_service.py');
        if (!file_exists($scriptPath)) {
            return;
        }

        $tempFile = null;
        try {
            $inputArg = '';
            if (is_string($imageInput)) {
                if (file_exists($imageInput)) {
                    $inputArg = $imageInput;
                } elseif (preg_match('/^data:image\/(\w+);base64,/', $imageInput, $m)) {
                    $ext = $m[1] === 'jpeg' ? 'jpg' : $m[1];
                    $data = base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $imageInput));
                    $tempFile = tempnam(sys_get_temp_dir(), 'soil_scan_') . '.' . $ext;
                    file_put_contents($tempFile, $data);
                    $inputArg = $tempFile;
                }
            } elseif ($imageInput instanceof \Illuminate\Http\UploadedFile) {
                $ext = $imageInput->getClientOriginalExtension() ?: 'jpg';
                $safeScanId = $scanId ? preg_replace('/[^a-zA-Z0-9_\-]/', '', $scanId) : uniqid('scan_');
                $tempFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'soil_upload_' . $safeScanId . '.' . $ext;
                @copy($imageInput->getRealPath(), $tempFile);
                $inputArg = $tempFile;
            }

            if (empty($inputArg) || !file_exists($inputArg)) {
                return;
            }

            $args = [escapeshellarg($inputArg)];
            if ($scanId) {
                $args[] = '--scan-id';
                $args[] = escapeshellarg($scanId);
            }

            $runner = new PythonProcessRunner();
            $runner->runDetached($scriptPath, $args, $scanId);

        } catch (\Throwable $e) {
            Log::info('Python colorimetry bridge detached-launch skipped: ' . $e->getMessage());
        }
        // Note: $tempFile cannot be deleted here since Python is still running.
        // The OS will eventually reclaim it; for long-running servers consider
        // a scheduled cleanup of /tmp/soil_scan_*.{jpg,png} older than 1 hour.
    }

    /**
     * Persist a Python scan result into Soil + SoilScan tables and return the
     * enriched response array.
     *
     * @param array $pythonResult Raw result dictionary from Python / JSON file
     * @param string $scanId Scan UUID
     * @param string|null $userId Authenticated User ID (passed from caller)
     * @param string $platform 'website' or 'mobile'
     * @param array $locationData Optional geolocation metadata ['latitude', 'longitude', 'barangay', 'location_name']
     * @return array Enriched analysis array or soft-rejection payload
     * @throws AuthenticationException
     */
    public function persistScanResult(
        array $pythonResult,
        string $scanId,
        ?string $userId = null,
        string $platform = 'website',
        array $locationData = []
    ): array {
        // Enforce authentication rule: mobile must never silently fall back to admin
        if ($platform === 'mobile') {
            if (empty($userId) || !User::where('user_id', $userId)->exists()) {
                throw new AuthenticationException('Unauthenticated: A valid user account is required to record mobile scans.');
            }
        } else {
            // For web, fallback to ADM-001 if unauthenticated/missing
            if (empty($userId) || !User::where('user_id', $userId)->exists()) {
                $userId = User::first()?->user_id ?? 'ADM-001';
            }
        }

        // Soft-reject contract: non-soil samples are rejected cleanly
        if (isset($pythonResult['label']) && $pythonResult['label'] !== 'real_soil') {
            return [
                'rejected' => true,
                'success'  => false,
                'error'    => 'NON_SOIL_DETECTED',
                'message'  => 'No valid soil sample detected. Please align the viewfinder over a clear, well-lit soil sample and try again.',
            ];
        }

        // Build the enriched analysis array
        $engine = 'Python Microservice (6-Stage Classification & Munsell Engine)';
        $baseAnalysis = [];
        if (isset($pythonResult['rgb']) && is_array($pythonResult['rgb'])) {
            $rawRgb = $pythonResult['rgb'];
            $normalizedRgb = [
                'r' => (int)($rawRgb['r'] ?? ($rawRgb[0] ?? 0)),
                'g' => (int)($rawRgb['g'] ?? ($rawRgb[1] ?? 0)),
                'b' => (int)($rawRgb['b'] ?? ($rawRgb[2] ?? 0)),
            ];
            $baseAnalysis = $this->fallbackService->analyzeImageFromRgb($normalizedRgb, $engine);
        }
        $analysis = $this->buildAnalysis($pythonResult, $baseAnalysis);

        // Strip appended codes from soil name
        $pureSoilName = trim(preg_replace('/\s*\([A-Za-z0-9\/\.\s]+\)$/i', '', $analysis['soil_name'] ?? ''));
        if (empty($pureSoilName) && !empty($analysis['color_name'])) {
            $pureSoilName = trim(preg_replace('/\s*\([A-Za-z0-9\/\.\s]+\)$/i', '', $analysis['color_name']));
        }
        $analysis['soil_name'] = $pureSoilName ?: 'Scanned Soil';

        $regions     = $analysis['regions'] ?? [];
        $isMultiLayer = count($regions) > 1;
        $createdScans = [];
        $soilScan     = null;
        $soil         = null;

        // Resolve or verify public web-accessible path for this scan
        $publicScansDir = storage_path('app/public/scans');
        if (!is_dir($publicScansDir)) {
            @mkdir($publicScansDir, 0755, true);
        }

        $scanJpgName = "scans/scan_{$scanId}.jpg";
        $fullPublicJpg = storage_path('app/public/' . $scanJpgName);
        $normJpgName = "scans/scan_{$scanId}_normalized.jpg";
        $fullNormJpg = storage_path('app/public/' . $normJpgName);

        // Check for candidate Stage 2 normalized image path
        $candidateNormPath = $pythonResult['stage2_normalized_path']
            ?? $analysis['stage2_normalized_path']
            ?? null;

        if (!file_exists($fullNormJpg) && !empty($candidateNormPath) && file_exists($candidateNormPath)) {
            @copy($candidateNormPath, $fullNormJpg);
        }

        $publicScanRelativePath = null;
        if (file_exists($fullNormJpg)) {
            @copy($fullNormJpg, $fullPublicJpg);
            $publicScanRelativePath = 'storage/' . $normJpgName;
        } elseif (!empty($candidateNormPath) && file_exists($candidateNormPath)) {
            @copy($candidateNormPath, $fullPublicJpg);
            @copy($candidateNormPath, $fullNormJpg);
            $publicScanRelativePath = 'storage/' . $normJpgName;
        } elseif (file_exists($fullPublicJpg)) {
            $publicScanRelativePath = 'storage/' . $scanJpgName;
        } elseif (!empty($pythonResult['stored_path']) && file_exists($pythonResult['stored_path'])) {
            @copy($pythonResult['stored_path'], $fullPublicJpg);
            $publicScanRelativePath = 'storage/' . $scanJpgName;
        } else {
            $publicScanRelativePath = 'storage/' . $scanJpgName;
        }

        $disk = env('FILESYSTEM_DISK', 'public');
        if ($disk === 's3') {
            try {
                if (file_exists($fullNormJpg)) {
                    $uploadedNorm = Storage::disk('s3')->put($normJpgName, file_get_contents($fullNormJpg), 'public');
                    if ($uploadedNorm) {
                        $publicScanRelativePath = Storage::disk('s3')->url($normJpgName);
                    }
                } elseif (file_exists($fullPublicJpg)) {
                    $uploadedScan = Storage::disk('s3')->put($scanJpgName, file_get_contents($fullPublicJpg), 'public');
                    if ($uploadedScan) {
                        $publicScanRelativePath = Storage::disk('s3')->url($scanJpgName);
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('[SoilMLAnalysisService] S3 scan upload failed: ' . $e->getMessage());
            }
        }

        $saveRegionImage = function ($thumbBase64, $idx) use ($scanId, $publicScanRelativePath, $disk) {
            if (empty($thumbBase64)) {
                return $publicScanRelativePath;
            }
            if (str_starts_with($thumbBase64, 'storage/') || str_starts_with($thumbBase64, 'http')) {
                return $thumbBase64;
            }
            try {
                $cleaned = preg_replace('/^data:image\/\w+;base64,/', '', $thumbBase64);
                $rawBin = base64_decode($cleaned, true);
                if ($rawBin !== false && strlen($rawBin) > 10) {
                    $ext = str_starts_with($cleaned, 'iVBOR') ? 'png' : 'jpg';
                    $rFileName = "scans/region_{$scanId}_{$idx}.{$ext}";
                    if ($disk === 's3') {
                        try {
                            Storage::disk('s3')->put($rFileName, $rawBin, 'public');
                            return Storage::disk('s3')->url($rFileName);
                        } catch (\Throwable $e) {
                            Log::warning('[SoilMLAnalysisService] S3 region upload failed: ' . $e->getMessage());
                        }
                    }
                    $destPath = storage_path('app/public/' . $rFileName);
                    $destDir = dirname($destPath);
                    if (!is_dir($destDir)) {
                        @mkdir($destDir, 0755, true);
                    }
                    @file_put_contents($destPath, $rawBin);
                    return 'storage/' . $rFileName;
                }
            } catch (\Throwable $e) {
                // Fallback to dominant image
            }
            return $publicScanRelativePath;
        };

        if ($isMultiLayer) {
            $subScansCreated = [];
            for ($i = count($regions) - 1; $i >= 1; $i--) {
                $r = $regions[$i];
                $mCode = $r['munsell'] ?? ($r['munsell_code'] ?? ($r['notation'] ?? null));
                if (!$mCode) continue;
                $mColor = MunsellColor::where('full_code', $mCode)->first();
                $layerName = $mColor?->color_name ?? ($r['label'] ?? 'Soil Layer');
                $layerHue  = $mColor?->hue ?? (explode(' ', $mCode)[0] ?? '10YR');
                $layerColorId = $mColor?->munsell_color_id ?? null;
                $layerDesc = $mColor?->description ?? "{$mCode} - {$layerName}";
                $pctVal  = $r['percent'] ?? (!empty($r['proportion']) ? round($r['proportion'] * 100, 1) : null);
                $rDeltaE = isset($r['color_delta_e']) && is_numeric($r['color_delta_e']) ? (float)$r['color_delta_e'] : (isset($analysis['color_delta_e']) ? (float)$analysis['color_delta_e'] : (isset($analysis['delta_e']) ? (float)$analysis['delta_e'] : 3.0));
                $confVal = round(max(88.5, min(99.6, 99.4 - ($rDeltaE * 0.45))), 1) . '%';
                $layerSoil = Soil::firstOrCreate(
                    ['full_code' => $mCode],
                    ['soil_name' => $layerName, 'hue' => $layerHue, 'full_code' => $mCode,
                     'description' => $layerDesc, 'status' => 'Active', 'scan_count' => 0,
                     'color_id' => $layerColorId, 'admin_id' => $userId]
                );
                $layerSoil->increment('scan_count');
                $note = "Determined Soil: {$layerSoil->effective_soil_name} ({$mCode})" . ($pctVal ? " - Soil Percentage: {$pctVal}%" : "") . " - Confidence: {$confVal}";
                $rImgPath = $saveRegionImage($r['region_thumbnail'] ?? null, $i);
                $subScan = SoilScan::create([
                    'user_id' => $userId, 'soil_id' => $layerSoil->soil_id,
                    'platform' => $platform, 'scan_note' => $note,
                    'regions_data' => [$r], 'scan_date' => now(),
                    'scan_image' => $rImgPath,
                ]);
                $subScansCreated[$i] = [
                    'scan_id' => $subScan->soil_scan_id,
                    'scan_seq' => SoilScan::where('soil_id', $layerSoil->soil_id)->count(),
                    'name' => $layerSoil->effective_soil_name, 'color_name' => $layerSoil->effective_soil_name,
                    'code' => $mCode, 'hue' => $layerSoil->hue ?? $layerHue,
                    'hex' => $layerSoil->effective_hex_color ?? ($r['hex'] ?? '#8F694E'),
                    'desc' => $layerSoil->effective_description, 'organic' => $layerSoil->effective_organic_matter,
                    'drainage' => $layerSoil->effective_soil_type, 'soil_type' => $layerSoil->effective_soil_type,
                    'confidence' => $confVal, 'percent' => $pctVal, 'regions' => [$r],
                    'image' => $rImgPath ? asset($rImgPath) : ($publicScanRelativePath ? asset($publicScanRelativePath) : null),
                    'scan_image' => $rImgPath ? asset($rImgPath) : ($publicScanRelativePath ? asset($publicScanRelativePath) : null),
                ];
            }
            $domR    = $regions[0];
            $domCode = $domR['munsell'] ?? ($domR['munsell_code'] ?? ($analysis['munsell_code'] ?? '10YR 5/3'));
            $domMColor = MunsellColor::where('full_code', $domCode)->first();
            $domName = $domMColor?->color_name ?? ($analysis['soil_name'] ?? 'Dominant Soil Layer');
            $domHue  = $domMColor?->hue ?? ($analysis['hue'] ?? (explode(' ', $domCode)[0] ?? '10YR'));
            $domColorId = $domMColor?->munsell_color_id ?? ($analysis['color_id'] ?? null);
            $domDesc = $domMColor?->description ?? ($analysis['description'] ?? "{$domCode} - {$domName}");
            $domPct  = $domR['percent'] ?? (!empty($domR['proportion']) ? round($domR['proportion'] * 100, 1) : null);
            $domDeltaE = isset($domR['color_delta_e']) && is_numeric($domR['color_delta_e']) ? (float)$domR['color_delta_e'] : (isset($analysis['color_delta_e']) ? (float)$analysis['color_delta_e'] : (isset($analysis['delta_e']) ? (float)$analysis['delta_e'] : 3.0));
            $domConf = round(max(88.5, min(99.6, 99.4 - ($domDeltaE * 0.45))), 1) . '%';
            $soil = Soil::firstOrCreate(
                ['full_code' => $domCode],
                ['soil_name' => $domName, 'hue' => $domHue, 'full_code' => $domCode,
                 'description' => $domDesc, 'status' => 'Active', 'scan_count' => 0,
                 'color_id' => $domColorId, 'admin_id' => $userId]
            );
            $soil->increment('scan_count');
            $analysis['description']    = $soil->effective_description;
            $analysis['soil_name']      = $soil->effective_soil_name;
            $analysis['organic_matter'] = $soil->effective_organic_matter;
            $analysis['soil_type']      = $soil->effective_soil_type;
            $domImgPath = $saveRegionImage($domR['region_thumbnail'] ?? null, 0);
            $dominantScanImage = $publicScanRelativePath ?: ($domImgPath ?: null);
            $soilScan = SoilScan::create([
                'user_id' => $userId, 'soil_id' => $soil->soil_id, 'platform' => $platform,
                'scan_note' => "Determined Soil: {$soil->effective_soil_name} ({$domCode})" . ($domPct ? " - Soil Percentage: {$domPct}%" : "") . " - Confidence: {$domConf}",
                'regions_data' => [$domR], 'scan_date' => now(),
                'scan_image' => $dominantScanImage,
            ]);
            $createdScans = [[
                'scan_id' => $soilScan->soil_scan_id,
                'scan_seq' => SoilScan::where('soil_id', $soil->soil_id)->count(),
                'name' => $soil->effective_soil_name, 'color_name' => $soil->effective_soil_name,
                'code' => $domCode, 'hue' => $soil->hue ?? $domHue,
                'hex' => $soil->effective_hex_color ?? ($domR['hex'] ?? '#8F694E'),
                'desc' => $soil->effective_description, 'organic' => $soil->effective_organic_matter,
                'drainage' => $soil->effective_soil_type, 'soil_type' => $soil->effective_soil_type,
                'confidence' => $domConf, 'percent' => $domPct, 'regions' => [$domR],
                'image' => $dominantScanImage ? asset($dominantScanImage) : null,
                'scan_image' => $dominantScanImage ? asset($dominantScanImage) : null,
            ]];
            for ($i = 1; $i < count($regions); $i++) {
                if (isset($subScansCreated[$i])) {
                    $createdScans[] = $subScansCreated[$i];
                }
            }
        } else {
            // Single-layer scan
            $soil = Soil::firstOrCreate(
                ['full_code' => $analysis['munsell_code']],
                ['soil_name' => $analysis['soil_name'], 'hue' => $analysis['hue'],
                 'full_code' => $analysis['munsell_code'], 'description' => $analysis['description'],
                 'status' => 'Active', 'scan_count' => 0,
                 'color_id' => $analysis['color_id'], 'admin_id' => $userId]
            );
            $soil->increment('scan_count');
            $analysis['description']    = $soil->effective_description;
            $analysis['soil_name']      = $soil->effective_soil_name;
            $analysis['organic_matter'] = $soil->effective_organic_matter;
            $analysis['soil_type']      = $soil->effective_soil_type;
            $soilScan = SoilScan::create([
                'user_id' => $userId, 'soil_id' => $soil->soil_id, 'platform' => $platform,
                'scan_note' => "Determined Soil: {$analysis['soil_name']} ({$analysis['munsell_code']}) - Confidence: {$analysis['confidence_formatted']}",
                'regions_data' => $regions, 'scan_date' => now(),
                'scan_image' => $publicScanRelativePath,
            ]);
            $createdScans = [[
                'scan_id' => $soilScan->soil_scan_id,
                'scan_seq' => SoilScan::where('soil_id', $soil->soil_id)->count(),
                'name' => $soil->effective_soil_name, 'color_name' => $soil->effective_soil_name,
                'code' => $analysis['munsell_code'], 'hue' => $soil->hue ?? $analysis['hue'],
                'hex' => $soil->effective_hex_color ?? ($analysis['hex'] ?? '#8F694E'),
                'desc' => $soil->effective_description, 'organic' => $soil->effective_organic_matter,
                'drainage' => $soil->effective_soil_type, 'soil_type' => $soil->effective_soil_type,
                'confidence' => $analysis['confidence_formatted'] ?? '97.4%',
                'percent' => 100, 'regions' => $regions,
                'image' => $publicScanRelativePath ? asset($publicScanRelativePath) : null,
                'scan_image' => $publicScanRelativePath ? asset($publicScanRelativePath) : null,
            ]];
        }

        SoilScan::enforceLimit(300);

        // Optional LocationMapping persistence
        if (!empty($locationData['latitude']) && !empty($locationData['longitude']) && $soilScan && $soil) {
            try {
                $lat = (float) $locationData['latitude'];
                $lng = (float) $locationData['longitude'];
                $barangay = trim((string) ($locationData['barangay'] ?? ($locationData['location_name'] ?? '')));
                if (empty($barangay) || $barangay === 'Lal-lo Overall') {
                    $barangay = LocationMapping::resolveNearestBarangay($lat, $lng);
                }

                LocationMapping::create([
                    'soil_scan_id' => $soilScan->soil_scan_id,
                    'soil_id'      => $soil->soil_id,
                    'latitude'     => $lat,
                    'longitude'    => $lng,
                    'barangay'     => $barangay,
                    'timestamp'    => now(),
                    'status'       => 'Active',
                ]);
            } catch (\Throwable $e) {
                Log::warning('[SoilMLAnalysisService] Failed to create LocationMapping: ' . $e->getMessage());
            }
        }

        $cacheBuster = '?t=' . time();
        $normalizedUrl = $publicScanRelativePath ? (asset($publicScanRelativePath) . $cacheBuster) : null;

        $baJpgName = "scans/scan_{$scanId}_before_after.jpg";
        $fullBaJpg = storage_path('app/public/' . $baJpgName);
        $candidateBaPath = $pythonResult['before_after_image_path'] ?? null;
        if (!file_exists($fullBaJpg) && !empty($candidateBaPath) && file_exists($candidateBaPath)) {
            @copy($candidateBaPath, $fullBaJpg);
        }

        $rawJpgName = "scans/scan_{$scanId}_raw.jpg";
        $fullRawJpg = storage_path('app/public/' . $rawJpgName);

        if (!file_exists($fullBaJpg) && file_exists($fullNormJpg) && file_exists($fullRawJpg) && extension_loaded('gd')) {
            try {
                $rawIm = @imagecreatefromstring(file_get_contents($fullRawJpg));
                $normIm = @imagecreatefromstring(file_get_contents($fullNormJpg));
                if ($rawIm && $normIm) {
                    $rw = imagesx($rawIm);
                    $rh = imagesy($rawIm);
                    if ($rw > 1000) {
                        $scale = 1000 / $rw;
                        $nw = 1000;
                        $nh = (int)($rh * $scale);
                        $rResized = imagecreatetruecolor($nw, $nh);
                        imagecopyresampled($rResized, $rawIm, 0, 0, 0, 0, $nw, $nh, $rw, $rh);
                        imagedestroy($rawIm);
                        $rawIm = $rResized;
                        $rw = $nw;
                        $rh = $nh;
                    }
                    $nResized = imagecreatetruecolor($rw, $rh);
                    imagecopyresampled($nResized, $normIm, 0, 0, 0, 0, $rw, $rh, imagesx($normIm), imagesy($normIm));
                    imagedestroy($normIm);
                    $normIm = $nResized;

                    $barH = max(28, (int)($rh * 0.08));
                    $canvas = imagecreatetruecolor($rw * 2, $rh + $barH);
                    $bg = imagecolorallocate($canvas, 18, 30, 24);
                    imagefilledrectangle($canvas, 0, 0, $rw * 2, $rh + $barH, $bg);
                    imagecopy($canvas, $rawIm, 0, $barH, 0, 0, $rw, $rh);
                    imagecopy($canvas, $normIm, $rw, $barH, 0, 0, $rw, $rh);
                    $colorBefore = imagecolorallocate($canvas, 255, 200, 100);
                    $colorAfter = imagecolorallocate($canvas, 142, 242, 178);
                    imagestring($canvas, 4, 10, (int)($barH * 0.2), "BEFORE", $colorBefore);
                    $munsellCode = $analysis['munsell_code'] ?? '';
                    imagestring($canvas, 4, $rw + 10, (int)($barH * 0.2), "AFTER (NORMALIZED) " . $munsellCode, $colorAfter);

                    // Draw green ROI box on the BEFORE side (left half)
                    $targetBox = $regions[0]['bounding_box'] ?? $regions[0]['box'] ?? null;
                    if (!$targetBox) {
                        $targetBox = ['x' => 0.25, 'y' => 0.25, 'w' => 0.5, 'h' => 0.5];
                    }
                    if ($targetBox) {
                        $bx = isset($targetBox['w']) ? (int)($targetBox['x'] * $rw) : (int)($targetBox['x'] ?? (0.25 * $rw));
                        $by = isset($targetBox['h']) ? (int)($barH + ($targetBox['y'] * $rh)) : (int)($barH + ($targetBox['y'] ?? (0.25 * $rh)));
                        $bw = isset($targetBox['w']) ? (int)($targetBox['w'] * $rw) : (int)($targetBox['width'] ?? ($rw * 0.5));
                        $bh = isset($targetBox['h']) ? (int)($targetBox['h'] * $rh) : (int)($targetBox['height'] ?? ($rh * 0.5));
                        $bx = max(0, min($rw - 1, $bx));
                        $by = max($barH, min($rh + $barH - 1, $by));
                        $bw = max(10, min($rw - $bx, $bw));
                        $bh = max(10, min($rh + $barH - $by, $bh));

                        $greenColor = imagecolorallocate($canvas, 74, 222, 128);
                        $darkColor = imagecolorallocate($canvas, 0, 0, 0);
                        imagesetthickness($canvas, 4);
                        imagerectangle($canvas, $bx - 1, $by - 1, $bx + $bw + 1, $by + $bh + 1, $darkColor);
                        imagesetthickness($canvas, 3);
                        imagerectangle($canvas, $bx, $by, $bx + $bw, $by + $bh, $greenColor);
                    }

                    imagejpeg($canvas, $fullBaJpg, 92);
                    imagedestroy($canvas);
                    imagedestroy($rawIm);
                    imagedestroy($normIm);
                }
            } catch (\Throwable $e) {
                Log::warning('[SoilMLAnalysisService] Could not generate fallback before_after image: ' . $e->getMessage());
            }
        }

        $beforeAfterUrl = null;
        $rawUrl = null;
        if ($disk === 's3') {
            try {
                if (file_exists($fullBaJpg)) {
                    $baData = file_get_contents($fullBaJpg);
                    if ($baData !== false) {
                        $uploadedBa = Storage::disk('s3')->put($baJpgName, $baData, 'public');
                        if ($uploadedBa) {
                            $beforeAfterUrl = Storage::disk('s3')->url($baJpgName);
                        }
                    }
                }
                if (file_exists($fullRawJpg)) {
                    $rawData = file_get_contents($fullRawJpg);
                    if ($rawData !== false) {
                        $uploadedRaw = Storage::disk('s3')->put($rawJpgName, $rawData, 'public');
                        if ($uploadedRaw) {
                            $rawUrl = Storage::disk('s3')->url($rawJpgName);
                        }
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('[SoilMLAnalysisService] S3 BA/Raw upload failed: ' . $e->getMessage());
            }
        }

        if (empty($beforeAfterUrl)) {
            $beforeAfterUrl = file_exists($fullBaJpg) ? (asset('storage/' . $baJpgName) . $cacheBuster) : null;
        }
        if (empty($rawUrl)) {
            $rawUrl = file_exists($fullRawJpg) ? (asset('storage/' . $rawJpgName) . $cacheBuster) : null;
        }

        $analysis['scan_model']             = $soilScan;
        $analysis['soil_model']             = $soil;
        $analysis['scan_id']                = $soilScan?->soil_scan_id;
        $analysis['scan_seq']               = $soil ? SoilScan::where('soil_id', $soil->soil_id)->count() : null;
        $analysis['created_scans']          = $createdScans;
        $analysis['scan_image']             = $publicScanRelativePath;
        $analysis['scan_image_url']         = $normalizedUrl;
        $analysis['image']                  = $normalizedUrl;
        $analysis['stage2_normalized_url']  = $normalizedUrl;
        $analysis['normalized_image_url']   = $normalizedUrl;
        $analysis['before_after_image_url'] = $beforeAfterUrl;
        $analysis['raw_image_url']          = $rawUrl;
        $analysis['stage2_mode']            = $analysis['stage2_mode'] ?? $pythonResult['stage2_mode'] ?? config('soil.stage2_mode', 'classical');
        $analysis['stage2_engine']          = $analysis['stage2_engine'] ?? $analysis['stage2_mode'];
        $analysis['success']                = true;
        $analysis['rejected']               = false;

        return $analysis;
    }
}

