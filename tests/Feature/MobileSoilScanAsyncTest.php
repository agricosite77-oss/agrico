<?php

namespace Tests\Feature;

use App\Models\LocationMapping;
use App\Models\Soil;
use App\Models\SoilScan;
use App\Models\User;
use App\Services\SoilMLAnalysisService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MobileSoilScanAsyncTest extends TestCase
{
    use RefreshDatabase;

    protected function createSyntheticSoilImage(): string
    {
        $img = imagecreatetruecolor(100, 100);
        $brown = imagecolorallocate($img, 107, 73, 49);
        imagefill($img, 0, 0, $brown);
        ob_start();
        imagejpeg($img, null, 90);
        $data = ob_get_clean();
        imagedestroy($img);
        return 'data:image/jpeg;base64,' . base64_encode($data);
    }

    protected function createTestUser(): User
    {
        return User::create([
            'user_id'  => 'USR-' . rand(1000, 9999),
            'name'     => 'Test Mobile Farmer',
            'email'    => 'farmer' . rand(10000, 99999) . '@example.com',
            'password' => bcrypt('password'),
            'status'   => 'Active',
        ]);
    }

    public function test_analyze_async_requires_authentication()
    {
        $response = $this->postJson('/api/scan/analyze', [
            'image_base64' => $this->createSyntheticSoilImage(),
        ]);

        $response->assertStatus(401);
    }

    public function test_progress_requires_authentication()
    {
        $response = $this->getJson('/api/scan/progress/test-scan-uuid-1234');
        $response->assertStatus(401);
    }

    public function test_analyze_async_initiates_pipeline_and_returns_202()
    {
        Storage::fake('public');
        $user = $this->createTestUser();
        Sanctum::actingAs($user);

        $scanId = 'test-mobile-scan-' . uniqid();
        $response = $this->postJson('/api/scan/analyze', [
            'scan_id'       => $scanId,
            'image_base64'  => $this->createSyntheticSoilImage(),
            'latitude'      => 18.156,
            'longitude'     => 121.658,
            'location_name' => 'Centro',
        ]);

        $response->assertStatus(202);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('async', true);
        $response->assertJsonPath('scan_id', $scanId);

        // Verify sidecar metadata was created
        $metaFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . "scan_meta_{$scanId}.json";
        $this->assertFileExists($metaFile);

        $meta = json_decode(file_get_contents($metaFile), true);
        $this->assertEquals($user->user_id, $meta['user_id']);
        $this->assertEquals(18.156, $meta['latitude']);
        $this->assertEquals('Centro', $meta['location_name']);

        // Clean up
        @unlink($metaFile);
    }

    public function test_progress_handles_soft_rejection_without_missing_keys()
    {
        $user = $this->createTestUser();
        Sanctum::actingAs($user);

        $scanId = 'test-mobile-reject-' . uniqid();
        $tmpDir = sys_get_temp_dir();
        $progressFile = $tmpDir . DIRECTORY_SEPARATOR . "scan_progress_{$scanId}.json";
        $resultFile   = $tmpDir . DIRECTORY_SEPARATOR . "scan_result_{$scanId}.json";

        file_put_contents($progressFile, json_encode([
            ['stage' => 1, 'status' => 'done'],
            ['stage' => 6, 'status' => 'done'],
        ]));

        file_put_contents($resultFile, json_encode([
            'success'    => true,
            'label'      => 'fake_soil',
            'confidence' => 0.99,
        ]));

        $response = $this->getJson("/api/scan/progress/{$scanId}");

        $response->assertStatus(200);
        $response->assertJsonPath('status', 'rejected');
        $response->assertJsonPath('rejected', true);
        $this->assertNotEmpty($response->json('message'));
        $this->assertNull($response->json('scan'));
    }

    public function test_progress_completes_scan_and_creates_records_with_geolocation()
    {
        $user = $this->createTestUser();
        Sanctum::actingAs($user);

        $scanId = 'test-mobile-complete-' . uniqid();
        $tmpDir = sys_get_temp_dir();
        $progressFile = $tmpDir . DIRECTORY_SEPARATOR . "scan_progress_{$scanId}.json";
        $resultFile   = $tmpDir . DIRECTORY_SEPARATOR . "scan_result_{$scanId}.json";
        $metaFile     = $tmpDir . DIRECTORY_SEPARATOR . "scan_meta_{$scanId}.json";

        file_put_contents($progressFile, json_encode([
            ['stage' => 1, 'status' => 'done'],
            ['stage' => 6, 'status' => 'done'],
        ]));

        file_put_contents($metaFile, json_encode([
            'user_id'       => $user->user_id,
            'platform'      => 'mobile',
            'latitude'      => 18.156,
            'longitude'     => 121.658,
            'location_name' => 'Centro',
            'barangay'      => 'Centro',
        ]));

        file_put_contents($resultFile, json_encode([
            'success'    => true,
            'label'      => 'real_soil',
            'confidence' => 0.95,
            'munsell'    => ['notation' => '10YR 5/4', 'color_delta_e' => 1.2],
            'rgb'        => [125, 95, 60],
            'regions'    => [
                [
                    'munsell'       => '10YR 5/4',
                    'label'         => 'Yellowish Brown',
                    'proportion'    => 1.0,
                    'percent'       => 100,
                    'rgb'           => [125, 95, 60],
                    'status'        => 'verified',
                    'color_delta_e' => 1.2,
                ]
            ]
        ]));

        $response = $this->getJson("/api/scan/progress/{$scanId}");

        $response->assertStatus(200);
        $response->assertJsonPath('status', 'done');
        $this->assertNotEmpty($response->json('scan'));
        $this->assertEquals('10YR 5/4', $response->json('scan.full_code'));
        $this->assertEquals($user->user_id, $response->json('scan.user_id'));

        // Verify LocationMapping was created
        $scanRecord = SoilScan::where('user_id', $user->user_id)->latest('soil_scan_id')->first();
        $this->assertNotNull($scanRecord);
        $location = LocationMapping::where('soil_scan_id', $scanRecord->soil_scan_id)->first();
        $this->assertNotNull($location);
        $this->assertEquals(18.156, (float)$location->latitude);
        $this->assertEquals('Centro', $location->barangay);

        // Verify temporary files cleaned up
        $this->assertFileDoesNotExist($progressFile);
        $this->assertFileDoesNotExist($resultFile);
        $this->assertFileDoesNotExist($metaFile);
    }
}
