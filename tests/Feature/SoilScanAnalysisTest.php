<?php

namespace Tests\Feature;

use App\Models\MunsellColor;
use App\Models\Soil;
use App\Models\SoilScan;
use App\Models\User;
use App\Services\SoilMLAnalysisService;
use App\Services\SoilScanService;
use App\Services\SoilValidationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use Tests\TestCase;

class SoilScanAnalysisTest extends TestCase
{
    use RefreshDatabase;

    protected SoilScanService $soilScanService;

    protected function setUp(): void
    {
        parent::setUp();
        $validator = new SoilValidationService();
        $this->soilScanService = new SoilScanService($validator);

        // Seed reference Munsell Colors
        MunsellColor::create([
            'full_code'   => '10YR 4/3',
            'hue'         => '10YR',
            'value'       => 4,
            'chroma'      => 3,
            'color_name'  => 'Dark Brown',
            'description' => 'Moist organic topsoil with high fertility potential.',
            'status'      => 'Active',
        ]);

        MunsellColor::create([
            'full_code'   => '10R 3/4',
            'hue'         => '10R',
            'value'       => 3,
            'chroma'      => 4,
            'color_name'  => 'Dusky Red',
            'description' => 'Fine grain soil with reddish mineral tones.',
            'status'      => 'Active',
        ]);
    }

    /**
     * Create a synthetic GD soil sample image (natural dark brown earth tone with texture).
     */
    protected function createSyntheticSoilImage(): string
    {
        $img = imagecreatetruecolor(100, 100);
        for ($y = 0; $y < 100; $y++) {
            for ($x = 0; $x < 100; $x++) {
                // Natural dark brown RGB (R ~ 89, G ~ 68, B ~ 51) with realistic soil grain texture noise
                $noise = rand(-18, 18);
                $r = max(0, min(255, 89 + $noise + rand(-4, 4)));
                $g = max(0, min(255, 68 + rand(-10, 10)));
                $b = max(0, min(255, 51 + rand(-8, 8)));
                $color = imagecolorallocate($img, $r, $g, $b);
                imagesetpixel($img, $x, $y, $color);
            }
        }

        ob_start();
        imagejpeg($img, null, 95);
        $data = ob_get_clean();
        imagedestroy($img);

        return 'data:image/jpeg;base64,' . base64_encode($data);
    }

    /**
     * Create a synthetic invalid non-soil image (solid bright blue).
     */
    protected function createSyntheticBlueImage(): string
    {
        $img = imagecreatetruecolor(100, 100);
        $blue = imagecolorallocate($img, 0, 100, 255);
        imagefill($img, 0, 0, $blue);

        ob_start();
        imagejpeg($img, null, 90);
        $data = ob_get_clean();
        imagedestroy($img);

        return 'data:image/jpeg;base64,' . base64_encode($data);
    }

    public function test_soil_scan_service_analyzes_valid_soil_image()
    {
        $base64Image = $this->createSyntheticSoilImage();
        $result = $this->soilScanService->analyzeImage($base64Image);

        $this->assertTrue($result['success']);
        $this->assertNotEmpty($result['munsell_code']);
        $this->assertContains($result['hue'], ['10YR', '7.5YR', '5YR', '2.5YR', '10R']);
        $this->assertArrayHasKey('rgb', $result);
        $this->assertArrayHasKey('lab', $result);
        $this->assertGreaterThanOrEqual(80.0, $result['confidence']);
    }

    // ── SoilValidationService unit tests ────────────────────────────────────────
    // SoilValidationService is no longer called in the main scan pipeline
    // (Tier A–D removed; Stage 1 ML gate is the sole gatekeeper).
    // These tests keep the class itself verified in isolation as a reference/fallback.

    public function test_soil_validation_service_rejects_solid_blue_image()
    {
        $this->expectException(InvalidArgumentException::class);
        $base64BlueImage = $this->createSyntheticBlueImage();
        $validator = new SoilValidationService();
        $validator->validate($base64BlueImage);
    }

    public function test_soil_ml_service_analyzes_image_via_python_or_fallback()
    {
        $validator = new SoilValidationService();
        $mlService = new SoilMLAnalysisService($this->soilScanService, $validator);
        $base64Image = $this->createSyntheticSoilImage();

        $result = $mlService->analyzeImage($base64Image);

        $this->assertTrue($result['success']);
        if (!empty($result['async'])) {
            $this->assertNotEmpty($result['scan_id']);
        } else {
            $this->assertNotEmpty($result['munsell_code']);
            $this->assertArrayHasKey('confidence', $result);
        }
    }

    public function test_web_scan_analyze_endpoint_returns_success_and_persists_records()
    {
        Storage::fake('public');
        $user = User::create([
            'user_id'  => 'USR-' . rand(1000, 9999),
            'name'     => 'Test Farmer',
            'email'    => 'farmer' . rand(100, 999) . '@example.com',
            'password' => bcrypt('password'),
            'status'   => 'Active',
        ]);
        $this->actingAs($user);

        // Mock SoilMLAnalysisService to return a deterministic real_soil result.
        // This test validates the controller's HTTP + DB persistence path, not the Python classifier.
        // The Python pipeline is unavailable/untrained in the test environment and is tested
        // separately via test_soil_ml_service_analyzes_image_via_python_or_fallback().
        $mockMlService = $this->createMock(\App\Services\SoilMLAnalysisService::class);
        $mockMlService->method('analyzeImage')->willReturn([
            'success'              => true,
            'label'                => 'real_soil',
            'munsell_code'         => '10YR 4/3',
            'hue'                  => '10YR',
            'value'                => '4',
            'chroma'               => '3',
            'color_name'           => 'Dark Brown',
            'soil_name'            => 'Dark Brown (10YR 4/3)',
            'hex'                  => '#5C4033',
            'confidence'           => 90.0,
            'confidence_formatted' => '90.0%',
            'rgb'                  => ['r' => 89, 'g' => 68, 'b' => 51],
            'lab'                  => ['l' => 28.5, 'a' => 5.2, 'b' => 11.3],
            'description'          => 'Moist organic topsoil with high fertility potential.',
            'soil_type'            => 'Loam',
            'organic_matter'       => 'Moderate',
            'drainage_profile'     => 'Well-drained',
            'recommended_crops'    => 'Rice, Corn',
            'color_id'             => null,
            'regions'              => [],
            'engine'               => 'Mock (Test)',
            'color_spaces'         => [],
            'color_space_badges'   => [],
        ]);
        $this->app->instance(\App\Services\SoilMLAnalysisService::class, $mockMlService);

        $base64Image = $this->createSyntheticSoilImage();

        $response = $this->postJson(route('website.scan.analyze'), [
            'image_base64' => $base64Image,
            'latitude'     => 14.5995,
            'longitude'    => 120.9842,
        ]);

        $response->assertStatus(202);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('async', true);
        $this->assertNotEmpty($response->json('scan_id'));
    }

    public function test_web_scan_analyze_endpoint_returns_422_on_missing_image()
    {
        $user = User::create([
            'user_id'  => 'USR-' . rand(1000, 9999),
            'name'     => 'Test Farmer',
            'email'    => 'farmer' . rand(100, 999) . '@example.com',
            'password' => bcrypt('password'),
            'status'   => 'Active',
        ]);
        $this->actingAs($user);

        $response = $this->postJson(route('website.scan.analyze'), []);

        $response->assertStatus(422);
        $response->assertJsonPath('success', false);
        $response->assertJsonStructure(['message']);
    }

    /**
     * Create a synthetic human skin tone image (R=180, G=130, B=100).
     */
    protected function createSyntheticSkinImage(): string
    {
        $img = imagecreatetruecolor(100, 100);
        $skin = imagecolorallocate($img, 180, 130, 100);
        imagefill($img, 0, 0, $skin);

        ob_start();
        imagejpeg($img, null, 90);
        $data = ob_get_clean();
        imagedestroy($img);

        return 'data:image/jpeg;base64,' . base64_encode($data);
    }

    public function test_soil_validation_service_rejects_flat_skin_tone_image()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('No valid soil sample detected. Please align the viewfinder over a clear, well-lit soil sample and try again.');
        $base64SkinImage = $this->createSyntheticSkinImage();
        $validator = new SoilValidationService();
        $validator->validate($base64SkinImage);
    }

    /**
     * Create a realistic photographed skin image with natural lighting gradient and 3D contours (stdDev > 10, edgeDensity ~ 0.045).
     */
    protected function createContouredSkinImage(): string
    {
        $img = imagecreatetruecolor(100, 100);
        for ($y = 0; $y < 100; $y++) {
            for ($x = 0; $x < 100; $x++) {
                // Natural gradient from 3D contour (x, y) + subtle pore noise
                $gradient = (int)(($x + $y) * 0.25);
                $noise = rand(-2, 2);
                $r = max(0, min(255, 175 + $gradient + $noise));
                $g = max(0, min(255, 125 + (int)($gradient * 0.8) + $noise));
                $b = max(0, min(255, 95 + (int)($gradient * 0.6) + $noise));
                imagesetpixel($img, $x, $y, imagecolorallocate($img, $r, $g, $b));
            }
        }
        ob_start();
        imagejpeg($img, null, 90);
        $data = ob_get_clean();
        imagedestroy($img);
        return 'data:image/jpeg;base64,' . base64_encode($data);
    }

    public function test_soil_validation_service_rejects_contoured_skin_image()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('No valid soil sample detected. Please align the viewfinder over a clear, well-lit soil sample and try again.');
        $base64SkinImage = $this->createContouredSkinImage();
        $validator = new SoilValidationService();
        $validator->validate($base64SkinImage);
    }
}

