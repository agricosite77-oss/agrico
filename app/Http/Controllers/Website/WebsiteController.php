<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\FAQ;
use App\Models\LocationMapping;
use App\Models\MunsellColor;
use App\Models\Soil;
use App\Models\SoilScan;
use App\Models\User;
use App\Models\UserRequest;
use App\Services\SoilMLAnalysisService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;

class WebsiteController extends Controller
{
    /**
     * Analyze uploaded or captured soil image dynamically via ML/CIELAB pipeline.
     */
    public function analyzeScan(Request $request, SoilMLAnalysisService $mlService)
    {
        $authUser = Auth::guard('web')->user() ?? Auth::user() ?? Auth::guard('admin')->user();
        if (!$authUser && session()->has('user_id')) {
            $authUser = User::where('user_id', session('user_id'))->first();
        }
        if (!$authUser && session()->has('admin_id')) {
            $authUser = User::where('user_id', session('admin_id'))->first();
        }

        if (!$authUser) {
            $authUser = User::first();
        }

        if ($authUser && method_exists($authUser, 'isApproved') && !$authUser->isApproved()) {
            return response()->json([
                'success' => false,
                'error'   => 'ACCOUNT_PENDING_APPROVAL',
                'message' => 'Account Pending Approval: Your account is awaiting admin approval. Scanning and Map access will be enabled once approved.',
            ], 403);
        }

        $imageInput = null;

        if ($request->hasFile('scan_image') && $request->file('scan_image')->isValid()) {
            $imageInput = $request->file('scan_image');
        } elseif ($request->filled('scan_image')) {
            $imageInput = $request->input('scan_image');
        } elseif ($request->filled('image_base64')) {
            $imageInput = $request->input('image_base64');
        }

        if (!$imageInput) {
            return response()->json([
                'success' => false,
                'message' => 'Please upload or capture a clear soil sample photo to scan.'
            ], 422);
        }

        // Accept the client-generated scan UUID or generate one; validate to prevent path traversal
        $scanId = $request->input('scan_id', '');
        if (empty($scanId)) {
            $scanId = (string) \Illuminate\Support\Str::uuid();
        } elseif (!preg_match('/^[a-zA-Z0-9\-]{8,64}$/', $scanId)) {
            return response()->json(['success' => false, 'message' => 'Invalid scan_id.'], 422);
        }

        // Persist initial scan image directly to public storage so it has a guaranteed web-accessible path
        try {
            $publicScansDir = storage_path('app/public/scans');
            if (!is_dir($publicScansDir)) {
                @mkdir($publicScansDir, 0755, true);
            }
            $targetPublicJpg = storage_path('app/public/scans/scan_' . $scanId . '.jpg');
            $targetRawJpg    = storage_path('app/public/scans/scan_' . $scanId . '_raw.jpg');
            if ($request->hasFile('scan_image') && $request->file('scan_image')->isValid()) {
                @copy($request->file('scan_image')->getRealPath(), $targetPublicJpg);
                @copy($request->file('scan_image')->getRealPath(), $targetRawJpg);
            } elseif (is_string($imageInput)) {
                if (preg_match('/^data:image\/(\w+);base64,/', $imageInput)) {
                    $rawBin = base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $imageInput));
                    if ($rawBin) {
                        @file_put_contents($targetPublicJpg, $rawBin);
                        @file_put_contents($targetRawJpg, $rawBin);
                    }
                } elseif (file_exists($imageInput)) {
                    @copy($imageInput, $targetPublicJpg);
                    @copy($imageInput, $targetRawJpg);
                }
            }

            if (env('FILESYSTEM_DISK') === 's3' && file_exists($targetPublicJpg)) {
                try {
                    Storage::disk('s3')->put('scans/scan_' . $scanId . '.jpg', file_get_contents($targetPublicJpg), 'public');
                } catch (\Throwable $e_s3) {
                    \Illuminate\Support\Facades\Log::warning('[WebsiteController] S3 scan upload failed: ' . $e_s3->getMessage());
                }
            }
        } catch (\Throwable $e) {
            Log::warning('[WebsiteController] Could not pre-save public scan photo: ' . $e->getMessage());
        }
        try {
            // Pass pre-saved public JPG if available to guarantee file persistence for detached Python
            $analysisInput = (isset($targetPublicJpg) && file_exists($targetPublicJpg) && filesize($targetPublicJpg) > 0)
                ? $targetPublicJpg
                : $imageInput;
            $mlService->analyzeImage($analysisInput, $scanId);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error'   => 'NON_SOIL_DETECTED',
                'message' => 'Soil Scan Error: Unable to start scan pipeline. Please try again.',
            ], 422);
        }

        // Return immediately (HTTP 202 Accepted) — frontend polls /scan/progress/{scanId}
        return response()->json([
            'success' => true,
            'async'   => true,
            'scan_id' => $scanId,
        ], 202);
    }

    /**
     * Poll progress of a running Python scan pipeline.
     *
     * Reads /tmp/scan_progress_{scanId}.json written by colorimetry_service.py.
     * When Stage 6 is done, also reads /tmp/scan_result_{scanId}.json,
     * runs the full DB-write logic, attaches the result, then cleans up both files.
     */
    public function scanProgress(Request $request, string $scanId, SoilMLAnalysisService $mlService)
    {
        // Sanitise to prevent path traversal
        if (!preg_match('/^[a-zA-Z0-9\-]{8,64}$/', $scanId)) {
            return response()->json(['error' => 'invalid_id'], 400);
        }

        $tmpDir       = sys_get_temp_dir();
        // Return cached payload immediately if already finalized
        $cachedPayload = \Illuminate\Support\Facades\Cache::get("scan_result_payload_{$scanId}");
        if ($cachedPayload) {
            return response()->json($cachedPayload);
        }

        $progressFile = $tmpDir . DIRECTORY_SEPARATOR . "scan_progress_{$scanId}.json";
        $resultFile   = $tmpDir . DIRECTORY_SEPARATOR . "scan_result_{$scanId}.json";

        if (!file_exists($progressFile)) {
            $scanIdSafe = preg_replace('/[^a-zA-Z0-9_\-]/', '', $scanId);
            $logFile = $tmpDir . DIRECTORY_SEPARATOR . "scan_python_{$scanIdSafe}.log";
            if (file_exists($logFile) && filesize($logFile) > 0) {
                $rawContent = file_get_contents($logFile);
                $errContent = is_string($rawContent) ? trim($rawContent) : '';
                if (!empty($errContent) && (
                    str_contains($errContent, 'Traceback') ||
                    str_contains($errContent, '"success": false') ||
                    str_contains($errContent, 'Image not found') ||
                    str_contains($errContent, 'Error:')
                )) {
                    return response()->json([
                        'events' => [['stage' => 0, 'status' => 'error', 'error' => $errContent]],
                        'stage'  => 0,
                        'status' => 'error',
                        'cursor' => 1,
                    ]);
                }
            }
            $debug = [];
            if (config('app.debug')) {
                $debug = [
                    'tmpDir'     => $tmpDir,
                    'logFile'    => $logFile,
                    'logExists'  => file_exists($logFile),
                    'logSize'    => file_exists($logFile) ? filesize($logFile) : 0,
                    'logContent' => file_exists($logFile) ? substr((string) file_get_contents($logFile), 0, 500) : null,
                    'pyBinary'   => \App\Services\PythonProcessRunner::getPythonBinary(),
                ];
            }
            return response()->json(['events' => [], 'stage' => 0, 'status' => 'pending', 'cursor' => 0, 'debug' => $debug]);
        }

        $history = json_decode(file_get_contents($progressFile), true) ?? [];
        if (!is_array($history)) $history = [];

        $since = (int) $request->query('since', 0);
        $newEvents = array_slice($history, $since);

        $latest = end($history) ?: ['stage' => 0, 'status' => 'pending'];
        $payload = [
            'events' => $newEvents,
            'cursor' => count($history),
            'stage'  => $latest['stage'] ?? 0,
            'status' => $latest['status'] ?? 'pending',
        ];

        if (($latest['stage'] ?? 0) === 6 && ($latest['status'] ?? '') === 'done') {
            $lockKey = "scan_finalizing_{$scanId}";
            $acquired = \Illuminate\Support\Facades\Cache::add($lockKey, true, 30);

            if ($acquired && file_exists($resultFile)) {
                try {
                    $rawResult = json_decode(file_get_contents($resultFile), true);
                    if ($rawResult && ($rawResult['success'] ?? false)) {
                        // Resolve authenticated user for web
                        $authUser = Auth::guard('web')->user() ?? Auth::user() ?? Auth::guard('admin')->user();
                        if (!$authUser && session()->has('user_id')) {
                            $authUser = User::where('user_id', session('user_id'))->first();
                        }
                        if (!$authUser && session()->has('admin_id')) {
                            $authUser = User::where('user_id', session('admin_id'))->first();
                        }
                        if (!$authUser) {
                            $authUser = User::first();
                        }
                        $userId = $authUser?->user_id ?? $authUser?->id ?? null;
                        if (!$userId || !User::where('user_id', $userId)->exists()) {
                            $userId = User::first()?->user_id ?? 'ADM-001';
                        }

                        $fullResult = $mlService->persistScanResult($rawResult, $scanId, $userId, 'website');
                        $payload['result'] = $fullResult;
                        $payload['stage'] = 6;
                        $payload['status'] = 'done';
                        if (!empty($fullResult['rejected'])) {
                            $payload['status'] = 'rejected';
                            $payload['rejected'] = true;
                        }
                    }
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::error('[WebsiteController] Error finalizing scan: ' . $e->getMessage());
                } finally {
                    @unlink($progressFile);
                    @unlink($resultFile);
                    \Illuminate\Support\Facades\Cache::put("scan_result_payload_{$scanId}", $payload, 180);
                    \Illuminate\Support\Facades\Cache::forget($lockKey);
                }
            } elseif (\Illuminate\Support\Facades\Cache::has($lockKey)) {
                // Background worker is currently writing to DB; tell frontend to keep polling
                $payload['stage'] = 6;
                $payload['status'] = 'finalizing';
            }
        }

        return response()->json($payload);
    }

    /**
     * Public Home page.
     */
    public function home()
    {
        $totalScans    = SoilScan::count();
        $totalColors   = MunsellColor::where(function ($q) {
            $q->where('status', 'Active')->orWhereNull('status');
        })->count();
        $totalUsers    = User::whereNotIn('role', ['admin', 'superadmin'])
            ->where(function ($q) {
                $q->where('status', 'Active')->orWhereNull('status');
            })->count();
        // Pinned Soil Locations: count actual user-submitted / approved pinned locations or pin location requests
        $pinnedLocationsCount = LocationMapping::whereNotNull('latitude')->whereNotNull('longitude')->count();
        $pinRequestsCount     = UserRequest::where(function ($q) {
            $q->where('type', 'Soil Coordinates')
              ->orWhere('type', 'Pin Soil Location')
              ->orWhere('type', 'LIKE', '%Pin%')
              ->orWhere('type', 'LIKE', '%Coordinates%');
        })->count();
        $totalSoils = max($pinnedLocationsCount, $pinRequestsCount);

        // Recent soil scans for the dashboard carousel (latest 6, all users)
        $recentScans = SoilScan::with(['soil.munsellColor', 'location'])
            ->latest('scan_date')
            ->limit(6)
            ->get();

        // Pending admin approval count (soils not yet Active)
        $pendingCount = Soil::where('status', '!=', 'Active')
            ->orWhereNull('status')
            ->count();

        // Offline queue is managed by the Android app; always 0 on web
        $offlineQueueCount = 0;

        return view('website.home', compact(
            'totalScans',
            'totalColors',
            'totalUsers',
            'totalSoils',
            'recentScans',
            'pendingCount',
            'offlineQueueCount'
        ));
    }


    /**
     * Scan History page (authenticated and approved users see their records; unapproved users and guests are guarded).
     */
    public function scan()
    {
        $user = Auth::guard('web')->user() ?? Auth::user() ?? Auth::guard('admin')->user();
        if (!$user && session()->has('user_id')) {
            $user = User::where('user_id', session('user_id'))->first();
        }
        if (!$user && session()->has('admin_id')) {
            $user = User::where('user_id', session('admin_id'))->first();
        }

        $isLoggedIn = (bool) $user;
        $isApproved = $isLoggedIn && (method_exists($user, 'isApproved') ? $user->isApproved() : true);
        $isPending  = $isLoggedIn && !$isApproved;

        $userId = $user?->user_id ?? $user?->id ?? null;

        $perPage = (int) request('per_page', 10);
        if ($perPage < 1) {
            $perPage = 10;
        }

        if ($isLoggedIn && $isApproved && $userId) {
            $scans = SoilScan::with(['soil.munsellColor', 'location', 'user'])
                ->where('user_id', $userId)
                ->orderBy('soil_scan_id', 'desc')
                ->paginate($perPage)
                ->appends(request()->query())
                ->fragment('scanHistory');
        } else {
            // Unauthenticated guest or pending unapproved user: return empty paginator for view
            $scans = SoilScan::whereRaw('1 = 0')->paginate($perPage)->fragment('scanHistory');
        }

        $allMunsellColors = MunsellColor::all();

        return response()
            ->view('website.scan', compact('scans', 'allMunsellColors', 'isLoggedIn', 'isApproved', 'isPending'))
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    /**
     * Store analyzed soil scan record into database.
     */
    public function storeScan(\Illuminate\Http\Request $request)
    {
        $authUser = Auth::guard('web')->user() ?? Auth::user() ?? Auth::guard('admin')->user();
        if (!$authUser && session()->has('user_id')) {
            $authUser = User::where('user_id', session('user_id'))->first();
        }
        if (!$authUser && session()->has('admin_id')) {
            $authUser = User::where('user_id', session('admin_id'))->first();
        }

        if (!$authUser) {
            return response()->json([
                'success' => false,
                'error'   => 'AUTH_REQUIRED',
                'message' => 'Please log in to perform a scan and view scan history.',
            ], 401);
        }

        if (method_exists($authUser, 'isApproved') && !$authUser->isApproved()) {
            return response()->json([
                'success' => false,
                'error'   => 'ACCOUNT_PENDING_APPROVAL',
                'message' => 'Account Pending Approval: Your account is awaiting admin approval. Scanning and Map access will be enabled once approved.',
            ], 403);
        }

        $request->validate([
            'soil_name' => 'required|string|max:255',
            'full_code' => 'required|string|max:100',
            'hue'       => 'nullable|string|max:50',
            'scan_note' => 'nullable|string',
            'latitude'  => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $userId = $authUser?->user_id ?? $authUser?->id ?? null;
        if (!$userId || !User::where('user_id', $userId)->exists()) {
            $fallbackUser = User::first();
            $userId = $fallbackUser?->user_id ?? 'ADM-001';
        }

        // Find matching MunsellColor record
        $munsellColor = MunsellColor::where('full_code', $request->full_code)
            ->orWhere('hue', $request->hue)
            ->first();

        // Find or create Soil record
        $soil = Soil::firstOrCreate(
            ['full_code' => $request->full_code],
            [
                'soil_name'   => $request->soil_name,
                'hue'         => $request->hue ?? '10YR',
                'full_code'   => $request->full_code,
                'description' => $request->scan_note ?? 'Sample processed using AgriCo Munsell Determination Algorithm.',
                'status'      => 'Active',
                'scan_count'  => 0,
                'color_id'    => $munsellColor?->munsell_color_id,
                'admin_id'    => $userId,
            ]
        );

        $soil->hue = $request->hue ?? $soil->hue ?? '10YR';
        $soil->full_code = $request->full_code ?? $soil->full_code;
        if ($munsellColor && !$soil->color_id) {
            $soil->color_id = $munsellColor->munsell_color_id;
        }
        $soil->increment('scan_count');
        $soil->save();

        // Create SoilScan record
        $soilScan = SoilScan::create([
            'user_id'    => $userId,
            'soil_id'    => $soil->soil_id,
            'scan_note'  => $request->scan_note ?? "Determined Soil: {$request->soil_name} ({$request->full_code})",
            'scan_date'  => now(),
            'scan_image' => $request->scan_image ?? null,
        ]);

        if ($request->filled('latitude') && $request->filled('longitude')) {
            LocationMapping::create([
                'latitude'     => (float) $request->latitude,
                'longitude'    => (float) $request->longitude,
                'barangay'     => $request->barangay ?? $request->location_name ?? 'Scanned Field Location',
                'timestamp'    => now(),
                'status'       => 'Active',
                'soil_scan_id' => $soilScan->soil_scan_id,
                'soil_id'      => $soil->soil_id,
            ]);
        }

        // Enforce max 300 history records (oldest automatically deleted)
        SoilScan::enforceLimit(300);

        $scanSeq = SoilScan::where('soil_id', $soil->soil_id)->count();

        return response()->json([
            'success'  => true,
            'message'  => 'Soil scan recorded successfully!',
            'scan_id'  => $soilScan->soil_scan_id,
            'scan_seq' => $scanSeq,
        ]);
    }

    /**
     * Store newly pinned soil sample location from Website Scanner for Admin Approval or Direct Active.
     */
    public function pinLocation(Request $request)
    {
        try {
            $validated = $request->validate([
                'latitude'      => ['required', 'numeric', 'between:-90,90'],
                'longitude'     => ['required', 'numeric', 'between:-180,180'],
                'barangay'      => ['required', 'string', 'max:255'],
                'soil_id'       => ['nullable'],
                'soil_scan_id'  => ['nullable'],
                'scan_note'     => ['nullable', 'string', 'max:1000'],
                'coverage_area' => ['nullable', 'string', 'max:100'],
                'pin_all'       => ['nullable', 'boolean'],
                'pin_mode'      => ['nullable', 'string', 'max:50'],
                'regions'       => ['nullable', 'array'],
            ]);

            $authUser = Auth::guard('web')->user() ?? Auth::user() ?? Auth::guard('admin')->user();
            if (!$authUser && session()->has('user_id')) {
                $authUser = User::where('user_id', session('user_id'))->first();
            }
            if (!$authUser && session()->has('admin_id')) {
                $authUser = User::where('user_id', session('admin_id'))->first();
            }
            $userId = $authUser?->user_id ?? $authUser?->id ?? null;
            $validUser = ($userId ? User::where('user_id', $userId)->first() : null) ?? User::first();
            $finalUserId = $validUser?->user_id ?? 'ADM-001';
            $isAdmin = $authUser && in_array($authUser->role, ['admin', 'superadmin']);

            // Determine or associate Soil record
            $soilId = $validated['soil_id'] ?? null;
            if ((!$soilId || !Soil::where('soil_id', $soilId)->exists()) && !empty($validated['soil_scan_id'])) {
                $scan = SoilScan::find($validated['soil_scan_id']);
                $soilId = $scan?->soil_id;
            }
            if (!$soilId || !Soil::where('soil_id', $soilId)->exists()) {
                $soil = Soil::first();
                $soilId = $soil?->soil_id ?? 1;
            }

            // Determine or create SoilScan record
            $soilScanId = $validated['soil_scan_id'] ?? null;
            if (!$soilScanId || !SoilScan::where('soil_scan_id', $soilScanId)->exists()) {
                $newScan = SoilScan::create([
                    'user_id'    => $finalUserId,
                    'soil_id'    => $soilId,
                    'platform'   => 'web',
                    'scan_note'  => $validated['scan_note'] ?? 'Website soil location sample pin',
                    'scan_date'  => now(),
                ]);
                $soilScanId = $newScan->soil_scan_id;
            } elseif (!empty($validated['scan_note'])) {
                SoilScan::where('soil_scan_id', $soilScanId)->update([
                    'scan_note' => $validated['scan_note'],
                ]);
            }

            $directPublish = $isAdmin && $request->boolean('direct_publish');
            $isPinAll = $request->boolean('pin_all') || ($request->input('pin_mode') === 'all');
            $regions = $request->input('regions');
            $createdLocations = [];

            if ($isPinAll && is_array($regions) && count($regions) > 1) {
                foreach ($regions as $idx => $r) {
                    $subSoilId = $r['soil_id'] ?? $r['id'] ?? null;
                    if (!$subSoilId && !empty($r['munsell'])) {
                        $subSoilId = Soil::where('full_code', $r['munsell'])->first()?->soil_id;
                        if (!$subSoilId) {
                            $mColor = \App\Models\MunsellColor::where('full_code', $r['munsell'])->first();
                            if ($mColor) {
                                $subSoilId = Soil::where('color_id', $mColor->munsell_color_id)->first()?->soil_id;
                            }
                        }
                    }
                    if (!$subSoilId || !Soil::where('soil_id', $subSoilId)->exists()) {
                        $subSoilId = $soilId;
                    }

                    // Slight offset (~10-15m) so pins do not completely overlap
                    $offsetLat = ($idx % 2 === 0 ? 1 : -1) * (intdiv($idx + 1, 2) * 0.00015);
                    $offsetLng = (($idx % 4 < 2) ? 1 : -1) * (intdiv($idx + 1, 2) * 0.00015);

                    $loc = LocationMapping::create([
                        'soil_scan_id' => $soilScanId,
                        'soil_id'      => $subSoilId,
                        'latitude'     => $validated['latitude'] + $offsetLat,
                        'longitude'    => $validated['longitude'] + $offsetLng,
                        'barangay'     => $validated['barangay'],
                        'timestamp'    => now(),
                        'status'       => $directPublish ? 'Active' : 'Pending',
                    ]);
                    $createdLocations[] = $loc;
                }
                $location = $createdLocations[0];
            } else {
                // Single pin
                $location = LocationMapping::create([
                    'soil_scan_id' => $soilScanId,
                    'soil_id'      => $soilId,
                    'latitude'     => $validated['latitude'],
                    'longitude'    => $validated['longitude'],
                    'barangay'     => $validated['barangay'],
                    'timestamp'    => now(),
                    'status'       => $directPublish ? 'Active' : 'Pending',
                ]);
                $createdLocations[] = $location;
            }

            // Create a UserRequest ticket for Admin Approval in Manage Request (unless direct published)
            if (!$directPublish) {
                try {
                    $user = $authUser ?? User::where('user_id', $finalUserId)->first();
                    $userName = $user?->name ?? $user?->full_name ?? ($isAdmin ? 'AgriCo Admin' : 'Website User');
                    $userEmail = $user?->email ?? ($isAdmin ? 'agricosite77@gmail.com' : 'user@agrico.gov.ph');
                    $coverageText = !empty($validated['coverage_area']) ? " [Covered Area: {$validated['coverage_area']}]" : '';
                    $notesText = "Soil Sample Location Pin #{$location->location_mapping_id} - Barangay: {$validated['barangay']}{$coverageText}. Coords: ({$validated['latitude']}, {$validated['longitude']}). Notes: " . ($validated['scan_note'] ?? 'No extra notes');
                    $requestCode = 'REQ-PIN-' . strtoupper(bin2hex(random_bytes(3)));

                    UserRequest::create([
                        'request_code' => $requestCode,
                        'user_name'    => $userName,
                        'user_email'   => $userEmail,
                        'type'         => 'Soil Coordinates',
                        'description'  => $notesText,
                        'status'       => 'Pending',
                        'submitted_at' => now(),
                    ]);
                } catch (\Throwable $e) {
                    report($e);
                }
            }

            return response()->json([
                'success'  => true,
                'message'  => $directPublish
                    ? "Coordinates pinned and published directly to Soil Map!"
                    : "Your soil coordinates in Brgy. {$validated['barangay']} have been submitted to Admin for approval!",
                'location' => $location,
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('[WebsiteController@pinLocation] ' . $e->getMessage(), [
                'exception' => $e,
                'request'   => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unable to submit pin location: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Interactive Map page — open and accessible to the public.
     */
    public function map()
    {
        $user = Auth::guard('web')->user() ?? Auth::user() ?? Auth::guard('admin')->user();
        if (!$user && session()->has('user_id')) {
            $user = User::where('user_id', session('user_id'))->first();
        }
        if (!$user && session()->has('admin_id')) {
            $user = User::where('user_id', session('admin_id'))->first();
        }

        $isLoggedIn = (bool) $user;
        $isApproved = $isLoggedIn && (method_exists($user, 'isApproved') ? $user->isApproved() : true);
        $isPending  = $isLoggedIn && !$isApproved;

        $mappings = LocationMapping::with(['soil.munsellColor', 'soilScan.user'])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where(function ($q) {
                $q->where('status', 'Active')
                  ->orWhereNull('status')
                  ->orWhere('status', '');
            })
            ->orderBy('location_mapping_id', 'asc')
            ->get();

        $mappings->each(function ($m) {
            $covStr = '';
            if ($m->soilScan?->scan_note && preg_match('/\[Covered Area:\s*([^\]]+)\]/i', $m->soilScan->scan_note, $cm)) {
                $covStr = trim($cm[1]);
            }
            $m->setAttribute('coverage', $covStr ?: '500 m²');

            // Exact Barangay Name (never 'Lal-lo Overall')
            if (empty($m->barangay) || strtolower($m->barangay) === 'lal-lo overall' || strtolower($m->barangay) === 'all barangays') {
                $m->barangay = LocationMapping::resolveNearestBarangay((float) $m->latitude, (float) $m->longitude);
            }

            // Munsell Soil Type / General Group
            $fullCode = $m->soil?->full_code ?? $m->soil?->munsellColor?->full_code ?? '';
            $munsellSoilType = $m->soil?->effective_soil_type ?: ($m->soil?->munsellColor?->soil_type ?? null);
            if (!$munsellSoilType && $fullCode) {
                $lookup = \App\Services\MunsellLookupService::lookup($fullCode);
                $munsellSoilType = $lookup['soil_type'] ?? null;
            }
            if (!$munsellSoilType && !empty($m->soil?->soil_type)) {
                $munsellSoilType = $m->soil->soil_type;
            }
            $m->setAttribute('munsell_soil_type', $munsellSoilType ?: 'Leached grayish brown');
        });

        return view('website.map', compact('mappings', 'isLoggedIn', 'isApproved', 'isPending'));
    }

    /**
     * Munsell Color Chart — all active colors.
     */
    public function munsell()
    {
        $colors = MunsellColor::where(function ($q) {
            $q->where('status', 'Active')->orWhereNull('status');
        })->orderBy('hue')->orderBy('value')->orderBy('chroma')->get();

        $hues = $colors->pluck('hue')->unique()->sort()->values();

        return view('website.munsell', compact('colors', 'hues'));
    }

    /**
     * FAQ page — all FAQ entries.
     */
    public function faq()
    {
        $faqs = FAQ::where(function ($q) {
            $q->where('status', 'Active')->orWhereNull('status');
        })->orderBy('faq_id')->get();

        return view('website.faq', compact('faqs'));
    }

    /**
     * Download the latest active mobile application APK (auth & approval required, with maintenance guard).
     */
    public function downloadApp()
    {
        // 1. Guard against System Maintenance / Updating Mode
        if (\App\Models\Application::isMaintenanceMode()) {
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'error'   => 'SYSTEM_MAINTENANCE',
                    'message' => 'AgriCo is currently undergoing system updates and scheduled maintenance. Mobile application downloads are temporarily paused.',
                ], 503);
            }

            return redirect()->route('website.home')->with('maintenance_download_blocked', true);
        }

        $authUser = Auth::guard('web')->user() ?? Auth::user() ?? Auth::guard('admin')->user();
        if (!$authUser && session()->has('user_id')) {
            $authUser = User::where('user_id', session('user_id'))->first();
        }
        if (!$authUser && session()->has('admin_id')) {
            $authUser = User::where('user_id', session('admin_id'))->first();
        }

        if (!$authUser) {
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'error'   => 'AUTH_REQUIRED',
                    'message' => 'Please log in to download the AgriCo Mobile Application.',
                ], 401);
            }
            return redirect()->route('login')->withErrors([
                'email' => 'Please log in to download the AgriCo Mobile Application.',
            ]);
        }

        if (method_exists($authUser, 'isApproved') && !$authUser->isApproved()) {
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'error'   => 'ACCOUNT_PENDING_APPROVAL',
                    'message' => 'Account Pending Approval: Your account is awaiting admin approval. Mobile app download will be enabled once approved.',
                ], 403);
            }
            return redirect()->route('website.home')->withErrors([
                'error' => 'Account Pending Approval: Your account is awaiting admin approval. Mobile app download will be enabled once approved.',
            ]);
        }

        $activeApp = \App\Models\Application::where('status', 'ACTIVE')->first();

        if ($activeApp && $activeApp->file_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($activeApp->file_path)) {
            return \Illuminate\Support\Facades\Storage::disk('public')->download($activeApp->file_path, $activeApp->filename ?: 'agrico-mobile-app.apk');
        }

        $samplePath = 'applications/agrico-mobile-v1.0.0.apk';
        if (!\Illuminate\Support\Facades\Storage::disk('public')->exists($samplePath)) {
            \Illuminate\Support\Facades\Storage::disk('public')->put($samplePath, 'AgriCo Mobile Application Package');
        }

        return \Illuminate\Support\Facades\Storage::disk('public')->download($samplePath, 'agrico-mobile-v1.0.0.apk');
    }


    /**
     * User Profile page (auth required).
     */
    public function profile()
    {
        /** @var \App\Models\User|\App\Models\Admin $user */
        $user = Auth::user() ?? Auth::guard('admin')->user();
        $userId = $user->user_id ?? $user->admin_id ?? null;

        $scanCount = $userId ? SoilScan::where('user_id', $userId)->count() : 0;

        return view('website.profile', compact('user', 'scanCount'));
    }

    /**
     * Update User Account Information.
     */
    public function updateProfile(\Illuminate\Http\Request $request)
    {
        $user = Auth::user() ?? Auth::guard('admin')->user();
        if (!$user) {
            return back()->withErrors(['error' => 'You must be logged in to update profile.']);
        }

        $userRec = null;
        $adminRec = null;

        if ($user instanceof \App\Models\User) {
            $userRec = $user;
            $adminRec = \App\Models\Admin::where('email', $user->email)->orWhere('admin_id', $user->user_id)->first();
        } elseif ($user instanceof \App\Models\Admin) {
            $adminRec = $user;
            $userRec = \App\Models\User::where('email', $user->email)->orWhere('user_id', $user->admin_id)->first();
        }

        $ignoreId = $userRec?->id ?? ($user instanceof \App\Models\User ? $user->id : null);

        $emailRules = ['sometimes', 'required', 'email', 'max:255'];
        if ($ignoreId) {
            $emailRules[] = \Illuminate\Validation\Rule::unique('users', 'email')->ignore($ignoreId);
        }

        $request->validate([
            'name'       => ['sometimes', 'required', 'string', 'max:255'],
            'email'      => $emailRules,
            'password'   => ['nullable', 'string', 'min:8', 'confirmed'],
            'student_id' => ['nullable', 'string', 'max:50'],
            'section'    => ['nullable', 'string', 'max:100'],
            'photo'      => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],
        ]);

        $updateUserData = [];
        $updateAdminData = [];

        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            if ($userRec && $userRec->profile_photo_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($userRec->profile_photo_path);
            }
            if ($adminRec && $adminRec->profile_photo_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($adminRec->profile_photo_path);
            }
            $photoPath = $request->file('photo')->store('profile-photos', 'public');
            $updateUserData['profile_photo_path'] = $photoPath;
            $updateAdminData['profile_photo_path'] = $photoPath;
        }

        if ($request->filled('name')) {
            $updateUserData['name'] = $request->name;
            $updateAdminData['full_name'] = $request->name;
        }
        if ($request->filled('email')) {
            $updateUserData['email'] = $request->email;
            $updateAdminData['email'] = $request->email;
        }
        if ($request->has('student_id')) {
            $updateUserData['student_id'] = $request->student_id ? trim($request->student_id) : null;
        }
        if ($request->has('section')) {
            $updateUserData['section'] = $request->section ? trim($request->section) : null;
        }

        if ($request->filled('password')) {
            $checkPass = $user->password;
            if (empty($request->current_password) || (!empty($checkPass) && !\Illuminate\Support\Facades\Hash::check($request->current_password, $checkPass))) {
                return back()->withErrors(['current_password' => 'The current password is incorrect.']);
            }
            $hashed = \Illuminate\Support\Facades\Hash::make($request->password);
            $updateUserData['password'] = $hashed;
            $updateAdminData['password'] = $hashed;
        }

        $userUpdated = false;
        $adminUpdated = false;

        if ($userRec && !empty($updateUserData)) {
            $userUpdated = $userRec->update($updateUserData);
        }
        if ($adminRec && !empty($updateAdminData)) {
            $adminUpdated = $adminRec->update($updateAdminData);
        }

        if ((!empty($updateUserData) || !empty($updateAdminData)) && !$userUpdated && !$adminUpdated) {
            \Illuminate\Support\Facades\Log::error("Profile update claimed success but updated 0 rows for {$user->email}");
            return back()->withErrors(['error' => 'Could not update your account information. Please try again.']);
        }

        return back()->with('success', 'Account information updated successfully!');
    }

    /**
     * Verify current password via AJAX.
     */
    public function verifyPassword(\Illuminate\Http\Request $request)
    {
        $user = Auth::user() ?? Auth::guard('admin')->user();
        if (!$user) {
            return response()->json(['valid' => false, 'message' => 'Unauthorized access.'], 401);
        }

        $currentPassword = $request->input('current_password');
        if (empty($currentPassword)) {
            return response()->json(['valid' => false, 'message' => 'Please enter your current password.']);
        }

        $isValid = false;
        if (!empty($user->password)) {
            $isValid = \Illuminate\Support\Facades\Hash::check($currentPassword, $user->password);
        }

        return response()->json([
            'valid'   => $isValid,
            'message' => $isValid ? 'Current password verified' : 'Incorrect current password',
        ]);
    }
}
