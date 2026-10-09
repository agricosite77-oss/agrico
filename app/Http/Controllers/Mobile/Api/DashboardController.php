<?php

namespace App\Http\Controllers\Mobile\Api;

use App\Http\Controllers\Controller;
use App\Models\SoilScan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()?->user_id ?? Auth::id() ?? $request->user()?->id;

        $recentScans = SoilScan::with(['soil.munsellColor', 'location'])
            ->when($userId, fn ($q) => $q->where('user_id', $userId))
            ->latest('scan_date')
            ->limit(5)
            ->get();

        $totalScans = SoilScan::when($userId, fn ($q) => $q->where('user_id', $userId))->count();

        return response()->json([
            'status' => 'success',
            'data'   => [
                'total_scans'  => $totalScans,
                'recent_scans' => $recentScans,
            ],
        ]);
    }
}
