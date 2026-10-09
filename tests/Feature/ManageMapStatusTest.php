<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\LocationMapping;
use App\Models\Soil;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManageMapStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_toggle_location_mapping_status_between_active_and_inactive(): void
    {
        $admin = Admin::create([
            'admin_id'  => 'ADM-MAP-001',
            'full_name' => 'Admin Tester',
            'email'     => 'admin_map@agrico.com',
            'password'  => bcrypt('password'),
        ]);

        $soil = Soil::create([
            'soil_name' => 'Test Clay Loam',
            'full_code' => '5YR 4/6',
            'soil_type' => 'Clay Loam',
            'hue'       => '5YR',
            'hex_color' => '#8F694E',
        ]);

        $user = \App\Models\User::create([
            'user_id'   => 'USR-TEST-001',
            'name'      => 'Test User',
            'email'     => 'testuser@agrico.com',
            'password'  => bcrypt('password'),
            'role'      => 'admin',
        ]);

        $scan = \App\Models\SoilScan::create([
            'user_id'   => $user->user_id,
            'soil_id'   => $soil->soil_id ?? $soil->id,
            'scan_date' => now(),
            'scan_note' => 'Test scan [Covered Area: 500 m²]',
        ]);

        $mapping = LocationMapping::create([
            'latitude'     => 18.2345,
            'longitude'    => 121.6789,
            'barangay'     => 'Jurisdiction',
            'soil_id'      => $soil->soil_id ?? $soil->id,
            'soil_scan_id' => $scan->soil_scan_id ?? $scan->id,
            'status'       => 'Active',
            'timestamp'    => now(),
        ]);

        // 1. Toggle from Active to Inactive
        $response = $this->actingAs($admin, 'admin')->postJson("/manage-map/{$mapping->id}/toggle-status");
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'status'  => 'Inactive',
        ]);

        $mapping->refresh();
        $this->assertEquals('Inactive', $mapping->status);

        // Verify Inactive locations are included in ManageMap component query
        $manageMapResponse = $this->actingAs($admin, 'admin')->get('/manage-map');
        $manageMapResponse->assertStatus(200);
        $manageMapResponse->assertSee('Inactive');
        $manageMapResponse->assertSee('agricoGrayIcon');
        $manageMapResponse->assertSee('agricoUpdateMarkerStatus');

        // 2. Toggle back from Inactive to Active
        $response2 = $this->actingAs($admin, 'admin')->postJson("/manage-map/{$mapping->id}/toggle-status");
        $response2->assertStatus(200);
        $response2->assertJson([
            'success' => true,
            'status'  => 'Active',
        ]);

        $mapping->refresh();
        $this->assertEquals('Active', $mapping->status);
    }
}
