<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Soil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ManageSoilController extends Controller
{
    public function index(Request $request)
    {
        $query = Soil::with('munsellColor');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('soil_name', 'like', "%{$search}%")
                  ->orWhere('hue', 'like', "%{$search}%")
                  ->orWhere('full_code', 'like', "%{$search}%")
                  ->orWhere('soil_type', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        return response()->json([
            'status' => 'success',
            'data'   => $query->paginate(15),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'soil_name'      => ['required', 'string', 'max:255'],
            'description'    => ['nullable', 'string'],
            'hue'            => ['required', 'string', 'max:20'],
            'full_code'      => ['nullable', 'string', 'max:30'],
            'soil_type'      => ['nullable', 'string', 'max:100'],
            'organic_matter' => ['nullable', 'string', 'max:100'],
            'hex_color'      => ['nullable', 'string', 'max:20'],
            'status'         => ['nullable', 'string', 'in:Active,Archived,Draft'],
            'color_id'       => ['nullable', 'integer', 'exists:munsell_colors,munsell_color_id'],
        ]);

        $validated['admin_id'] = Auth::id();
        $validated['last_admin_id'] = Auth::id();
        $validated['status'] = $validated['status'] ?? 'Active';

        $soil = Soil::create($validated);

        return response()->json([
            'status' => 'success',
            'data'   => $soil,
        ], 201);
    }

    public function show(Soil $soil)
    {
        $soil->load('munsellColor');

        return response()->json([
            'status' => 'success',
            'data'   => $soil,
        ]);
    }

    public function update(Request $request, Soil $soil)
    {
        $validated = $request->validate([
            'soil_name'      => ['sometimes', 'string', 'max:255'],
            'description'    => ['nullable', 'string'],
            'hue'            => ['sometimes', 'string', 'max:20'],
            'full_code'      => ['nullable', 'string', 'max:30'],
            'soil_type'      => ['nullable', 'string', 'max:100'],
            'organic_matter' => ['nullable', 'string', 'max:100'],
            'hex_color'      => ['nullable', 'string', 'max:20'],
            'status'         => ['sometimes', 'string', 'in:Active,Archived,Draft'],
            'color_id'       => ['nullable', 'integer', 'exists:munsell_colors,munsell_color_id'],
        ]);

        $validated['last_admin_id'] = Auth::id();
        $soil->update($validated);

        return response()->json([
            'status' => 'success',
            'data'   => $soil,
        ]);
    }

    public function destroy(Soil $soil)
    {
        $soil->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Soil record deleted successfully.',
        ]);
    }
}
