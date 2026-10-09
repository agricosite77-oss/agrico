<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class TrainingControllerTest extends TestCase
{
    protected function createDummyBase64Image(): string
    {
        $img = imagecreatetruecolor(50, 50);
        $color = imagecolorallocate($img, 120, 80, 50);
        imagefill($img, 0, 0, $color);

        ob_start();
        imagejpeg($img, null, 80);
        $data = ob_get_clean();
        imagedestroy($img);

        return 'data:image/jpeg;base64,' . base64_encode($data);
    }

    public function test_training_page_renders_successfully()
    {
        $response = $this->get('/training');
        $response->assertStatus(200);
        $response->assertSee('Manual Training-Data Labeling');
    }

    public function test_save_training_image_as_fake_soil()
    {
        $base64 = $this->createDummyBase64Image();

        $response = $this->postJson('/training/save', [
            'image_base64' => $base64,
            'label'        => 'fake_soil',
        ]);

        $response->assertStatus(201);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('label', 'fake_soil');

        $storedPath = $response->json('stored_path');
        $this->assertNotEmpty($storedPath);
        $this->assertFileExists($storedPath);
        $this->assertStringContainsString('Fake_Soil', $storedPath);
        $expectedRoot = rtrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, config('soil.scan_soil_root')), DIRECTORY_SEPARATOR);
        $actualPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $storedPath);
        $this->assertStringStartsWith($expectedRoot, $actualPath);

        // Cleanup test artifact
        if (File::exists($storedPath)) {
            File::delete($storedPath);
            $json = pathinfo($storedPath, PATHINFO_DIRNAME) . '/' . pathinfo($storedPath, PATHINFO_FILENAME) . '.json';
            if (File::exists($json)) {
                File::delete($json);
            }
        }
    }

    public function test_save_training_image_as_real_soil()
    {
        $base64 = $this->createDummyBase64Image();

        $response = $this->postJson('/training/save', [
            'image_base64' => $base64,
            'label'        => 'real_soil',
            'hue'          => '10YR',
            'value'        => 4,
            'chroma'       => 3,
        ]);

        $response->assertStatus(201);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('label', 'real_soil');
        $response->assertJsonPath('notation', '10YR 4/3');

        $storedPath = $response->json('stored_path');
        $this->assertNotEmpty($storedPath);
        $this->assertFileExists($storedPath);
        $this->assertStringContainsString('10YR 4', $storedPath);
        $expectedRoot = rtrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, config('soil.scan_soil_root')), DIRECTORY_SEPARATOR);
        $actualPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $storedPath);
        $this->assertStringStartsWith($expectedRoot, $actualPath);

        // Cleanup test artifact
        if (File::exists($storedPath)) {
            File::delete($storedPath);
            $json = pathinfo($storedPath, PATHINFO_DIRNAME) . '/' . pathinfo($storedPath, PATHINFO_FILENAME) . '.json';
            if (File::exists($json)) {
                File::delete($json);
            }
        }
    }

    public function test_save_real_soil_without_munsell_parameters()
    {
        $base64 = $this->createDummyBase64Image();

        $response = $this->postJson('/training/save', [
            'image_base64' => $base64,
            'label'        => 'real_soil',
        ]);

        $response->assertStatus(201);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('label', 'real_soil');

        $storedPath = $response->json('stored_path');
        $this->assertNotEmpty($storedPath);
        $this->assertFileExists($storedPath);
        $this->assertStringContainsString('Real_Soil', $storedPath);

        // Verify stored directly in Real_Soil (not in subfolders)
        $parentDir = dirname($storedPath);
        $this->assertEquals('Real_Soil', basename($parentDir));

        // Cleanup test artifact
        if (File::exists($storedPath)) {
            File::delete($storedPath);
            $json = pathinfo($storedPath, PATHINFO_DIRNAME) . '/' . pathinfo($storedPath, PATHINFO_FILENAME) . '.json';
            if (File::exists($json)) {
                File::delete($json);
            }
        }
    }
}
