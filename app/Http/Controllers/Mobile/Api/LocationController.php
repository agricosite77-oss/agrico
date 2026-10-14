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
        $locations = LocationMapping::with(['soil.munsellColor', 'soilScan'])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('status', 'Active')
            ->orderBy('location_mapping_id', 'desc')
            ->get();

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
        $validated = $request->validate([
            'latitude'      => ['required', 'numeric', 'between:-90,90'],
            'longitude'     => ['required', 'numeric', 'between:-180,180'],
            'barangay'      => ['required', 'string', 'max:255'],
            'soil_id'       => ['nullable', 'integer'],
            'soil_scan_id'  => ['nullable', 'integer'],
            'scan_note'     => ['nullable', 'string', 'max:1000'],
            'coverage_area' => ['nullable', 'string', 'max:100'],
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

        // Resolve barangay to exact authentic Lal-lo barangay name
        $barangay = trim($validated['barangay']);
        if (empty($barangay) || $barangay === 'Lal-lo Overall') {
            $barangay = LocationMapping::resolveNearestBarangay((float) $validated['latitude'], (float) $validated['longitude']);
        }

        // Create LocationMapping record with Pending status awaiting Admin approval
        $location = LocationMapping::create([
            'soil_scan_id' => $soilScanId,
            'soil_id'      => $soilId,
            'latitude'     => $validated['latitude'],
            'longitude'    => $validated['longitude'],
            'barangay'     => $barangay,
            'timestamp'    => now(),
            'status'       => 'Pending',
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

