<?php

namespace App\Livewire\Admin\Dashboard;

use App\Models\Application;
use App\Models\LocationMapping;
use App\Models\MunsellColor;
use App\Models\Soil;
use App\Models\SoilScan;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Component;

class AdminDashboard extends Component
{
    public function render()
    {
        return view('admin.livewire.dashboard.admin-dashboard', [

            'lastUpdated' => Carbon::now(),

            // SOIL STATISTICS
            'totalSoils' => Soil::count(),
            'activeSoils' => Soil::where('status', 'Active')->count(),
            'inactiveSoils' => Soil::where('status', 'Inactive')->count(),

            // SCAN STATISTICS
            'totalScans' => SoilScan::count(),
            'todayScans' => SoilScan::whereDate('created_at', Carbon::today())->count(),
            'weeklyScans' => SoilScan::whereBetween('created_at', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek(),
            ])->count(),

            'todaySoils' => Soil::whereDate('created_at', Carbon::today())->count(),
            'weeklySoils' => Soil::whereBetween('created_at', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek(),
            ])->count(),

            // USER STATISTICS
            'totalUsers' => User::count(),
            'totalAdmins' => User::where('role', 'admin')->count(),
            'weeklyUsers' => User::whereBetween('created_at', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek(),
            ])->count(),

            // APPLICATION & USER REQUEST STATISTICS
            'pendingApplications' => Application::count(),
            'weeklyApplications' => Application::whereBetween('created_at', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek(),
            ])->count(),
            'pendingRequests' => \App\Models\UserRequest::where('status', 'Pending')->count(),
            'weeklyRequests' => \App\Models\UserRequest::whereBetween('created_at', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek(),
            ])->count(),

            // LOCATION MAPPING
            'totalMappingReports' => LocationMapping::count(),
            'weeklyMappingReports' => LocationMapping::whereBetween('created_at', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek(),
            ])->count(),

            // Munsell Hue Monitor
            'munsellHues' => \Illuminate\Support\Facades\DB::table('munsell_colors')
                ->select('munsell_colors.hue')
                ->selectRaw('
                    COALESCE((
                        SELECT COUNT(*)
                        FROM soil_scans
                        JOIN soils ON soils.soil_id = soil_scans.soil_id
                        WHERE soils.hue = munsell_colors.hue
                           OR soils.color_id IN (SELECT munsell_color_id FROM munsell_colors mc2 WHERE mc2.hue = munsell_colors.hue)
                    ), 0) AS total_scans
                ')
                ->groupBy('munsell_colors.hue')
                ->orderByRaw("
                    CASE munsell_colors.hue
                        WHEN '10R' THEN 1
                        WHEN '2.5YR' THEN 2
                        WHEN '5YR' THEN 3
                        WHEN '7.5YR' THEN 4
                        WHEN '10YR' THEN 5
                        WHEN '2.5Y' THEN 6
                        ELSE 7
                    END
                ")
                ->get()
                ->map(function ($color) {
                    $scans = (int) $color->total_scans;
                    $color->scans_count = $scans;
                    $color->soils_count = $scans;
                    return $color;
                }),
        ])->layout('admin.layouts.app');
    }
}
