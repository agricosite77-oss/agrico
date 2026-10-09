<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LocationMapping;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ManageMapController extends Controller
{
    /**
     * Fast direct toggle of location mapping status (bypasses Livewire to prevent request queuing).
     */
    public function toggleStatus(Request $request, $id): JsonResponse
    {
        $cleanId = ltrim((string) $id, '#');
        $mapping = LocationMapping::find($cleanId);

        if (!$mapping) {
            return response()->json(['success' => false, 'message' => 'Location mapping not found.'], 404);
        }

        $isInactive = strtolower(trim($mapping->status ?? '')) === 'inactive';
        $newStatus = $isInactive ? 'Active' : 'Inactive';
        $mapping->status = $newStatus;
        $mapping->save();

        return response()->json([
            'success' => true,
            'status' => $newStatus,
            'mapping_id' => $mapping->location_mapping_id ?? $mapping->id,
        ]);
    }
}
