<?php

use App\Services\SoilMLAnalysisService;
use App\Services\SoilScanService;
use App\Services\SoilValidationService;

beforeEach(function () {
    app()->bind(SoilScanService::class, function () {
        return new SoilScanService(new SoilValidationService());
    });
});

it('falls back to unclassified when python result status is null', function () {
    $service = app(SoilMLAnalysisService::class);

    $pythonResult = [
        'label' => 'real_soil',
        'confidence' => 0.7,
        'status' => null,
        'regions' => [],
    ];

    $analysis = $service->buildAnalysis($pythonResult);

    expect($analysis['status'])->toBe('unclassified');
});

it('falls back to unclassified when status key is entirely absent', function () {
    $service = app(SoilMLAnalysisService::class);

    $pythonResult = [
        'label' => 'real_soil',
        'confidence' => 0.7,
        // no 'status' key at all, e.g. a pre-migration API response shape
        'regions' => [],
    ];

    $analysis = $service->buildAnalysis($pythonResult);

    expect($analysis['status'])->toBe('unclassified');
});

it('falls back to regions[0].status when top-level status missing', function () {
    $service = app(SoilMLAnalysisService::class);

    $pythonResult = [
        'label' => 'real_soil',
        'confidence' => 0.7,
        'regions' => [
            ['status' => 'verified'],
        ],
    ];

    $analysis = $service->buildAnalysis($pythonResult);

    expect($analysis['status'])->toBe('verified');
});

it('treats a non-string status as unclassified', function () {
    $service = app(SoilMLAnalysisService::class);

    $pythonResult = [
        'label' => 'real_soil',
        'confidence' => 0.7,
        'status' => 12345, // malformed/unexpected type
        'regions' => [],
    ];

    $analysis = $service->buildAnalysis($pythonResult);

    expect($analysis['status'])->toBe('unclassified');
});

it('throws AuthenticationException on mobile scan when userId is null or invalid', function () {
    $service = app(SoilMLAnalysisService::class);

    $pythonResult = [
        'label' => 'real_soil',
        'confidence' => 0.95,
        'regions' => [],
    ];

    $service->persistScanResult($pythonResult, 'test-scan-uuid-1', null, 'mobile');
})->throws(\Illuminate\Auth\AuthenticationException::class);

it('returns explicit rejection structure when python result is non-soil', function () {
    $service = app(SoilMLAnalysisService::class);

    $pythonResult = [
        'label' => 'fake_soil',
        'confidence' => 0.98,
        'regions' => [],
    ];

    $result = $service->persistScanResult($pythonResult, 'test-scan-uuid-2', 'ADM-001', 'website');

    expect($result['rejected'])->toBeTrue()
        ->and($result['success'])->toBeFalse()
        ->and($result['error'])->toBe('NON_SOIL_DETECTED')
        ->and($result['message'])->toContain('No valid soil sample detected');
});

