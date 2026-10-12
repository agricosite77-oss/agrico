<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TrainingController extends Controller
{
    /**
     * Display the manual training-data labeling page.
     */
    public function index()
    {
        return view('website.training');
    }

    /**
     * Save a user-labeled training image directly into Scan_Soil_Image hierarchy.
     */
    public function save(Request $request)
    {
        $request->validate([
            'image_base64' => 'required|string',
            'label'        => 'required|in:real_soil,fake_soil',
            'hue'          => 'nullable|string',
            'value'        => 'nullable|numeric',
            'chroma'       => 'nullable|numeric',
        ]);

        $tempPath = $this->decodeBase64ToTempFile($request->input('image_base64'));
        if (!$tempPath || !file_exists($tempPath)) {
            return response()->json([
                'success' => false,
                'error'   => 'Failed to decode image payload.',
            ], 422);
        }

        $runner = new \App\Services\PythonProcessRunner();
        $scriptPath = base_path('python/add_training_image.py');
        $args = [
            escapeshellarg($tempPath),
            escapeshellarg($request->input('label')),
        ];

        if ($request->input('label') === 'real_soil' && $request->filled('hue') && $request->filled('value') && $request->filled('chroma')) {
            $args[] = escapeshellarg($request->input('hue'));
            $args[] = escapeshellarg((string)$request->input('value'));
            $args[] = escapeshellarg((string)$request->input('chroma'));
        }

        $result = $runner->run($scriptPath, $args);

        if (file_exists($tempPath)) {
            @unlink($tempPath);
        }

        if (!$result || !($result['success'] ?? false)) {
            Log::error('TrainingController::save failed', [
                'script' => $scriptPath,
                'args'   => $args,
                'result' => $result,
            ]);

            return response()->json([
                'success' => false,
                'error'   => $result['error'] ?? 'Unknown error saving training image.',
            ], 422);
        }

        // Refresh the Stage 1 reference cache in the background (incremental, non-blocking).
        try {
            $runner->runDetached(base_path('python/build_stage1_reference_cache.py'), [], 'stage1_cache');
        } catch (\Throwable $e) {
            Log::warning('Stage 1 cache refresh dispatch failed: ' . $e->getMessage());
        }

        return response()->json($result, 201);
    }

    /**
     * Decode a base64 image data-URI to a temporary file.
     */
    protected function decodeBase64ToTempFile(string $base64): ?string
    {
        $data = $base64;
        $ext = 'jpg';

        if (preg_match('/^data:image\/(\w+);base64,/', $base64, $m)) {
            $ext = ($m[1] === 'jpeg') ? 'jpg' : $m[1];
            $data = substr($base64, strpos($base64, ',') + 1);
        }

        $binary = base64_decode($data);
        if ($binary === false) {
            return null;
        }

        $tempPath = tempnam(sys_get_temp_dir(), 'train_') . '.' . $ext;
        file_put_contents($tempPath, $binary);

        return $tempPath;
    }
}
