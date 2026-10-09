<?php

namespace App\Http\Controllers\Mobile\Api;

use App\Http\Controllers\Controller;
use App\Models\MunsellColor;
use App\Livewire\Admin\MunsellColor\ManageMunsellColor;
use Illuminate\Http\Request;

class MunsellColorController extends Controller
{
    public function index(Request $request)
    {
        $query = MunsellColor::where(function ($q) {
            $q->where('status', 'Active')->orWhereNull('status');
        });

        if ($search = trim($request->input('search', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('full_code', 'like', "%{$search}%")
                  ->orWhere('color_name', 'like', "%{$search}%")
                  ->orWhere('hue', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('soil_type', 'like', "%{$search}%")
                  ->orWhere('soil_organic_index', 'like', "%{$search}%");
            });
        }

        if ($hue = $request->input('hue')) {
            if ($hue !== 'All' && $hue !== 'Recommend') {
                $query->where('hue', $hue);
            }
        }

        $colors = $query->orderBy('hue')->orderBy('value')->orderBy('chroma')->get();

        $totalAll = MunsellColor::where(function ($q) {
            $q->where('status', 'Active')->orWhereNull('status');
        })->count();

        $transformed = $colors->map(function ($c) {
            $cleanHue = trim(str_replace('/', '', $c->hue ?? '10YR'));
            $cleanVal = trim((string)($c->value ?? '4'));
            $cleanChroma = trim((string)($c->chroma ?? '3'));
            $fullCode = $c->full_code ?: ($cleanHue . ' ' . $cleanVal . '/' . $cleanChroma);

            $hex = !empty($c->hex_code)
                ? (str_starts_with($c->hex_code, '#') ? $c->hex_code : '#' . $c->hex_code)
                : ManageMunsellColor::munsellToHex($cleanHue, $cleanVal, $cleanChroma);

            return [
                'id'                 => 'munsell_' . $c->munsell_color_id,
                'munsell_color_id'   => $c->munsell_color_id,
                'colorName'          => $c->color_name ?: ('Munsell ' . $cleanHue),
                'munsellCode'        => $fullCode,
                'full_code'          => $fullCode,
                'hue'                => $cleanHue,
                'value'              => $cleanVal,
                'chroma'             => $cleanChroma,
                'hexCode'            => $hex,
                'description'        => $c->description ?: ($fullCode . ' - ' . strtolower($c->color_name ?? '')),
                'soil_organic_index' => $c->soil_organic_index ?: '—',
                'soil_type'          => $c->soil_type ?: '—',
            ];
        });

        return response()->json([
            'status'    => 'success',
            'total'     => $transformed->count(),
            'total_all' => $totalAll,
            'data'      => $transformed,
        ]);
    }
}
