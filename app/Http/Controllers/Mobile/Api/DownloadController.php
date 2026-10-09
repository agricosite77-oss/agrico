<?php

namespace App\Http\Controllers\Mobile\Api;

use App\Http\Controllers\Controller;
use App\Models\Download;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DownloadController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'application_id' => ['required', 'exists:applications,application_id'],
            'device_type'    => ['nullable', 'string', 'max:50'],
        ]);

        $download = Download::create([
            'application_id' => $validated['application_id'],
            'user_id'        => Auth::id(),
            'device_type'    => $validated['device_type'] ?? 'Mobile',
            'downloaded_at'  => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'data'   => $download,
        ], 201);
    }
}
