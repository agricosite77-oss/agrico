<?php

use App\Models\LocationMapping;
use App\Models\Soil;
use App\Models\SoilScan;
use App\Models\User;
use App\Models\UserRequest;

test('approve scan marks verified and makes pin active', function () {
    $user = User::first() ?? User::create([
        'name'     => 'Test Student',
        'email'    => 'test_mod_' . uniqid() . '@agrico.com',
        'password' => bcrypt('password'),
        'status'   => 'Active',
    ]);

    $soil = Soil::create([
        'soil_name' => 'Test Clay Loam ' . uniqid(),
        'status'    => 'Inactive',
    ]);

    $scan = SoilScan::create([
        'user_id'             => $user->user_id,
        'soil_id'             => $soil->soil_id,
        'scan_note'           => 'Test scan',
        'verification_status' => 'Pending',
    ]);

    $location = LocationMapping::create([
        'soil_scan_id' => $scan->soil_scan_id,
        'soil_id'      => $soil->soil_id,
        'latitude'     => 18.2000,
        'longitude'    => 121.6600,
        'barangay'     => 'Catugan',
        'status'       => 'Pending',
        'timestamp'    => now(),
    ]);

    $ticket = UserRequest::create([
        'request_code' => 'REQ-PIN-' . strtoupper(substr(uniqid(), 0, 6)),
        'user_name'    => $user->name,
        'user_email'   => $user->email,
        'type'         => 'Soil Coordinates',
        'description'  => "Location Pin #{$location->location_mapping_id} - Coords: (18.2000, 121.6600)",
        'status'       => 'Pending',
    ]);

    $approvedScan = SoilScan::approveScan($scan->soil_scan_id, 'ADM-001');

    expect($approvedScan)->not->toBeNull()
        ->and($approvedScan->fresh()->verification_status)->toBe('Verified')
        ->and($soil->fresh()->status)->toBe('Active')
        ->and($location->fresh()->status)->toBe('Active')
        ->and($ticket->fresh()->status)->toBe('Approved');
});

test('reject scan marks rejected and hides pin', function () {
    $user = User::first() ?? User::create([
        'name'     => 'Test Student',
        'email'    => 'test_mod_rej_' . uniqid() . '@agrico.com',
        'password' => bcrypt('password'),
        'status'   => 'Active',
    ]);

    $soil = Soil::create([
        'soil_name' => 'Test Sandy Soil ' . uniqid(),
        'status'    => 'Active',
    ]);

    $scan = SoilScan::create([
        'user_id'             => $user->user_id,
        'soil_id'             => $soil->soil_id,
        'scan_note'           => 'Test scan rejected',
        'verification_status' => 'Pending',
    ]);

    $location = LocationMapping::create([
        'soil_scan_id' => $scan->soil_scan_id,
        'soil_id'      => $soil->soil_id,
        'latitude'     => 18.2100,
        'longitude'    => 121.6700,
        'barangay'     => 'Bagumbayan',
        'status'       => 'Active',
        'timestamp'    => now(),
    ]);

    $ticket = UserRequest::create([
        'request_code' => 'REQ-PIN-' . strtoupper(substr(uniqid(), 0, 6)),
        'user_name'    => $user->name,
        'user_email'   => $user->email,
        'type'         => 'Soil Coordinates',
        'description'  => "Location Pin #{$location->location_mapping_id} - Coords: (18.2100, 121.6700)",
        'status'       => 'Pending',
    ]);

    $rejectedScan = SoilScan::rejectScan($scan->soil_scan_id, 'ADM-001');

    expect($rejectedScan)->not->toBeNull()
        ->and($rejectedScan->fresh()->verification_status)->toBe('Rejected')
        ->and($location->fresh()->status)->toBe('Inactive')
        ->and($ticket->fresh()->status)->toBe('Disapproved');
});
