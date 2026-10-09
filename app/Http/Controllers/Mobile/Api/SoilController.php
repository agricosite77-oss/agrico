<?php

namespace App\Http\Controllers\Mobile\Api;

use App\Http\Controllers\Controller;
use App\Models\Soil;
use App\Models\SoilScan;
use Illuminate\Http\Request;

class SoilController extends Controller
{
    public function index(Request $request)
    {
        $soils = Soil::with('munsellColor')->paginate(20);

        return response()->json([
            'status' => 'success',
            'data'   => $soils,
        ]);
    }

    /**
     * Get Most Scanned Soils dynamically based on the database records.
     * Supports optional hue filtering and text search.
     */
    public function mostScanned(Request $request)
    {
        $hue = $request->query('hue');
        $search = $request->query('search');

        $query = Soil::with(['munsellColor'])
            ->where('status', 'Active')
            ->orderByDesc('scan_count')
            ->orderBy('soil_id', 'asc');

        if (!empty($hue) && $hue !== 'All') {
            $query->where(function ($q) use ($hue) {
                $q->where('hue', $hue)
                  ->orWhere(function ($sq) use ($hue) {
                      $sq->whereNull('hue')
                         ->whereHas('munsellColor', fn($mq) => $mq->where('hue', $hue));
                  });
            });
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('soil_name', 'like', "%{$search}%")
                  ->orWhere('full_code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $soils = $query->take(20)->get();

        $soilIds = $soils->pluck('soil_id');
        $latestScans = SoilScan::whereIn('soil_id', $soilIds)
            ->whereNotNull('scan_image')
            ->where('scan_image', '!=', '')
            ->latest('scan_date')
            ->get()
            ->keyBy('soil_id');

        $data = $soils->map(function ($soil) use ($latestScans) {
            $scan = $latestScans->get($soil->soil_id);
            $scanImg = null;
            if ($scan && !empty($scan->scan_image)) {
                $scanImg = str_starts_with($scan->scan_image, 'http')
                    ? $scan->scan_image
                    : url($scan->scan_image);
            }

            $mColor = $soil->munsellColor;
            $code = $soil->full_code ?: ($mColor?->full_code ?? '10YR 4/4');
            $name = $soil->effective_soil_name ?: ($mColor?->color_name ?? 'Soil Color');
            $desc = $mColor?->description ?: ($soil->effective_description ?: "$code - $name");

            return [
                'id'                 => 'soil_' . $soil->soil_id,
                'soil_id'            => $soil->soil_id,
                'munsellCode'        => $code,
                'full_code'          => $code,
                'hue'                => $soil->hue ?: ($mColor?->hue ?? '10YR'),
                'value'              => (string) $soil->dynamic_value,
                'chroma'             => (string) $soil->dynamic_chroma,
                'colorName'          => $name,
                'hexCode'            => $soil->effective_hex_color,
                'description'        => $desc,
                'soil_organic_index' => $mColor?->soil_organic_index ?: '—',
                'soil_type'          => $mColor?->soil_type ?: ($soil->effective_soil_type ?: '—'),
                'scansCount'         => (int) $soil->scan_count,
                'scanImage'          => $scanImg,
            ];
        });

        return response()->json([
            'status' => 'success',
            'data'   => $data,
        ]);
    }

    public function show(string $id)
    {
        $soil = Soil::with('munsellColor')->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data'   => $soil,
        ]);
    }
}
