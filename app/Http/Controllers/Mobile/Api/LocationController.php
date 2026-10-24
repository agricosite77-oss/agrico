<?php

namespace App\Http\Controllers\Mobile\Api;

use App\Http\Controllers\Controller;
use App\Models\LocationMapping;
use App\Models\Soil;
use App\Models\SoilScan;
use App\Models\User;
use App\Models\UserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LocationController extends Controller
{
    /**
     * Get all active geo-tagged soil location mappings.
     * Shared across User Web Map, Admin Map, and Mobile App.
     */
    public function index(Request $request)
    {
        $locations = LocationMapping::with([
            'soil.munsellColor',
            'soilScan:soil_scan_id,scan_note,scan_date,platform,user_id,regions_data'
        ])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where(function ($q) {
                $q->where('status', 'Active')
                  ->orWhereNull('status')
                  ->orWhere('status', '');
            })
            ->whereDoesntHave('soilScan', function ($sq) {
                $sq->where('verification_status', 'Rejected');
            })
            ->orderBy('location_mapping_id', 'desc')
            ->get();

        $locations->each(function ($m) {
            $covStr = '';
            if ($m->soilScan?->scan_note && preg_match('/\[Covered Area:\s*([^\]]+)\]/i', $m->soilScan->scan_note, $cm)) {
                $covStr = trim($cm[1]);
            }
            $m->setAttribute('coverage', $covStr ?: '500 m²');

            if (empty($m->barangay) || strtolower($m->barangay) === 'lal-lo overall' || strtolower($m->barangay) === 'all barangays') {
                $m->barangay = LocationMapping::resolveNearestBarangay((float) $m->latitude, (float) $m->longitude);
            }

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

        return response()->json([
            'status' => 'success',
            'data' => $locations,
        ]);
    }

    /**
     * Store newly pinned soil sample location from Mobile App for Admin Approval.
     */
    public function storePin(Request $request)
    {
        // Tolerant normalization for soil_confidence and soil_percentage
        if ($request->has('soil_confidence')) {
            $rawConf = $request->input('soil_confidence');
            if ($rawConf !== null && $rawConf !== '') {
                $cleanConf = is_string($rawConf) ? trim(str_replace(['%', ' '], '', $rawConf)) : $rawConf;
                if (is_numeric($cleanConf)) {
                    $cVal = (float)$cleanConf;
                    if ($cVal > 0 && $cVal <= 1.0) {
                        $cVal = $cVal * 100;
                    }
                    $request->merge(['soil_confidence' => round($cVal, 1)]);
                }
            } else {
                $request->merge(['soil_confidence' => null]);
            }
        }

        if ($request->has('soil_percentage')) {
            $rawPct = $request->input('soil_percentage');
            if ($rawPct !== null && $rawPct !== '') {
                $cleanPct = is_string($rawPct) ? trim($rawPct) : (string)$rawPct;
                if (!str_ends_with($cleanPct, '%') && is_numeric($cleanPct)) {
                    $cleanPct .= '%';
                }
                $request->merge(['soil_percentage' => $cleanPct]);
            }
        }

        $validated = $request->validate([
            'latitude'          => ['required', 'numeric', 'between:-90,90'],
            'longitude'         => ['required', 'numeric', 'between:-180,180'],
            'barangay'          => ['required', 'string', 'max:255'],
            'soil_id'           => ['nullable', 'integer'],
            'soil_scan_id'      => ['nullable', 'integer'],
            'scan_note'         => ['nullable', 'string', 'max:1000'],
            'coverage_area'     => ['nullable', 'string', 'max:100'],
            'pinned_soil_index' => ['nullable', 'integer', 'min:0'],
            'soil_percentage'   => ['nullable', 'string', 'max:20'],
            'soil_confidence'   => ['nullable', 'numeric', 'between:0,100'],
            'soil_layer_meta'   => ['nullable'],
        ]);

        $userId = Auth::guard('sanctum')->id() ?? Auth::id() ?? User::where('role', 'admin')->value('user_id') ?? 'ADM-001';

        // Determine or associate Soil record
        $soilId = $validated['soil_id'] ?? null;
        if (! $soilId && ! empty($validated['soil_scan_id'])) {
            $scan = SoilScan::find($validated['soil_scan_id']);
            $soilId = $scan?->soil_id;
        }
        if (! $soilId) {
            $soil = Soil::first();
            $soilId = $soil?->soil_id ?? 1;
        }

        // Determine or create SoilScan record
        $soilScanId = $validated['soil_scan_id'] ?? null;
        if (! $soilScanId) {
            $newScan = SoilScan::create([
                'user_id'    => $userId,
                'soil_id'    => $soilId,
                'platform'   => 'mobile',
                'scan_note'  => $validated['scan_note'] ?? 'Mobile soil location sample pin',
                'scan_date'  => now(),
            ]);
            $soilScanId = $newScan->soil_scan_id;
        } else {
            // Update existing scan note if provided
            if (! empty($validated['scan_note'])) {
                SoilScan::where('soil_scan_id', $soilScanId)->update([
                    'scan_note' => $validated['scan_note'],
                ]);
            }
        }

        $pinnedIndex = isset($validated['pinned_soil_index']) ? (int) $validated['pinned_soil_index'] : 0;

        // Prevent duplicate pinning of the same layer or soil for this scan
        if ($soilScanId !== null) {
            $existing = LocationMapping::where('soil_scan_id', $soilScanId)
                ->where(function ($q) use ($pinnedIndex, $soilId) {
                    $q->where('pinned_soil_index', $pinnedIndex)
                      ->orWhere('soil_id', $soilId);
                })
                ->first();
            if ($existing) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'This soil layer has already been pinned for this scan.',
                    'data'    => $existing,
                ], 422);
            }
        }

        // Resolve barangay to exact authentic Lal-lo barangay name
        $barangay = trim($validated['barangay']);
        if (empty($barangay) || $barangay === 'Lal-lo Overall') {
            $barangay = LocationMapping::resolveNearestBarangay((float) $validated['latitude'], (float) $validated['longitude']);
        }

        // Create LocationMapping record with Pending status awaiting Admin approval
        $location = LocationMapping::create([
            'soil_scan_id'      => $soilScanId,
            'soil_id'           => $soilId,
            'latitude'          => $validated['latitude'],
            'longitude'         => $validated['longitude'],
            'barangay'          => $barangay,
            'timestamp'         => now(),
            'status'            => 'Pending',
            'pinned_soil_index' => $pinnedIndex,
            'soil_percentage'   => $validated['soil_percentage'] ?? null,
            'soil_confidence'   => $validated['soil_confidence'] ?? null,
            'soil_layer_meta'   => is_array($validated['soil_layer_meta'] ?? null)
                ? $validated['soil_layer_meta']
                : (is_string($validated['soil_layer_meta'] ?? null) ? json_decode($validated['soil_layer_meta'], true) : null),
        ]);

        // Create UserRequest entry for Admin Approval tracking
        try {
            $user = User::find($userId);
            $coverageText = ! empty($validated['coverage_area']) ? " [Covered Area: {$validated['coverage_area']}]" : '';
            $notesText = "Soil Sample Location Pin #{$location->location_mapping_id} - Barangay: {$barangay}{$coverageText}. Coords: ({$validated['latitude']}, {$validated['longitude']}). Notes: " . ($validated['scan_note'] ?? 'No extra notes');
            $requestCode = 'REQ-PIN-' . strtoupper(bin2hex(random_bytes(3)));

            UserRequest::create([
                'request_code' => $requestCode,
                'user_name'    => $user?->name ?? 'Mobile User',
                'user_email'   => $user?->email ?? 'mobile@agrico.gov.ph',
                'type'         => 'Soil Coordinates',
                'description'  => $notesText,
                'status'       => 'Pending',
                'submitted_at' => now(),
            ]);
        } catch (\Throwable $e) {
            // Log but don't fail the mapping creation
            report($e);
        }

        // Trigger notifications across Mobile Application, Website, and Email
        try {
            \App\Services\NotificationService::notifyPinSubmitted($location, $user ?? User::find($userId), 'mobile application');
        } catch (\Throwable $e) {
            report($e);
        }

        $loadedLocation = LocationMapping::with(['soil.munsellColor', 'soilScan'])
            ->find($location->location_mapping_id);

        return response()->json([
            'status'  => 'success',
            'message' => 'Soil location pinned and submitted for Admin approval successfully.',
            'data'    => $loadedLocation,
        ], 201);
    }
}

