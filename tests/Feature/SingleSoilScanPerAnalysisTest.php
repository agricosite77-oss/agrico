<?php

use App\Models\SoilScan;
use App\Models\Soil;
use App\Models\User;
use App\Models\MunsellColor;
use App\Services\SoilMLAnalysisService;
use App\Services\SoilScanService;
use App\Services\SoilValidationService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    app()->bind(SoilScanService::class, function () {
        return new SoilScanService(new SoilValidationService());
    });
});

it('creates exactly one SoilScan record when analyzing a multi-layer soil image with 6 colors', function () {
    $user = User::factory()->create(['user_id' => 'TEST-USR-01']);

    MunsellColor::create([
        'full_code' => '5YR 3/2',
        'hue' => '5YR',
        'color_name' => 'Dark Reddish Brown',
        'hex_code' => '#5C382C',
        'description' => 'Dark reddish brown clay loam rich in organic matter.',
    ]);
    MunsellColor::create([
        'full_code' => '7.5YR 4/4',
        'hue' => '7.5YR',
        'color_name' => 'Brown',
        'hex_code' => '#8C5E39',
        'description' => 'Brown subsoil with moderate permeability.',
    ]);

    $regions = [
        ['munsell' => '5YR 3/2', 'munsell_code' => '5YR 3/2', 'label' => 'Dark Reddish Brown', 'percent' => 35, 'hex' => '#5C382C', 'color_delta_e' => 1.2],
        ['munsell' => '7.5YR 4/4', 'munsell_code' => '7.5YR 4/4', 'label' => 'Brown', 'percent' => 25, 'hex' => '#8C5E39', 'color_delta_e' => 1.5],
        ['munsell' => '2.5YR 3/4', 'munsell_code' => '2.5YR 3/4', 'label' => 'Dark Reddish Brown', 'percent' => 15, 'hex' => '#6A2E20', 'color_delta_e' => 2.1],
        ['munsell' => '2.5Y 5/4', 'munsell_code' => '2.5Y 5/4', 'label' => 'Light Olive Brown', 'percent' => 10, 'hex' => '#8D7B48', 'color_delta_e' => 2.4],
        ['munsell' => '10YR 4/3', 'munsell_code' => '10YR 4/3', 'label' => 'Brown', 'percent' => 10, 'hex' => '#7D6246', 'color_delta_e' => 2.0],
        ['munsell' => '10YR 6/3', 'munsell_code' => '10YR 6/3', 'label' => 'Pale Brown', 'percent' => 5, 'hex' => '#A8947A', 'color_delta_e' => 2.8],
    ];

    $pythonResult = [
        'success' => true,
        'label' => 'real_soil',
        'status' => 'verified',
        'confidence' => 0.96,
        'munsell_code' => '5YR 3/2',
        'hue' => '5YR',
        'soil_name' => 'Dark Reddish Brown',
        'regions' => $regions,
    ];

    $service = app(SoilMLAnalysisService::class);
    $scanUuid = 'test-uuid-multi-scan-6';

    $result = $service->persistScanResult($pythonResult, $scanUuid, $user->user_id, 'website');

    // CRITICAL: Exactly 1 SoilScan must exist in the database!
    expect(SoilScan::count())->toBe(1);

    $savedScan = SoilScan::first();
    expect($savedScan->soil_scan_id)->toBe($result['scan_id'])
        ->and($savedScan->regions_data)->toHaveCount(6)
        ->and($result['created_scans'])->toHaveCount(6);

    // All layer cards in created_scans must reference the single scan_id
    foreach ($result['created_scans'] as $layerCard) {
        expect($layerCard['scan_id'])->toBe($savedScan->soil_scan_id);
    }
});
