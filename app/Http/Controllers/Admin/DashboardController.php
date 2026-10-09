<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\LocationMapping;
use App\Models\Soil;
use App\Models\SoilScan;
use App\Models\User;
use App\Models\UserRequest;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data'   => [
                'total_users'        => User::count(),
                'total_scans'        => SoilScan::count(),
                'total_soils'        => Soil::count(),
                'total_locations'    => LocationMapping::count(),
                'total_applications' => Application::count(),
                'pending_requests'   => UserRequest::where('status', 'Pending')->count(),
            ],
        ]);
    }
}
