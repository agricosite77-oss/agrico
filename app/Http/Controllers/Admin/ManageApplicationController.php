<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ManageApplicationController extends Controller
{
    public function index(Request $request)
    {
        $applications = Application::withCount('downloads')
            ->latest()
            ->paginate(15);

        return response()->json([
            'status' => 'success',
            'data'   => $applications,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'application_name' => ['required', 'string', 'max:255'],
            'version'          => ['required', 'string', 'max:50'],
            'file_path'        => ['nullable', 'string'],
            'filename'         => ['nullable', 'string'],
            'file_size'        => ['nullable', 'integer'],
            'release_notes'    => ['nullable', 'string'],
            'status'           => ['nullable', 'string', 'in:Active,Inactive,Archived'],
        ]);

        $validated['uploaded_by'] = Auth::id() ?? 1;
        $validated['status'] = $validated['status'] ?? 'Active';

        $app = Application::create($validated);

        return response()->json([
            'status' => 'success',
            'data'   => $app,
        ], 201);
    }

    public function show(Application $application)
    {
        $application->loadCount('downloads');

        return response()->json([
            'status' => 'success',
            'data'   => $application,
        ]);
    }

    public function update(Request $request, Application $application)
    {
        $validated = $request->validate([
            'application_name' => ['sometimes', 'string', 'max:255'],
            'version'          => ['sometimes', 'string', 'max:50'],
            'release_notes'    => ['nullable', 'string'],
            'status'           => ['sometimes', 'string', 'in:Active,Inactive,Archived'],
        ]);

        $application->update($validated);

        return response()->json([
            'status' => 'success',
            'data'   => $application,
        ]);
    }

    public function destroy(Application $application)
    {
        $application->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Application record deleted successfully.',
        ]);
    }
}
