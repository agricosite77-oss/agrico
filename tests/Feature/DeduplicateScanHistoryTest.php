<?php

use App\Models\SoilScan;
use App\Models\Soil;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;

uses(RefreshDatabase::class);

it('removes duplicate SoilScan rows that share identical regions_data and timestamp', function () {
    $user = User::factory()->create(['user_id' => 'DUP-USR-01']);
    $soil1 = Soil::create(['soil_name' => 'Dark Reddish Brown', 'hue' => '5YR', 'full_code' => '5YR 3/2']);
    $soil2 = Soil::create(['soil_name' => 'Brown', 'hue' => '7.5YR', 'full_code' => '7.5YR 4/4']);

    $sharedRegions = [
        ['munsell' => '5YR 3/2', 'percent' => 60],
        ['munsell' => '7.5YR 4/4', 'percent' => 40],
    ];

    $now = now();

    // Simulate the old duplicate creation (2 scans for 1 image)
    $scan1 = SoilScan::create([
        'user_id' => $user->user_id,
        'soil_id' => $soil2->soil_id,
        'scan_note' => 'Determined Soil: Brown (7.5YR 4/4)',
        'scan_date' => $now,
        'regions_data' => $sharedRegions,
        'created_at' => $now,
    ]);

    $scan2 = SoilScan::create([
        'user_id' => $user->user_id,
        'soil_id' => $soil1->soil_id,
        'scan_note' => 'Determined Soil: Dark Reddish Brown (5YR 3/2)',
        'scan_date' => $now,
        'regions_data' => $sharedRegions,
        'created_at' => $now,
    ]);

    expect(SoilScan::count())->toBe(2);

    Artisan::call('scan:deduplicate');

    // Only 1 scan remains, retaining the full regions_data
    expect(SoilScan::count())->toBe(1);
    $remaining = SoilScan::first();
    expect($remaining->regions_data)->toHaveCount(2);
});
